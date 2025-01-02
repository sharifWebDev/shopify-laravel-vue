<?php

namespace App\ShopifyApp\Traits;

use Gnikyt\BasicShopifyAPI\BasicShopifyAPI;
use Gnikyt\BasicShopifyAPI\Session;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\ShopifyApp\Contracts\ApiHelper as IApiHelper;
use App\ShopifyApp\Contracts\Objects\Values\AccessToken as AccessTokenValue;
use App\ShopifyApp\Contracts\Objects\Values\ShopDomain as ShopDomainValue;
use App\ShopifyApp\Contracts\Objects\Values\ShopId as ShopIdValue;
use App\ShopifyApp\Objects\Values\AccessToken;
use App\ShopifyApp\Objects\Values\SessionContext;
use App\ShopifyApp\Objects\Values\ShopDomain;
use App\ShopifyApp\Objects\Values\ShopId;
//use App\ShopifyApp\Storage\Models\Charge;
//use App\ShopifyApp\Storage\Models\Plan;
//use App\ShopifyApp\Storage\Scopes\Namespacing;
use App\ShopifyApp\Util;

/**
 * Responsible for representing a shop record.
 */
trait ShopModel
{
//    use SoftDeletes;

    /**
     * The API helper instance.
     *
     * @var IApiHelper
     */
    public $apiHelper;

    /**
     * Session context used between requests.
     *
     * @var SessionContext
     */
    protected $sessionContext;

    /**
     * Boot the trait.
     *
     * Note that the method boot[TraitName] is automatically booted by Laravel.
     *
     * @return void
     */
//    protected static function bootShopModel(): void
//    {
//        static::addGlobalScope(new Namespacing());
//    }

    /**
     * {@inheritdoc}
     */
    public function getId(): ShopIdValue
    {
        return ShopId::fromNative($this->table_id);
    }

    /**
     * {@inheritdoc}
     */
    public function getDomain(): ShopDomainValue
    {
        return ShopDomain::fromNative($this->myshopify_domain);
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessToken(): AccessTokenValue
    {
        return AccessToken::fromNative($this->access_token);
    }

    /**
     * {@inheritdoc}
     */
    public function charges(): HasMany
    {
        return $this->hasMany(
            Util::getShopifyConfig('models.charge', Charge::class),
            Util::getShopsTableForeignKey(),
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
//    public function hasCharges(): bool
//    {
//        return $this->charges->isNotEmpty();
//    }

    /**
     * {@inheritdoc}
     */
//    public function plan(): BelongsTo
//    {
//        return $this->belongsTo(Util::getShopifyConfig('models.plan', Plan::class));
//    }

    /**
     * {@inheritdoc}
     */
    public function isGrandfathered(): bool
    {
        return (bool) $this->shopify_grandfathered === true;
    }

    /**
     * {@inheritdoc}
     */
    public function isFreemium(): bool
    {
        return (bool) $this->shopify_freemium === true;
    }

    /**
     * {@inheritdoc}
     */
    public function hasOfflineAccess(): bool
    {
        return ! $this->getAccessToken()->isNull() && ! empty($this->password);
    }

    /**
     * {@inheritDoc}
     */
    public function setSessionContext(SessionContext $session): void
    {
        $this->sessionContext = $session;
    }

    /**
     * {@inheritDoc}
     */
    public function getSessionContext(): ?SessionContext
    {
        return $this->sessionContext;
    }

    /**
     * {@inheritdoc}
     */
    public function apiHelper(): IApiHelper
    {
        if ($this->apiHelper === null) {
            // Set the session
            $session = new Session(
                $this->getDomain()->toNative(),
                $this->getAccessToken()->toNative()
            );
            $this->apiHelper = resolve(IApiHelper::class)->make($session);
        }

        return $this->apiHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function api(): BasicShopifyAPI
    {
        if ($this->apiHelper === null) {
            $this->apiHelper();
        }

        return $this->apiHelper->getApi();
    }
}
