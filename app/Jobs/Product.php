<?php

namespace App\Jobs;


use App\Models\Product as ModelsProduct;
use App\Traits\FunctionTrait;
use App\Traits\RequestTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class Product implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use FunctionTrait, RequestTrait;
    public $user, $store;
    /**
     * Create a new job instance.
     * @return void
     */
    public function __construct($user, $store) {
        $this->user = $user;
        $this->store = $store;
    }

    /**
     * Execute the job.
     * @return void
     */
    public function handle() {
        try {
            $since_id = 0;
            $headers = getShopifyHeadersForStore($this->store);
            do {
                $endpoint = getShopifyURLForStore('products.json?limit=250&since_id='.$since_id, $this->store);
                $response = $this->makeAnAPICallToShopify('GET', $endpoint, null, $headers);
                $products = $response['statusCode'] == 200 ? $response['body']['products'] ?? null : null;

                foreach($products as $product) {
                    $since_id = $product['id'];
                    $this->updateOrCreateThisProductInDB($product);
                }

            } while($products !== null && count($products) > 0);

        } catch(Exception $e) {
            Log::info($e->getMessage());
        }
    }

    public function getProductsByGraphql(){

        $headers  = getShopifyHeadersForStore($this->store);
        $endpoint = getShopifyURLForStore('graphql.json', $this->store);
        $cursor = null;
        $hasNextPage = true;

        do {

            // GraphQL Query
            $query = "query (\$cursor: String) {
                        products(first: 10, after: \$cursor) {
                            pageInfo {
                                hasNextPage
                                endCursor
                            }
                            edges {
                                node {
                                    id
                                    title
                                    descriptionHtml
                                    bodyHtml
                                    handle
                                    vendor
                                    productType
                                    tags
                                    status
                                    createdAt
                                    updatedAt
                                    onlineStorePreviewUrl
                                    publishedAt
                                    seo {
                                      description
                                      title
                                    }
                                    variants(first: 10) {
                                      edges {
                                        node {
                                          sku
                                          taxable
                                          title
                                          id
                                          availableForSale
                                          compareAtPrice
                                          barcode
                                          createdAt
                                          displayName
                                          inventoryItem {
                                            id
                                            unitCost {
                                              amount
                                              currencyCode
                                            }
                                          }
                                          inventoryManagement
                                          fulfillmentService {
                                            serviceName
                                          }
                                          inventoryQuantity
                                          inventoryPolicy
                                          weight
                                          updatedAt
                                          weightUnit
                                          position
                                          price
                                          selectedOptions {
                                            name
                                            value
                                          }
                                        }
                                      }
                                    }
                                    collections(first: 10) {
                                      edges {
                                        node {
                                          id
                                        }
                                      }
                                    }
                                    productCategory {
                                      productTaxonomyNode {
                                        id
                                      }
                                    }
                                    options {
                                      id
                                      name
                                      position
                                      values
                                    }
                                    images(first: 10) {
                                      edges {
                                        node {
                                          id
                                        }
                                      }
                                    }
                                }
                            }
                        }
                    }";

            // Query Variables
            $variables = [
                'numProducts' => 10,
                'cursor' => $cursor,
            ];


            $response = $this->makeAnAPICallToShopify('POST', $endpoint, null, $headers, ['query' => $query, 'variables' => $variables]);

            // Check for errors in the response
            if (isset($response['statusCode']) && $response['statusCode'] !== 200) {
                // Handle error
                // Log or display the error message
                Log::info($response);
            }
            $responseBody = $response['body'];

            $data = $responseBody['data'];
            $products = $data['products']['edges'];
            $pageInfo = $data['products']['pageInfo'];


//            foreach ($products as $product) {
//                $productId = $product['node']['id'];
//                // Process product data and update/create in your database
//            }

            $hasNextPage = $pageInfo['hasNextPage'];
            $cursor = $pageInfo['endCursor'];

            Log::info($products);
            sleep(15);

        } while ($hasNextPage);

    }

    private function updateOrCreateThisProductInDB($product) {
        try {
            $payload = [
                'store_id' => $this->store->id,
                'store_table_id' => $this->store->table_id,
                'id' => $product['id'],
                'admin_graphql_api_id' => $product['admin_graphql_api_id'],
                'title' => $product['title'],
                'body_html' => $product['body_html'],
                'vendor' => $product['vendor'],
                'handle' => $product['handle'],
                'product_type' => $product['product_type'],
                'variants' => json_encode($product['variants']),
                'options' => json_encode($product['options']),
                'images' => json_encode($product['images']),
                'tags' => $product['tags'],
                'status' => $product['status'],
                'published_at' => $product['published_at'],
                'created_at' => $product['created_at'],
                'updated_at' => $product['updated_at'],
            ];
            $update_arr = [
                'store_id' => $this->store->id,
                'id' => $product['id']
            ];
            ModelsProduct::updateOrCreate($update_arr, $payload);
            return true;
        } catch(Exception $e) {
            Log::info($e->getMessage());
        }
    }

}
