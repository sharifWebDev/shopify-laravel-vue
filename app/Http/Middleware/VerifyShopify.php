<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Inertia\Inertia;
use App\ShopifyApp\Util;
use Illuminate\Support\Str;
use App\Models\ShopifyStore;
use App\Traits\RequestTrait;
use Illuminate\Http\Request;
use App\Traits\FunctionTrait;
use Illuminate\Http\Response;
use Illuminate\Auth\AuthManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Assert\AssertionFailedException;
use Illuminate\Http\RedirectResponse;
use App\ShopifyApp\Contracts\ShopModel;
use Illuminate\Support\Facades\Redirect;
use App\ShopifyApp\Exceptions\HttpException;
use App\ShopifyApp\Objects\Enums\DataSource;
use App\ShopifyApp\Objects\Values\AccessToken;
use App\ShopifyApp\Objects\Values\SessionToken;
use App\ShopifyApp\Objects\Values\SessionContext;
use App\ShopifyApp\Objects\Values\NullableSessionId;
use App\ShopifyApp\Exceptions\SignatureVerificationException;
use App\ShopifyApp\Contracts\Objects\Values\ShopDomain as ShopDomainValue;


class VerifyShopify
{
    use FunctionTrait, RequestTrait;
    /**
     * The auth manager.
     *
     * @var AuthManager
     */
    protected $auth;

    /**
     * Previous request shop.
     *
     * @var ShopModel|null
     */
    protected $previousShop;

    /**
     * Constructor.
     *
     * @param AuthManager $auth      The Laravel auth manager.
     *
     * @return void
     */
    public function __construct(
        AuthManager $auth,
    ) {
        $this->auth = $auth;
    }

    /**
     * Undocumented function.
     *
     * @param Request $request The request object.
     * @param Closure $next    The next action.
     *
     * @throws SignatureVerificationException If HMAC verification fails.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
     
        if ($request->has('hmac')) {
            $validRequest = $this->validateRequestFromShopify($request->all());
            if ($validRequest) {
                //Check if shop parameter exists on the request.
                $shop = $request->has('shop');

                if ($shop) {
                    $storeDetails = $this->getStoreByDomain($request->shop);
                    
                    if ($storeDetails !== null && $storeDetails !== false) {
                        $validAccessToken = $this->checkIfAccessTokenIsValid($storeDetails);
                        if ($validAccessToken) {
                            $target = $this->tokenRedirect($request);
 
                                info('touch--------- VerifyShopify middleware handle method checkIfAccessTokenIsValid target , 
                                host, shopDomain --',
                                [
                                    'host' => $request->get('host'),
                                    'shopDomain' => $request->get('shop'),
                                    'target' => $target,
                                ]);

                            return Inertia::render(
                                'Auth/Token',
                                [
                                    'host' => $request->get('host'),
                                    'shopDomain' => $request->get('shop'),
                                    'target' => $target,
                                ]
                            );
                        } else {
                            $endpoint = 'https://' . $request->shop .
                            '/admin/oauth/authorize?client_id=' . config('shopify.shopify_api_key') .
                            '&scope=' .  config('shopify.api_scopes') .
                            '&redirect_uri=' . route('app_install_redirect');
                            info('touch--------- VerifyShopify middleware handle method checkIfAccessTokenIsValid else endpoint--', 
                       [ 'endpoint' => $endpoint] );
                            return Redirect::to($endpoint);
                        }
                    }
                } else {
                    throw new HttpException('Shop is not installed or missing data.', Response::HTTP_FORBIDDEN);
                }
            }
        }
         
        $tokenSource = $this->getAccessTokenFromRequest($request);
        info('touch--------- VerifyShopify middleware handle method tokenSource--', ['tokenSource' => $tokenSource]);

        if ($tokenSource === null) {
            dd('Shop is not installed or missing data--.', Response::HTTP_FORBIDDEN);
            //throw new HttpException('Shop is not installed or missing data.', Response::HTTP_FORBIDDEN);
        }

        // try { 
        //     // Try and process the token
        //     $token = SessionToken::fromNative($tokenSource);
        //     info('touch--------- VerifyShopify middleware handle method SessionToken::fromNative($tokenSource)--', ['token' => SessionToken::fromNative($tokenSource)]);
        // } catch (AssertionFailedException $e) {
        //     // Invalid or expired token, we need a new one
        //      $target = $this->tokenRedirect($request);
        //     return Inertia::render(
        //         'Auth/Token',
        //         [
        //             'host' => $request->get('host'),
        //             'shopDomain' => $request->get('shop'),
        //             'target' => $target,
        //         ]
        //     );
        // }
 
        // // Login the shop
        // $loginResult = $this->loginShopFromToken(
        //     $token,
        //     NullableSessionId::fromNative($request->query('session'))
        // );

        // if (!$loginResult) {
        //     // Shop is not installed or something is missing from it's data
        //     throw new HttpException('Shop is not installed or missing data.', Response::HTTP_FORBIDDEN);
        // }
        info('Request URL: ' . $request->fullUrl());
       
        return $next($request);
    }


    /**
     * Login and verify the shop and it's data.
     *
     * @param SessionToken      $token     The session token.
     * @param NullableSessionId $sessionId Incoming session ID (if available).
     *
     * @return bool
     */
    protected function loginShopFromToken(SessionToken $token, NullableSessionId $sessionId): bool
    {
        info('loginShopFromToken');
        // Get the shop domain from the token
        $shopDomainValue = $token->getShopDomain()->getValue()->domain;
     
        info('touch--------- VerifyShopify middleware handle method loginShopFromToken $shopDomainValue--',
        [$shopDomainValue]);

        // Now you can use $shopDomainString in your database query
        $shop = ShopifyStore::where('myshopify_domain', $shopDomainValue)->first();
        $user = User::where('myshopify_domain', $shopDomainValue)->first();

        // has the shop
        if (! $shop) {
            return false;
        }

        // Set the session details for the token, session ID, and access token
        $context = new SessionContext($token, $sessionId, AccessToken::fromNative($shop->access_token));

        info('touch--------- VerifyShopify middleware handle method loginShopFromToken $context--',
        [$context]);

        $user->setSessionContext($context);

        $previousContext = $this->previousShop?->getSessionContext();
        if (! $user->getSessionContext()->isValid($previousContext)) {
            // Something is invalid
            return false;
        }

        // All is well, login the shop
        $this->auth->login($user);

        return true;
    }

    /**
     * Redirect to token route.
     *
     * @param Request $request The request object.
     *
     * @return string
     */
    protected function tokenRedirect(Request $request): string
    {
        info([$request->all()]);
        // At this point the HMAC and other details are verified already, filter it out
        $path = $request->path();
        $target = Str::start($path, '/');

        if ($request->query()) {
            $filteredQuery = Collection::make($request->query())->except([
                'hmac',
                'locale',
                'new_design_language',
                'timestamp',
                'session',
                'shop',
            ]);
            
            info('touch--------- VerifyShopify middleware handle method tokenRedirect filteredQuery--',
           ['filteredQuery' => $filteredQuery] );

           
            if ($filteredQuery->isNotEmpty()) {
                info('touch--------- VerifyShopify middleware handle method tokenRedirect filteredQuery http_build_query--',
                [$target .= '?' . http_build_query($filteredQuery->toArray())] );

                
                $target .= '?' . http_build_query($filteredQuery->toArray());
            }
        }

        info('touch--------- VerifyShopify middleware handle method tokenRedirect filteredQuery--',
        ['target' => $target] );
        return $target;
    }

    /**
     * Redirect to install route.
     *
     * @param ShopDomainValue $shopDomain The shop domain.
     *
     * @return RedirectResponse
     */
    protected function installRedirect(ShopDomainValue $shopDomain): RedirectResponse
    {
        info('installRedirect');
        return Redirect::route(
            Util::getShopifyConfig('route_names.authenticate'),
            ['shop' => $shopDomain->toNative()]
        );
    }

    /**
     * Grab the HMAC value, if present, and how it was found.
     * Order of precedence is:.
     *
     *  - GET/POST Variable
     *  - Headers
     *  - Referer
     *
     * @param Request $request The request object.
     *
     * @return array
     */
    protected function getHmacFromRequest(Request $request): array
    {
        info('getHmacFromRequest');
        // All possible methods
        $options = [
            // GET/POST
            DataSource::INPUT()->toNative() => $request->input('hmac'),
            // Headers
            DataSource::HEADER()->toNative() => $request->header('X-Shop-Signature'),
            // Headers: Referer
            DataSource::REFERER()->toNative() => function () use ($request): ?string {
                $url = parse_url($request->header('referer', ''), PHP_URL_QUERY);
                parse_str($url ?? '', $refererQueryParams);
                if (! $refererQueryParams || ! isset($refererQueryParams['hmac'])) {
                    return null;
                }

                return $refererQueryParams['hmac'];
            },
        ];

        // Loop through each until we find the HMAC
        foreach ($options as $method => $value) {
            $result = is_callable($value) ? $value() : $value;
            if ($result !== null) {
                return ['source' => $method, 'value' => $value];
            }
        }

        return ['source' => null, 'value' => null];
    }

    /**
     * Get the token from request (if available).
     *
     * @param Request $request The request object.
     *
     * @return string
     */
    protected function getAccessTokenFromRequest(Request $request): ?string
    {
        //dd([$request->all()]);
      
        if (config('shopify.turbo_enabled')) {
 
            if ($request->bearerToken()) { 
                $bearerTokens = Collection::make(explode(',', $request->header('Authorization', '')));
                $newestToken = Str::substr(trim($bearerTokens->last()), 7); 
                return $newestToken;
            }
            
            info('touch--------- VerifyShopify middleware handle method getAccessTokenFromRequest $request->get(token)---',
             [$request->get('token')]);

            return $request->get('token');
        }

        return $this->isApiRequest($request) ? $request->bearerToken() : $request->get('token');
    }

    /**
     * Grab the request data.
     *
     * @param Request $request The request object.
     * @param string  $source  The source of the data.
     *
     * @return array
     */
    protected function getRequestData(Request $request, string $source): array
    {
        info('getRequestData');
        // All possible methods
        $options = [
            // GET/POST
            DataSource::INPUT()->toNative() => function () use ($request): array {
                // Verify
                $verify = [];
                foreach ($request->query() as $key => $value) {
                    $verify[$key] = $this->parseDataSourceValue($value);
                }

                return $verify;
            },
            // Headers
            DataSource::HEADER()->toNative() => function () use ($request): array {
                // Always present
                $shop = $request->header('X-Shop-Domain');
                $signature = $request->header('X-Shop-Signature');
                $timestamp = $request->header('X-Shop-Time');

                $verify = [
                    'shop' => $shop,
                    'hmac' => $signature,
                    'timestamp' => $timestamp,
                ];

                // Sometimes present
                $code = $request->header('X-Shop-Code') ?? null;
                $locale = $request->header('X-Shop-Locale') ?? null;
                $state = $request->header('X-Shop-State') ?? null;
                $id = $request->header('X-Shop-ID') ?? null;
                $ids = $request->header('X-Shop-IDs') ?? null;

                foreach (compact('code', 'locale', 'state', 'id', 'ids') as $key => $value) {
                    if ($value) {
                        $verify[$key] = $this->parseDataSourceValue($value);
                    }
                }

                return $verify;
            },
            // Headers: Referer
            DataSource::REFERER()->toNative() => function () use ($request): array {
                $url = parse_url($request->header('referer'), PHP_URL_QUERY);
                parse_str($url, $refererQueryParams);

                // Verify
                $verify = [];
                foreach ($refererQueryParams as $key => $value) {
                    $verify[$key] = $this->parseDataSourceValue($value);
                }

                return $verify;
            },
        ];

        return $options[$source]();
    }

    /**
     * Parse the data source value.
     * Handle simple key/values, arrays, and nested arrays.
     *
     * @param mixed $value
     *
     * @return string
     */
    protected function parseDataSourceValue($value): string
    {
        info('parseDataSourceValue');
        /**
         * Format the value.
         *
         * @param mixed $val
         *
         * @return string
         */
        $formatValue = function ($val): string {
            return is_array($val) ? '["' . implode('", "', $val) . '"]' : $val;
        };

        // Nested array
        if (is_array($value) && is_array(current($value))) {
            return implode(', ', array_map($formatValue, $value));
        }

        // Array or basic value
        return $formatValue($value);
    }

    /**
     * Determine if the request is AJAX or expects JSON.
     *
     * @param Request $request The request object.
     *
     * @return bool
     */
    protected function isApiRequest(Request $request): bool
    {
        return $request->ajax() || $request->expectsJson();
    }


    private function checkIfAccessTokenIsValid($storeDetails)
    {
        info('touch--------- VerifyShopify middleware handle method checkIfAccessTokenIsValid--');
        
        try {
            if ($storeDetails !== null && isset($storeDetails->access_token) && strlen($storeDetails->access_token) > 0) {
                $token = $storeDetails->access_token;
                $endpoint = getShopifyURLForStore('shop.json', $storeDetails);
                $headers = getShopifyHeadersForStore($storeDetails);

                // dd( $endpoint, $headers);
                $response = $this->makeAnAPICallToShopify('GET', $endpoint, null, $headers, null);
                return $response['statusCode'] === 200;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
