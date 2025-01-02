<?php

namespace App\ShopifyApp\Objects\Values;

use App\ShopifyApp\Contracts\Objects\Values\SessionId as SessionIdValue;
use Funeralzone\ValueObjects\NullTrait;

/**
 * Value object for session ID of a session token (null).
 */
final class NullSessionId implements SessionIdValue
{
    use NullTrait;
}
