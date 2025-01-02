<?php

namespace App\ShopifyApp\Objects\Values;

use App\ShopifyApp\Contracts\Objects\Values\PlanId as PlanIdValue;
use Funeralzone\ValueObjects\Nullable;

/**
 * Value object for plan's ID (nullable).
 */
final class NullablePlanId extends Nullable implements PlanIdValue
{
    /**
     * @return string
     */
    protected static function nonNullImplementation(): string
    {
        return PlanId::class;
    }

    /**
     * @return string
     */
    protected static function nullImplementation(): string
    {
        return NullPlanId::class;
    }
}
