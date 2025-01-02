<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FrameHeadersMiddleware {
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle( Request $request, Closure $next ): Response {
        $response = $next( $request );

        // Remove X-Frame-Options header
        $response->headers->remove( 'X-Frame-Options' );

        // allow all iframes
        $response->headers->set( 'X-Frame-Options', 'ALLOWALL' );

        // Add Content-Security-Policy header to allow Shopify admin
        $shop = $request->get( 'shop' );
        if ( $shop ) {
            $response->headers->set(
                'Content-Security-Policy',
                "frame-ancestors https://admin.shopify.com https://" . $shop
            );
        } else {
            $response->headers->set(
                'Content-Security-Policy',
                "frame-ancestors https://admin.shopify.com"
            );
        }

        // info($response);
        // dd($response );

        return $response;
    }
}
