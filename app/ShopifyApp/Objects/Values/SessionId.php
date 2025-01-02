<?php

namespace App\ShopifyApp\Objects\Values;

use App\ShopifyApp\Contracts\Objects\Values\SessionId as SessionIdValue;
use Funeralzone\ValueObjects\Scalars\StringTrait;

/**
 * Value object for session ID of a session token.
 */
final class SessionId implements SessionIdValue
{
    use StringTrait;
}
