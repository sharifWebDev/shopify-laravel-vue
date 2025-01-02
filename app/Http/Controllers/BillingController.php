<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\Subscription;
use App\Traits\RequestTrait;
use Illuminate\Http\Request;
use App\Traits\FunctionTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class BillingController extends Controller
{
    use FunctionTrait, RequestTrait;
    //

    /**
     * @param Request $request
     */
    public function buySubscription(Request $request)
    {

        // dd($request->shopifyUrl );

        try {
            $user  = Auth::user();
            $store = $user->getShopifyStore;
            $plan  = Subscription::where('id', $request->subscription)->first();
            info('Plan >>>>', ['store' => $store, 'plan' => $plan]);
            $headers  = getShopifyHeadersForStore($store);
            $endpoint = getShopifyURLForStore('graphql.json', $store);

            info( 'Pay Headers',[$headers] );
            info( 'Pay endpoint',[$endpoint] );

            if ($plan) {
                $price     = (float) $plan->amount;
                $trialDays = $plan->triad_day;
                $name      = config('app.name') . ' Charge for Plan ';
                $shopSlug = explode('.', $store->myshopify_domain)[0];
                $appName = config('shopify.app_name');
                $returnUrl = "https://admin.shopify.com/store/$shopSlug/apps/$appName";

                //
                // 'host'       => $request->get('host'),
                // 'shopDomain' => $request->get('shop'),

                info('returnUrl >>>>'. $returnUrl);

                $variables = [
                    'name'      => $name,
                    'returnUrl' => $returnUrl,
                    'trialDays' => $trialDays,
                    'test'      => config('shopify.subscription_is_test'), // set this depending on whether you're in a test environment
                    'lineItems' => [
                        [
                            'plan' => [
                                'appRecurringPricingDetails' => [
                                    'price'    => ['amount' => $price, 'currencyCode' => 'USD'],
                                    //                                'interval' => 'ANNUAL',
                                    'interval' => 'EVERY_30_DAYS',
                                ],
                            ],
                        ],
                    ],
                ];
                $mutation = <<<'GRAPHQL'
                mutation appSubscriptionCreate($name: String!, $returnUrl: URL!, $trialDays: Int!, $test: Boolean!, $lineItems: [AppSubscriptionLineItemInput!]!) {
                  appSubscriptionCreate(name: $name, returnUrl: $returnUrl, trialDays: $trialDays, test: $test, lineItems: $lineItems) {
                    appSubscription {
                      id
                    }
                    confirmationUrl
                    userErrors {
                      field
                      message
                    }
                  }
                }
                GRAPHQL;
            }

            $payload = [
                'query'     => $mutation,
                'variables' => $variables,
            ];

            $response = $this->makeAnAPICallToShopify('POST', $endpoint, null, $headers, $payload);

            info('Pay Response >>>>', [$response]);
            // dd($response);

            if ($response['statusCode'] === 200) {
                if (isset($response['body']['data'])) {
                    if (isset($response['body']['data']['appSubscriptionCreate'])) {
                        $body = $response['body']['data']['appSubscriptionCreate'];
                        if (isset($body['userErrors']) && !empty($body['userErrors'])) {
                            return back()->with(
                                [
                                    'flash' => [
                                        'status' => false,
                                        'errors' => $body['userErrors'],
                                    ],
                                ]
                            );
                        }

                        return response()->json(['confirmationUrl' => $body['confirmationUrl']]);
                    }
                }
                return back()->with('error', 'Some problem occurred. Please try again in some time.');
            }


            return back()->with('error', 'Some problem occurred. Please try again in some time.');
        } catch (Exception $e) {
            logger()->error($e->getMessage(), ['line' => $e->getLine(), 'file' => $e->getFile()]);
            return back()->with('error', 'Some problem occurred. Please try again in some time.');
        }
    }

    // public function acceptSubscriptionCharge(Request $request)
    // {
    //     try {
    //         $user = Auth::user();
    //         $store = $user->getShopifyStore;
    //         $headers = getShopifyHeadersForStore($store);

    //         //            //this endpoint for Rest api
    //         //            $endpoint = getShopifyURLForStore('recurring_application_charges/'.$request['charge_id'].'.json', $store);
    //         //            $response = $this->makeAnAPICallToShopify('GET', $endpoint, null, $headers);

    //         if (isset($request['type'])) {

    //             if ($request['type'] == 'subscription') {
    //                 $request = $request->only(['charge_id', 'plan_id', 'type']);
    //                 $plan_id = $request['plan_id'];
    //                 $plan = 1;
    //                 // $plan = Plan::where('id', $plan_id)->first();
    //                 $endpoint = getShopifyURLForStore('recurring_application_charges/' . $request['charge_id'] . '/activate.json', $store);
    //                 $response = $this->makeAnAPICallToShopify('POST', $endpoint, null, $headers);
    //                 if ($response['statusCode'] === 200) {
    //                     $body = $response['body']['recurring_application_charge'];
    //                     Log::info($body);
    //                     if ($body['status'] && $body['status'] === 'active') {
    //                         // UserPlans::create([
    //                         //     'user_id' => $user->id,
    //                         //     'store_table_id' => $store->table_id,
    //                         //     'plan_id' => $plan_id,
    //                         //     'credits' => $plan->credits,
    //                         //     'price' => $plan->price,
    //                         //     'subscription_charge_id' => (int)$body['id'],
    //                         //     'status' => "ACTIVE",
    //                         //     'trial_days' => $body['trial_days'],
    //                         //     'trial_ends_on' => $body['trial_ends_on'],
    //                         //     'billing_on' => $body['billing_on'],
    //                         //     'subscription_created_at' => $body['created_at'],
    //                         //     'subscription_updated_at' => $body['updated_at'],
    //                         //     'response' => json_encode($body),
    //                         // ]);
    //                         $store->assignSubscriptionCredits($plan->credits);
    //                         return redirect()->route('billing.index')->with(
    //                             [
    //                                 'flash' =>
    //                                 [
    //                                     'status'    => true,
    //                                     'msg'       => 'Plan purchased successfully'
    //                                 ],
    //                             ]
    //                         );
    //                     }
    //                 }
    //             } elseif ($request['type'] == "onetime") {
    //                 $request = $request->only(['charge_id', 'type', 'credits']);
    //                 $endpoint = getShopifyURLForStore('application_charges/' . $request['charge_id'] . '.json', $store);
    //                 $response = $this->makeAnAPICallToShopify('GET', $endpoint, null, $headers);
    //                 if ($response['statusCode'] === 200) {
    //                     $body = $response['body']['application_charge'];
    //                     if ($body['status'] && $body['status'] === 'active') {
    //                         if (!UserOneTimePlan::where('charge_id', $request['charge_id'])->exists()) {
    //                             $credits = $request['credits'];
    //                             if (isset($store->getLastCreditPlanInfo)) {
    //                                 $credits += $store->getLastCreditPlanInfo->credits;
    //                             }
    //                             UserOneTimePlan::create([
    //                                 'user_id' => $user->id,
    //                                 'store_table_id' => $store->table_id,
    //                                 'credits' => $request['credits'],
    //                                 'available_credits' => $credits,
    //                                 'price' => $body['price'],
    //                                 'charge_id' => (int)$body['id'],
    //                                 'status' => $body['status'],
    //                                 'billing_on' => $body['created_at'],
    //                                 'response' => json_encode($body),
    //                             ]);

    //                             //                                $store->assignOneTimeCredits($credits);
    //                         }

    //                         return redirect()->route('billing.index')->with(
    //                             [
    //                                 'flash' =>
    //                                 [
    //                                     'status'    => true,
    //                                     'msg'       => 'Plan purchased successfully'
    //                                 ],
    //                             ]
    //                         );
    //                     }
    //                 }
    //             }
    //         }

    //         return back()->with('error', 'Some problem occurred. Please try again after some time.');
    //     } catch (Exception $e) {
    //         Log::info($e->getMessage());
    //         return back()->with('error', 'Some problem occurred. Please try again after some time.');
    //     }
    // }

    /**
     * @param Request $request
     */
    public function buyThisPlan(Request $request)
    {
        // try {
        $user  = Auth::user();
        $store = $user->getShopifyStore;

        // $plan = Plan::where('id', $id)->first();
        $endpoint = getShopifyURLForStore('recurring_application_charges.json', $store);
        $headers  = getShopifyHeadersForStore($store);

        $trialDays = 7; // Adding trial days here
        $payload   = [
            'recurring_application_charge' => [
                'name'       => config('app.name') . ' Charge for Plan ',
                'price'      => (float) 5,
                'trial_days' => $trialDays,
                'test'       => true, // Make sure to remove this in production, it's only for testing
                'return_url' => config('app.url') . 'dashboard/rac/accept?plan_id=',
            ],
        ];
        $response = $this->makeAnAPICallToShopify('POST', $endpoint, null, $headers, $payload);

        info($response);

        // dd($response);

        if ($response['statusCode'] === 201) {
            $body = $response['body']['recurring_application_charge'];
            return Inertia::location($body['confirmation_url']);
        }
        return back()->with('error', 'Some problem occurred. Please try again in some time.');
        // } catch(Exception $e) {
        //     Log::info($e->getMessage().' '.$e->getLine());
        //     return back()->with('error', 'Some problem occurred. Please try again in some time.');
        // }
    }
}
