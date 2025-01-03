<?php

namespace App\ShopifyApp\Objects\Values;

use App\ShopifyApp\Contracts\Objects\Values\ShopDomain as ShopDomainValue;
use App\ShopifyApp\Exceptions\InvalidShopDomainException;
use App\ShopifyApp\Objects\Enums\DataSource;
use Assert\AssertionFailedException;
use Funeralzone\ValueObjects\Scalars\StringTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\ShopifyApp\Util;

/**
 * Value object for shop's domain.
 */
final class ShopDomain implements ShopDomainValue
{
    use StringTrait;
    public string $domain;

    /**
     * Constructor.
     *
     * @param string $domain The shop's domain.
     *
     * @throws InvalidShopDomainException
     *
     * @return void
     */
    public function __construct(string $domain)
    {
        $this->string = $this->sanitizeShopDomain($domain);
        $this->domain = $this->string;

        if ($this->string === '') {
            throw new InvalidShopDomainException("Invalid shop domain [{$domain}]");
        }
    }

    /**
     * Grab the shop, if present, and how it was found.
     * Order of precedence is:.
     *
     *  - GET/POST Variable ("shop" or "shopDomain")
     *  - Headers ("X-Shop-Domain")
     *  - Referer ("shop" or "shopDomain" query param or decoded "token" query param)
     *
     * @param Request $request The request object.
     *
     * @return ShopDomainValue
     */
    public static function fromRequest(Request $request): ShopDomainValue
    {
        // All possible methods
        $options = [
            // GET/POST
            DataSource::INPUT()->toNative() => $request->input('shop', $request->input('shopDomain')),

            // Headers
            DataSource::HEADER()->toNative() => $request->header('X-Shop-Domain'),

            // Headers: Referer
            DataSource::REFERER()->toNative() => function () use ($request): ?string {
                $url = parse_url($request->header('referer', ''), PHP_URL_QUERY);
                if (! $url) {
                    return null;
                }

                $params = Util::parseQueryString($url);
                $shop = Arr::get($params, 'shop', Arr::get($params, 'shopDomain'));
                if ($shop) {
                    return $shop;
                }

                $token = Arr::get($params, 'token');
                if ($token) {
                    try {
                        $token = new SessionToken($token, false);
                        if ($shopDomain = $token->getShopDomain()) {
                            return $shopDomain->toNative();
                        }
                    } catch (AssertionFailedException $e) {
                        // Unable to decode the token
                        return null;
                    }
                }

                return null;
            },
        ];

        // Loop through each until we find the shop
        foreach ($options as $value) {
            $result = is_callable($value) ? $value() : $value;
            if ($result !== null) {
                // Found a shop
                return self::fromNative($result);
            }
        }

        // No shop domain found in any source
        return NullShopDomain::fromNative(null);
    }

    /**
     * Ensures shop domain meets the specs.
     *
     * @param string $domain The shopify domain
     *
     * @return string
     */
    protected function sanitizeShopDomain(string $domain): string
    {
        $configEndDomain = config('shopify.myshopify_domain')??'';
        $domain = strtolower(preg_replace('/^https?:\/\//i', '', trim($domain)));

        if (strpos($domain, $configEndDomain) === false && strpos($domain, '.') === false) {
            // No myshopify.com ($configEndDomain) in shop's name
            $domain .= ".{$configEndDomain}";
        }

        $hostname = parse_url("https://{$domain}", PHP_URL_HOST);

        if (! preg_match('/^[a-zA-Z0-9][a-zA-Z0-9\-]*\.'.preg_quote($configEndDomain, '/').'$/', $hostname)) {
            return '';
        }

        return $hostname;
    }
}
