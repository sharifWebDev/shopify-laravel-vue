<?php

namespace App\Jobs;

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

class DeleteWebhooks implements ShouldQueue {

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
    public function handle()
    {
        $store = ShopifyStore::where('store_id', $this->store_id)->first();
        $endpoint = getShopifyURLForStore('webhooks.json', $store);
        $headers = getShopifyHeadersForStore($store);
        $response = $this->makeAnAPICallToShopify('GET', $endpoint, null, $headers);
        $webhooks = $response['body']['webhooks'];
        foreach($webhooks as $webhook) {
            $endpoint = getShopifyURLForStore('webhooks/'.$webhook['id'].'.json', $store);
            $response = $this->makeAnAPICallToShopify('DELETE', $endpoint, null, $headers);
            Log::info('Response for deleting webhooks');
            Log::info($response);
        }
    }
}
