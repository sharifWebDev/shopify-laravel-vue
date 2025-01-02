<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Store;
use App\Models\ShopifyStore;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthManager;
use Assert\AssertionFailedException;
use App\ShopifyApp\Exceptions\HttpException;
use App\ShopifyApp\Objects\Values\AccessToken;
use Symfony\Component\HttpFoundation\Response;
use App\ShopifyApp\Objects\Values\SessionToken;
use App\ShopifyApp\Objects\Values\SessionContext;
use App\ShopifyApp\Objects\Values\NullableSessionId;

class VerifyShopifyAPI
{
    protected $auth;
    

    public function __construct(
        AuthManager $auth,
    ) {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function handle(Request $request, Closure $next)
    {
        info('touch Verify ShopifyAPI');
         
        $tokenSource = $request->bearerToken();

        if ($tokenSource === null) {
            return response()->json(['message' => 'Invalid token.'], 500);
        }
        try {
            // Try and process the token
            $token = SessionToken::fromNative($tokenSource);

        } catch (AssertionFailedException $e) {
            // Invalid or expired token, we need a new one
            return response()->json(['message' => 'Invalid token.'], 500);
        }
        if ($token !== null){
            // Login the shop
            $loginResult = $this->loginShopFromToken(
                $token,
                NullableSessionId::fromNative($request->query('session'))
            );
            if (!$loginResult) {
                // Shop is not installed or something is missing from its data
                return response()->json(['message' =>'Shop is not installed or missing data.'], 500);
            }
        }

        return $next($request);
    }


    protected function loginShopFromToken(SessionToken $token, NullableSessionId $sessionId): bool
    {

        info('loginShopFromToken');

        // Get the shop domain from the token
        $shopDomainValue = $token->getShopDomain()->getValue()->domain;

        // Now you can use $shopDomainString in your database query
        $user = User::where('myshopify_domain', $shopDomainValue)->first();
        // has the shop
        if (! $user) {
            return false;
        }

        $shop = ShopifyStore::where('myshopify_domain', $shopDomainValue)->first();
        // has the shop
        if (! $shop) {
            return false;
        }

        // Set the session details for the token, session ID, and access token
        $context = new SessionContext($token, $sessionId, AccessToken::fromNative($shop->access_token));

        $user->setSessionContext($context);


        // All is well, login the shop
        $this->auth->login($user);

        return true;
    }
}
