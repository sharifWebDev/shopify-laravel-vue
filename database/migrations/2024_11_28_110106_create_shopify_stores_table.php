<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shopify_stores', function (Blueprint $table) {
            $table->id();
            //
            $table->unsignedBigInteger('store_id')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('access_token')->nullable();
            $table->string('api_key')->nullable();
            $table->string('api_secret_key')->nullable();
            $table->string('myshopify_domain')->unique()->nullable();
            $table->string('phone')->nullable();
            $table->string('address1')->nullable();
            $table->string('address2')->nullable();
            $table->string('zip')->nullable();
            $table->longText('fulfillment_service_response')->nullable();
            $table->tinyInteger('fulfillment_service')->nullable();
            $table->tinyInteger('fulfillment_orders_opt_in')->nullable();
            $table->string('currency')->nullable();
            $table->double('webhook_status')->default(0);
            $table->longText('response')->nullable();
            //
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopify_stores');
    }
};
