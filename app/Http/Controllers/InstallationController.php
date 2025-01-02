<?php

namespace App\Http\Controllers;

use Exception;
use Throwable;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Store;
use Illuminate\Support\Str;
use App\Models\ShopifyStore;
use App\Traits\RequestTrait;
use Illuminate\Http\Request;
use App\Traits\FunctionTrait;
use App\Jobs\ConfigureWebhooks;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class InstallationController extends Controller
{
    use FunctionTrait, RequestTrait;

    /**
     * @var mixed
     */
    private $api_scopes, $api_key, $api_secret;

    public function __construct()
    {
        $this->api_scopes = implode(',', config('shopify.api_scopes'));
        $this->api_key    = config('shopify.shopify_api_key');
        $this->api_secret = config('shopify.shopify_api_secret');
    }

    /**
     * @param Request $request
     */
    public function startInstallation(Request $request)
    {
        try {
            $validRequest = $this->validateRequestFromShopify($request->all());
            if (!$validRequest) {
                throw new Exception('Request is not valid!');
            }

            $shop = $request->has('shop'); //Check if shop parameter exists on the request.
            if (!$shop) {
                throw new Exception('Shop parameter not present in the request');
            }

            $storeDetails = $this->getStoreByDomain($request->shop);
            if (!$storeDetails) {
                $endpoint = 'https://' . $request->shop .
                    '/admin/oauth/authorize?client_id=' . $this->api_key .
                    '&scope=' . $this->api_scopes .
                    '&redirect_uri=' . route('app_install_redirect');
                return Redirect::to($endpoint);
            }

            //store record exists and now determine whether the access token is valid or not
            //if not then forward them to the re-installation flow
            //if yes then redirect them to the login page.

            $validAccessToken = $this->checkIfAccessTokenIsValid($storeDetails);

            if (!$validAccessToken) {
                $endpoint = 'https://' . $request->shop .
                    '/admin/oauth/authorize?client_id=' . $this->api_key .
                    '&scope=' . $this->api_scopes .
                    '&redirect_uri=' . route('app_install_redirect');
                return Redirect::to($endpoint);
            }

            $is_embedded = determineIfAppIsEmbedded();
            if (!$is_embedded) {
                return Redirect::route('login');
            }

            $user = User::where('shopify_store_id', $storeDetails->store_id)->first();
            Auth::login($user);
            $target = "/dashboard?host=" . $request->get('host') . "&shop=" . $request->get('shop');

            return Inertia::render(
                'Auth/Token',
                [
                    'host'       => $request->get('host'),
                    'shopDomain' => $request->get('shop'),
                    'target'     => $target,
                ]
            );
        } catch (Exception $e) {
            dd($e->getMessage(), $e->getLine(), $e->getFile());
        }
    }

    /**
     * @param Request $request
     */
    public function handleRedirect(Request $request)
    { 
        info('Request received for handleRedirect');
        try {
            $validRequest = $this->validateRequestFromShopify($request->all());
            if (!$validRequest) {
                throw new Exception('Request is not valid!');
            }

            $is_embedded = determineIfAppIsEmbedded();
            if ($request->has('session') && $is_embedded) {
                info('Session param present in the URL');
                $storeDetails = $this->getStoreByDomain($request->shop);
                $user         = User::where('shopify_store_id', $storeDetails->store_id)->first();
                Auth::login($user);
                $target = "/dashboard?host=" . $request->get('host') . "&shop=" . $request->get('shop');
                info('loading token page', ['target' => $target]);
                return Inertia::render(
                    'Auth/Token',
                    [
                        'host'       => $request->get('host'),
                        'shopDomain' => $request->get('shop'),
                        'target'     => $target,
                    ]
                );
            }

            if (!$request->has('shop') || !$request->has('code')) {
                throw new Exception('Code / Shop param not present in the URL');
            }

            $shop         = $request->get('shop');
            $code         = $request->get('code');
            info('Shop and Code received from the request', ['shop' => $shop, 'code' => $code]);
            $accessToken = $this->requestAccessTokenFromShopifyForThisStore($shop, $code);

            if (!$accessToken) {
                throw new Exception('Invalid Access Token ' . $accessToken);
            }

            info('Access Token received from Shopify', ['access_token' => $accessToken]);

            $shopDetails  = $this->getShopDetailsFromShopify($shop, $accessToken);
            $storeDetails = $this->saveStoreDetailsToDatabase($shopDetails, $accessToken);
            if (!$storeDetails) {
                Log::info('Problem during saving shop details into the db');
                Log::info($storeDetails);
                dd('Problem during installation. please check logs.');
            }

            //At this point the installation process is complete.
            if (!$is_embedded) {
                return Redirect::route('login');
            }

            $user = User::where('shopify_store_id', $storeDetails->store_id)->first();
            Auth::login($user);
            $target = "/dashboard?host=" . $request->get('host') . "&shop=" . $request->get('shop');
            info('loading token page after installation');
            return Inertia::render(
                'Auth/Token',
                [
                    'host'       => $request->get('host'),
                    'shopDomain' => $shop,
                    'target'     => $target,
                ]
            );
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' ' . $e->getLine());
            dd($e->getMessage(), $e->getLine(), $e->getFile());
        }
    }

    /**
     * @param  $shopDetails
     * @param  $accessToken
     * @return mixed
     */
    public function saveStoreDetailsToDatabase($shopDetails, $accessToken)
    {
        try {
            $payload = [
                'access_token'     => $accessToken,
                'myshopify_domain' => $shopDetails['myshopify_domain'],
                'store_id'         => $shopDetails['id'],
                'email'            => $shopDetails['email'],
                'name'             => $shopDetails['name'],
                'phone'            => $shopDetails['phone'],
                'address1'         => $shopDetails['address1'],
                'address2'         => $shopDetails['address2'],
                'zip'              => $shopDetails['zip'],
            ];
            $store           = ShopifyStore::updateOrCreate(['myshopify_domain' => $shopDetails['myshopify_domain']], $payload);
            $random_password = Str::random(12);
            Log::info('Password generated ' . $random_password);
            $user_payload = [
                'email'             => $shopDetails['email'],
                'myshopify_domain'  => $shopDetails['myshopify_domain'],
                'password'          => bcrypt($random_password),
                'shopify_store_id'  => $store->store_id,
                'name'            => $shopDetails['name'],
                'email_verified_at' => date('Y-m-d h:i:s'),
                'status'            => true,
                'is_admin'         => true,

            ];
            $user = User::updateOrCreate(['email' => $shopDetails['email'], 'myshopify_domain' => $shopDetails['myshopify_domain']], $user_payload);
            $user->markEmailAsVerified(); //To mark this user verified without requiring them to.
            ConfigureWebhooks::dispatchSync($store->store_id);
            // Product::dispatch( $user, $store );

            //            $this->registerForFulfillmentService($store);
            Session::flash('success', 'Installation for your store ' . $shopDetails['name'] . ' has completed and the credentials have been sent to ' . $shopDetails['email'] . '. Please login.');
            return $store;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' ' . $e->getLine());
            return false;
        }
    }

    /**
     * @param $store
     */
    public function registerForFulfillmentService($store)
    {
        try {
            $endpoint = getShopifyURLForStore('fulfillment_services.json', $store->toArray());
            $headers  = getShopifyHeadersForStore($store->toArray());

            // First, check if the service already exists
            $servicesResponse = $this->makeAnAPICallToShopify('GET', $endpoint, null, $headers);

            $serviceExists = false;
            $serviceName   = config('shopify.fulfillment_service_name');

            if (!empty($servicesResponse['body']['fulfillment_services'])) {
                foreach ($servicesResponse['body']['fulfillment_services'] as $service) {
                    // Using case-insensitive comparison and trimming spaces
                    if (strtolower(trim($service['name'])) === strtolower(trim($serviceName))) {
                        $serviceExists     = true;
                        $formattedResponse = [
                            "statusCode" => 200,
                            "body"       => [
                                "fulfillment_service" => $service,
                            ],
                        ];
                        $store->update(['fulfillment_service_response' => json_encode($formattedResponse), 'fulfillment_service' => true, 'fulfillment_orders_opt_in' => true]);
                        Log::info('store update');
                        break;
                    }
                }
            }

            // If the service doesn't exist, create it
            if (!$serviceExists) {
                $body = [
                    "fulfillment_service" => [
                        "name"                      => $serviceName,
                        "callback_url"              => route('service_callback'),
                        "inventory_management"      => true,
                        "tracking_support"          => true,
                        "fulfillment_orders_opt_in" => true,
                        "requires_shipping_method"  => true,
                        "format"                    => "json",
                    ],
                ];

                $response = $this->makeAnAPICallToShopify('POST', $endpoint, null, $headers, $body);

                if (isset($response['statusCode']) && $response['statusCode'] == 201) {
                    $store->update(['fulfillment_service' => true, 'fulfillment_orders_opt_in' => true]);
                } else {
                    // Log error details if the creation fails
                    Log::error('Failed to create fulfillment service.', ['response' => $response]);
                }

                $store->update(['fulfillment_service_response' => json_encode($response)]);
            }
        } catch (Exception $e) {
            Log::error('FS ' . $e->getMessage() . ' ' . $e->getLine());
        } catch (Throwable $e) {
            Log::error('FS ' . $e->getMessage() . ' ' . $e->getLine());
        }
    }

    /**
     * @param  $shop
     * @param  $accessToken
     * @return mixed
     */
    private function getShopDetailsFromShopify($shop, $accessToken)
    {
        try {
            $endpoint = getShopifyURLForStore('shop.json', ['myshopify_domain' => $shop]);
            $headers  = getShopifyHeadersForStore(['access_token' => $accessToken]);
            $response = $this->makeAnAPICallToShopify('GET', $endpoint, null, $headers);
            if ($response['statusCode'] !== 200) {

                Log::info('Response received for shop details', [$response]);
                return null;
            }

            $body = $response['body'];
            Log::info('getShopDetailsFromShopify', [$body]);
            if (!is_array($body)) {
                $body = json_decode($body, true);
            }

            return $body['shop'] ?? null;
        } catch (Exception $e) {
            Log::info('Problem getting the shop details from shopify');
            Log::info($e->getMessage() . ' ' . $e->getLine());
            return null;
        }
    }

    /**
     * @param  $shop
     * @param  $code
     * @return mixed
     */
    private function requestAccessTokenFromShopifyForThisStore($shop, $code)
    {
        try {
            $endpoint    = 'https://' . $shop . '/admin/oauth/access_token';
            $headers     = ['Content-Type: application/json'];
            $requestBody = json_encode([
                'client_id'     => $this->api_key,
                'client_secret' => $this->api_secret,
                'code'          => $code,
            ]);
            $response = $this->makeAPOSTCallToShopify($requestBody, $endpoint, $headers);
            info('Response from shopify for access token', ['makeAPOSTCallToShopify response' => $response, 'request' => $requestBody]);
            if ($response['statusCode'] == 200) {
                $body = $response['body'];
                if (!is_array($body)) {
                    $body = json_decode($body, true);
                }

                if (is_array($body) && isset($body['access_token']) && $body['access_token'] !== null) {
                    return $body['access_token'];
                }
            }
            return false;
        } catch (Exception $e) {
            info('Problem getting the access token from shopify', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * @param  $storeDetails
     * @return mixed
     */
    private function checkIfAccessTokenIsValid($storeDetails)
    {
        try {
            if ($storeDetails !== null && isset($storeDetails->access_token) && strlen($storeDetails->access_token) > 0) {
                $token    = $storeDetails->access_token;
                $endpoint = getShopifyURLForStore('shop.json', $storeDetails);
                $headers  = getShopifyHeadersForStore($storeDetails);
                $response = $this->makeAnAPICallToShopify('GET', $endpoint, null, $headers, null);
                return $response['statusCode'] === 200;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param Request $request
     */
    public function serviceCallback(Request $request)
    {
        Log::info('Response From service callback', [$request->all()]);
    }
}
