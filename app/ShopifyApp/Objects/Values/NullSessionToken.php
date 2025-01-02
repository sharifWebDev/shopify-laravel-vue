<?php

namespace App\ShopifyApp\Objects\Values;

use App\ShopifyApp\Contracts\Objects\Values\SessionToken as SessionTokenValue;
use Funeralzone\ValueObjects\NullTrait;

/**
 * Value object for session token (null).
 */
final class NullSessionToken implements SessionTokenValue
{
    use NullTrait;
}
