<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShopifyStore extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function hasRegisteredForFulfillmentService(): bool
    {
        return $this->fulfillment_service === 1 || $this->fulfillment_service === true;
    }

    public function getUser()
    {
        return $this->hasOne(User::class, 'shopify_store_id', 'store_id');
    }

    public function getStoreTemplates()
    {
        return $this->hasMany(StoreTemplate::class);
    }
}
