<?php

namespace App\ShopifyApp\Objects\Values;

use App\ShopifyApp\Contracts\Objects\Values\SessionToken as SessionTokenValue;
use Funeralzone\ValueObjects\Nullable;

/**
 * Value object for session token (nullable).
 */
final class NullableSessionToken extends Nullable implements SessionTokenValue
{
    /**
     * @return string
     */
    protected static function nonNullImplementation(): string
    {
        return SessionToken::class;
    }

    /**
     * @return string
     */
    protected static function nullImplementation(): string
    {
        return NullSessionToken::class;
    }
}
