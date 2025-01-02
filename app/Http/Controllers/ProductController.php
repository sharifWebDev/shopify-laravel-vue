<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Product;
use App\Traits\RequestTrait;
use Illuminate\Http\Request;
use App\Traits\FunctionTrait;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    use FunctionTrait, RequestTrait;

    public function fetchProductData(Request $request)
    {
        $user = Auth::user();
        $store = $user->getShopifyStore;
        $headers = getShopifyHeadersForStore($store);
        $endpoint = getShopifyURLForStore('graphql.json', $store);

        $allProducts = [];
        $hasNextPage = false;
        $cursor = null;

        try {
            do {
                // Dynamically build the query
                $query = <<<GRAPHQL
                {
                    products(first: 250
            GRAPHQL;

                if ($cursor !== null) {
                    $query .= ', after: "' . addslashes($cursor) . '"';
                }

                $query .= <<<GRAPHQL
                    ) {
                        edges {
                            cursor
                            node {
                                id
                                title
                                handle
                                descriptionHtml
                                variants(first: 5) {
                                    edges {
                                        node {
                                            id
                                            title
                                            price
                                            inventoryQuantity
                                        }
                                    }
                                }
                            }
                        }
                        pageInfo {
                            hasNextPage
                        }
                    }
                }
                GRAPHQL;

                // Prepare the request body
                $requestBody = ['query' => $query];

                // Make the API call
                $response = $this->makeAnAPICallToShopify('POST', $endpoint, null, $headers, $requestBody);

                //   info('Fetched All Products >>>>>', ['products' => $response]);

                // Process the response  
                if ($response['statusCode'] === 200 && isset($response['body']['data']['products'])) {
                    $productsData = $response['body']['data']['products'];
                    $allProducts = array_merge($allProducts, $productsData['edges']);
                    $hasNextPage = $productsData['pageInfo']['hasNextPage'];

                    // Update the cursor for the next page
                    $lastEdge = end($productsData['edges']);
                    $cursor = $lastEdge['cursor'] ?? null;
                } else {
                    // Handle errors and exit the loop
                    $hasNextPage = false;
                    info('Error fetching products:', $response);
                }
            } while ($hasNextPage);


            return response()->json([
                'status' => 'success',
                'data' => $allProducts,
            ]);
        } catch (Exception $e) {
            logger()->error($e->getMessage(), ['line' => $e->getLine(), 'file' => $e->getFile()]);
        }

        // info('Fetched All Products', ['products' => $allProducts]);


    }
}
