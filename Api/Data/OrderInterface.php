<?php
declare(strict_types=1);

namespace Ingrid\Checkout\Api\Data;

/**
 * Interface OrderInterface
 *
 * For Ingrid Order data ExtensionAttribute on SalesOrder
 *
 * @package Ingrid\Checkout\Api\Data
 * @api
 */
interface OrderInterface {

    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @return bool
     */
    public function isTest(): bool;

    /**
     * @return string
     */
    public function getSessionId(): string;

    /**
     * @return string
     */
    public function getShippingMethod(): string;

    /**
     * @return string|null
     */
    public function getCarrier(): ?string;

    /**
     * @return string|null
     */
    public function getProduct(): ?string;

    /**
     * @return string|null
     */
    public function getExternalMethodId(): ?string;

    /**
     * @return string|null
     */
    public function getLocationId(): ?string;

    /**
     * @return string|null
     */
    public function getLocationName(): ?string;

    /**
     * @return string|null
     */
    public function getTimeSlotId(): ?string;

    /**
     * @return string|null
     */
    public function getTimeSlotStart(): ?string;

    /**
     * @return string|null
     */
    public function getTimeSlotEnd(): ?string;
}
