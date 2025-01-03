<?php

namespace App\Jobs;

use Exception;
use App\Models\Store;
use App\Models\ShopifyStore;
use App\Traits\RequestTrait;
use App\Traits\FunctionTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class ConfigureWebhooks implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use FunctionTrait, RequestTrait;

    private $store_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($store_id) {
        $this->store_id = $store_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        try {
            $store = ShopifyStore::where('store_id', $this->store_id)->first();
            $endpoint = getShopifyURLForStore('webhooks.json', $store);
            $headers = getShopifyHeadersForStore($store);
            $webhooks_config = config('shopify.webhook_events');
            foreach($webhooks_config as $topic => $url) {
                $body = [
                    'webhook' => [
                        'topic' => $topic,
                        'address' => config('app.url').'webhook/'.$url,
                        'format' => 'json'
                    ]
                ];
                $response = $this->makeAnAPICallToShopify('POST', $endpoint, null, $headers, $body);
                Log::info('Response for topic '.$topic);
                Log::info($response['body']);
                //You can write a logic to save this in the database table.
            } 
        } catch(Exception $e) {
            //Log::info(json_encode($e->getTrace()));
            Log::info('here in configure webhooks ' . $e->getMessage().' '.$e->getLine());
        }
    }
}
