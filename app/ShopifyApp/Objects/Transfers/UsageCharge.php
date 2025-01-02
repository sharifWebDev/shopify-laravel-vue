<?php

namespace App\ShopifyApp\Objects\Transfers;

use App\ShopifyApp\Contracts\Objects\Values\PlanId;
use App\ShopifyApp\Objects\Enums\ChargeStatus;
use App\ShopifyApp\Objects\Enums\ChargeType;
use App\ShopifyApp\Objects\Values\ShopId;
use Illuminate\Support\Carbon;
use Osiset\ShopifyApp\Objects\Transfers\ChargeReference;

/**
 * Represents create usage charge.
 */
final class UsageCharge extends AbstractTransfer
{
    /**
     * The shop ID.
     *
     * @var ShopId
     */
    public $shopId;

    /**
     * The plan ID.
     *
     * @var PlanId
     */
    public $planId;

    /**
     * The charge ID from Shopify.
     *
     * @var ChargeReference
     */
    public $chargeReference;

    /**
     * Usage charge type.
     *
     * @var ChargeType
     */
    public $chargeType;

    /**
     * Usage charge status.
     *
     * @var ChargeStatus
     */
    public $chargeStatus;

    /**
     * When the charge will be billed on.
     *
     * @var Carbon
     */
    public $billingOn;

    /**
     * Usage charge details.
     *
     * @var UsageChargeDetails
     */
    public $details;

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->chargeType = ChargeType::USAGE();
        $this->chargeStatus = ChargeStatus::ACCEPTED();
    }
}
