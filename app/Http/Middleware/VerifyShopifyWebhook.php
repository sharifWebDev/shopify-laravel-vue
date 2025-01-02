<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyShopifyWebhook
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        info('touch Verify ShopifyAPI');
        // Capture the raw body
        $data = $request->getContent();

        info($request->all());

        // Get the HMAC header
        $hmac = $request->header('x-shopify-hmac-sha256');
        $secret = config('shopify.shopify_api_secret');

        // Generate the hash
        $hash = base64_encode(hash_hmac('sha256', $data, $secret, true));

        // Log the HMAC and generated hash for debugging
        info("Shopify HMAC: " . $hmac);
        info("Generated Hash: " . $hash);

        // Verify the hash
        if ($hash !== $hmac) {
            return response("Couldn't verify incoming Webhook request!", 401);
        }

        // Parse the JSON body
        $request->json()->all();

        return $next($request);
    }
}
