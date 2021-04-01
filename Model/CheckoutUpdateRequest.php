<?php
declare(strict_types=1);

namespace Ingrid\Checkout\Model;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class CheckoutDataUpdateRequest
 * @package Ingrid\Checkout\Model
 * @Serializer\ExclusionPolicy("none")
 */
class CheckoutUpdateRequest {

    /**
     * @var string
     * @Serializer\Type("string")
     * @Serializer\SerializedName("email")
     */
    public $email;

    /**
     * @var CheckoutAddress
     * @Serializer\Type("Ingrid\Checkout\Model\CheckoutAddress")
     * @Serializer\SerializedName("address")
     */
    public $address;
}
