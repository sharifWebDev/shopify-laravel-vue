<?php
 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController; 
use App\Http\Controllers\WebhooksController;
use App\Http\Controllers\InstallationController; 
 
Route::middleware(['web', 'auth.shopify'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('dashboard');
    
    Route::get('/dashboard', [HomeController::class, 'index']);
    
    Route::get('/posts', [HomeController::class, 'index'])->name('posts');
    
    Route::get('/posts/create', [HomeController::class, 'create'])->name('posts.create');
 
    Route::post('/posts/store', [HomeController::class, 'store'])->name('posts.store');
 
    Route::get('/posts/{post}/edit', [HomeController::class, 'edit'])->name('posts.edit');
 
    Route::put('/posts/{post}', [HomeController::class, 'update'])->name('posts.update');
 
    Route::get('/posts/{post}', [HomeController::class, 'show'])->name('posts.show');
 
    Route::delete('/posts/{post}', [HomeController::class, 'destroy'])->name('posts.destroy');
});

// Shopify/auth
Route::prefix('shopify/auth')->group(function () {
    Route::get('/', [InstallationController::class, 'startInstallation']);
    Route::get('redirect', [InstallationController::class, 'handleRedirect'])->name('app_install_redirect');
});

// Fulfillment routes
Route::any('/service_callback', [InstallationController::class, 'serviceCallback'])->name('service_callback');
 
Route::get('configure/webhooks/{id}', [WebhooksController::class, 'configureWebhooks']);
 
Route::prefix('webhook')->group(function () {
    Route::post('app/uninstall', [WebhooksController::class, 'appUninstalled']); 
    Route::any('shop/redacted', [WebhooksController::class, 'shopRedacted']); 
})->middleware('verify.shopify.webhook');
