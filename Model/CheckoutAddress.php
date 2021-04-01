<?php
declare(strict_types=1);

namespace Ingrid\Checkout\Model;

use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Type;

/**
 * Class CheckoutAddress
 * @package Ingrid\Checkout\Model
 * @Serializer\ExclusionPolicy("none")
 */
class CheckoutAddress {

    /**
     * @var string
     * @Type("string")
     * @Serializer\SerializedName("countryId")
     */
    public $countryId;

    /**
     * @var string
     * @Type("string")
     */
    public $postcode;

    /**
     * @var string
     * @Type("string")
     */
    public $city;

    /**
     * @var string
     * @Type("string")
     */
    public $regionCode;

    /**
     * @var string[]
     * @Type("array<string>")
     */
    public $street;

    /**
     * @var string
     * @Type("string")
     */
    public $telephone;
}
