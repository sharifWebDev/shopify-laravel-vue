<?php

namespace App\ShopifyApp\Objects\Values;

use App\ShopifyApp\Contracts\Objects\Values\SessionId as SessionIdValue;
use Funeralzone\ValueObjects\Nullable;

/**
 * Value object for session ID of a session token (nullable).
 */
final class NullableSessionId extends Nullable implements SessionIdValue
{
    /**
     * @return string
     */
    protected static function nonNullImplementation(): string
    {
        return SessionId::class;
    }

    /**
     * @return string
     */
    protected static function nullImplementation(): string
    {
        return NullSessionId::class;
    }
}
