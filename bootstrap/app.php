<?php

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use App\Http\Middleware\HandleInertiaRequests;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Middleware\FrameHeadersMiddleware;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
        $middleware
            ->append(FrameHeadersMiddleware::class)
            ->validateCsrfTokens(except: [
                '*',
            ])
            ->web(append: [
                HandleInertiaRequests::class,
            ])
            ->alias([
                'auth.shopify'           => \App\Http\Middleware\VerifyShopify::class,
                'auth.api.shopify'       => \App\Http\Middleware\VerifyShopifyAPI::class,
                'verify.shopify.webhook' => \App\Http\Middleware\VerifyShopifyWebhook::class,

            ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->respond(function (Response $response, Throwable $exception, Request $request) {
            if (!app()->environment(['local', 'testing']) && in_array($response->getStatusCode(), [500, 503, 404, 403])) {
                return Inertia::render('Error', ['status' => $response->getStatusCode()])
                    ->toResponse($request)
                    ->setStatusCode($response->getStatusCode());
            } elseif ($response->getStatusCode() === 419) {
                return back()->with([
                    'message' => 'The page expired, please try again.',
                ]);
            }

            return $response;
        });
    })->create();
