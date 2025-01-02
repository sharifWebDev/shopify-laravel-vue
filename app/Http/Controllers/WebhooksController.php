<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ShopifyStore;
use App\Models\UninstallApp;
use Illuminate\Http\Request;
use App\Jobs\ConfigureWebhooks;
use Illuminate\Support\Facades\Log;

class WebhooksController extends Controller
{
    //


    /**
     * @param $id
     */
    public function configureWebhooks($id)
    {
        try {
            ConfigureWebhooks::dispatchSync($id);
            print_r('Done');
        } catch (\Exception $e) {
            Log::info($e->getMessage() . ' ' . $e->getLine());
        }
    }


    /**
     * @param Request $request
     */
    public function appUninstalled(Request $request)
    {
        Log::info('Received webhook for store remove event app removed');
        // Log::info($request->all());
        $log_data = $request->all();
        Log::info('Request data: ' . json_encode($log_data));

        $req   = $request->all();
        $store = ShopifyStore::where('store_id', $req['id'])->first();


        if ($store) {
            $storeId = $store->store_id;

            $userAssignData = User::where('shopify_store_id', $storeId)->first();

            $history = UninstallApp::create([
                'response_data' => json_encode($req),
                'status'        => true,
                'user_id'       => $userAssignData->user_id,
            ]);

            // Notification::route('mail', $store->email)->notify(new ShopifyUninstall($store->name));

            $store->delete();
            // $user = User::where('store_id', $storeId)->first();

            // if ($user){
            //     $user->delete();
            // }

            // Log::info('Store removed from webhook. History Id: ' . $history->id);

            return response()->json(['status' => 200]);
        }
        return response()->json(['status' => 500, 'msg' => 'store data not found']);
    }



    public function customersDataRequest(Request $request)
    {
        Log::info('Customers Data Request info from webhook : ');
        $log_data = $request->all();
        Log::info('customersDataRequest method Request data: ' . json_encode($log_data));
    }

    public function customersRedacted(Request $request)
    {
        Log::info('customersRedacted info from webhook : ');
        $log_data = $request->all();
        Log::info('customersRedacted method Request data: ' . json_encode($log_data));
    }


    public function shopRedacted(Request $request)
    {
        Log::info('Shop Redacted info from webhook : ');
        $log_data = $request->all();
        Log::info('shopRedacted method Request data: ' . json_encode($log_data));

        $shopifyStore = ShopifyStore::where('id', $request->shop_id)->first();

        if (!$shopifyStore) {
            return response("Shopify Shop Redacted Webhook request!", 401);
        }
    }
}
