<?php

namespace App\ShopifyApp\Objects\Values;

use App\ShopifyApp\Contracts\Objects\Values\SessionToken as SessionTokenValue;
use App\ShopifyApp\Contracts\Objects\Values\ShopDomain as ShopDomainValue;
use Assert\Assert;
use Assert\AssertionFailedException;
use Funeralzone\ValueObjects\Scalars\StringTrait;
use Illuminate\Support\Carbon;
use App\ShopifyApp\Util;

/**
 * Value object for a session token (JWT).
 */
final class SessionToken implements SessionTokenValue
{
    use StringTrait;

    public const TOKEN_FORMAT = '/^eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9\.[A-Za-z0-9\-_=]+\.[A-Za-z0-9\-_=]*$/';
    public const EXCEPTION_MALFORMED = 'Session token is malformed.';
    public const EXCEPTION_INVALID = 'Session token is invalid.';
    public const EXCEPTION_EXPIRED = 'Session token has expired.';
    public const LEEWAY_SECONDS = 10;

    protected array $parts;
    protected string $iss;
    protected string $dest;
    protected string $aud;
    protected string $sub;
    protected Carbon $exp;
    protected Carbon $nbf;
    protected Carbon $iat;
    protected string $jti;
    protected SessionId $sid;
    protected ShopDomainValue $shopDomain;

    public function __construct(string $token, bool $verifyToken = true)
    {
        $this->string = $token;
        $this->decodeToken();

        if ($verifyToken) {
            $this->verifySignature();
            $this->verifyValidity();
            $this->verifyExpiration();
        }
    }

    protected function decodeToken(): void
    {
        Assert::that($this->string)->regex(self::TOKEN_FORMAT, self::EXCEPTION_MALFORMED);

        $this->parts = explode('.', $this->string);
        $body = json_decode(Util::base64UrlDecode($this->parts[1]), true);

        Assert::thatAll([
            $body['iss'] ?? null,
            $body['dest'] ?? null,
            $body['aud'] ?? null,
            $body['sub'] ?? null,
            $body['exp'] ?? null,
            $body['nbf'] ?? null,
            $body['iat'] ?? null,
            $body['jti'] ?? null,
            $body['sid'] ?? null,
        ])->notNull(self::EXCEPTION_MALFORMED);

        $this->iss = $body['iss'];
        $this->dest = $body['dest'];
        $this->aud = $body['aud'];
        $this->sub = $body['sub'];
        $this->jti = $body['jti'];
        $this->sid = SessionId::fromNative($body['sid']);
        $this->exp = Carbon::createFromTimestamp($body['exp']);
        $this->nbf = Carbon::createFromTimestamp($body['nbf']);
        $this->iat = Carbon::createFromTimestamp($body['iat']);

        $host = parse_url($body['dest'], PHP_URL_HOST);
        $this->shopDomain = NullableShopDomain::fromNative($host);
    }

    public function getShopDomain(): ShopDomainValue
    {
        return $this->shopDomain;
    }

    public function getSessionId(): SessionId
    {
        return $this->sid;
    }

    public function getExpiration(): Carbon
    {
        return $this->exp;
    }

    public function getNotBefore(): Carbon
    {
        return $this->nbf;
    }

    public function getIssuedAt(): Carbon
    {
        return $this->iat;
    }

    public function getIssuer(): string
    {
        return $this->iss;
    }

    public function getDestination(): string
    {
        return $this->dest;
    }

    public function getAudience(): string
    {
        return $this->aud;
    }

    public function getSubject(): string
    {
        return $this->sub;
    }

    public function getTokenId(): string
    {
        return $this->jti;
    }

    public function getLeewayExpiration(): Carbon
    {
        return (clone $this->exp)->addSeconds(self::LEEWAY_SECONDS);
    }

    public function getLeewayNotBefore(): Carbon
    {
        return (clone $this->nbf)->subSeconds(self::LEEWAY_SECONDS);
    }

    public function getLeewayIssuedAt(): Carbon
    {
        return (clone $this->iat)->subSeconds(self::LEEWAY_SECONDS);
    }

    protected function verifySignature(): void
    {
        $partsCopy = $this->parts;
        $signature = Hmac::fromNative(array_pop($partsCopy));
        $tokenWithoutSignature = implode('.', $partsCopy);

        $secret = config('shopify.shopify_api_secret');
        $hmac = Util::createHmac(['data' => $tokenWithoutSignature, 'raw' => true], $secret);
        $encodedHmac = Hmac::fromNative(Util::base64UrlEncode($hmac->toNative()));

        Assert::that($signature->isSame($encodedHmac))->true(self::EXCEPTION_INVALID);
    }

    protected function verifyValidity(): void
    {
        Assert::that($this->iss)->contains($this->dest, self::EXCEPTION_INVALID);
        Assert::that($this->aud)->eq(config('shopify.shopify_api_key'), self::EXCEPTION_INVALID);
    }

    protected function verifyExpiration(): void
    {
        $now = Carbon::now();

        Assert::that($now->greaterThan($this->getLeewayExpiration()))->false(self::EXCEPTION_EXPIRED);
        Assert::that($now->lessThan($this->getLeewayNotBefore()))->false(self::EXCEPTION_EXPIRED);
        Assert::that($now->lessThan($this->getLeewayIssuedAt()))->false(self::EXCEPTION_EXPIRED);
    }
}
