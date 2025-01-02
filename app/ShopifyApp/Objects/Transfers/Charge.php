<?php

namespace App\ShopifyApp\Objects\Transfers;

use App\ShopifyApp\Contracts\Objects\Values\PlanId;
use App\ShopifyApp\Objects\Enums\ChargeStatus;
use App\ShopifyApp\Objects\Enums\ChargeType;
use App\ShopifyApp\Objects\Values\ChargeReference;
use App\ShopifyApp\Objects\Values\ShopId;
use Illuminate\Support\Carbon;

/**
 * Represents create charge.
 */
final class Charge extends AbstractTransfer
{
    /**
     * Shop ID.
     *
     * @var ShopId
     */
    public $shopId;

    /**
     * Plan ID.
     *
     * @var PlanId
     */
    public $planId;

    /**
     * Charge ID from Shopify.
     *
     * @var ChargeReference
     */
    public $chargeReference;

    /**
     * Charge type (recurring or single).
     *
     * @var ChargeType
     */
    public $chargeType;

    /**
     * Charge status.
     *
     * @var ChargeStatus
     */
    public $chargeStatus;

    /**
     * When the charge was activated.
     *
     * @var Carbon
     */
    public $activatedOn;

    /**
     * When the charge will be billed on.
     *
     * @var Carbon|null
     */
    public $billingOn;

    /**
     * When the trial ends on.
     *
     * @var Carbon|null
     */
    public $trialEndsOn;

    /**
     * Plan details for reference.
     *
     * @var PlanDetails
     */
    public $planDetails;
}
