<?php

declare(strict_types=1);

namespace Ingrid\Checkout\Api;

interface IngridSessionInterface {

    /**

     * @param int $completed
     * @return $this
     */
    public function setIsCompleted(bool $completed);

    /**
     * Whether the COS Session is completed or not
     *
     * @return bool
     */
    public function isCompleted(): bool;

    /**
     * Set COS ID
     *
     * @param string $sessionId
     * @return $this
     */
    public function setSessionId(string $sessionId);

    /**
     * Get COS ID
     *
     * @return string
     */
    public function getSessionId(): string;

    /**
     * Entity ID
     *
     * @return int
     */
    public function getId();

    public function getOrderId() : int;
    public function setOrderId(int $orderId);

    public function setOrderIncrementId(string $orderIncrementId);

    public function getExternalMethodId(): ?string;
    public function setExternalMethodId(string $externalMethodId);

    public function getShippingMethod(): string;
    public function setShippingMethod(string $shippingMethod);

    public function getCarrier(): ?string;
    public function setCarrier(string $carrier);

    public function getProduct(): ?string;
    public function setProduct(string $product);

    public function getCategoryName(): string;
    public function setCategoryName(string $categoryName);

    public function getLocationId(): ?string;
    public function setLocationId(string $locationId);

    public function getLocationName(): ?string;
    public function setLocationName(string $locationName);

    public function isTest(): bool;
    public function setTest(bool $isTest);

    public function getTimeSlotId(): ?string;
    public function setTimeSlotId(string $timeSlotId);

    public function getTimeSlotStart(): ?string;
    public function setTimeSlotStart(string $timeSlotStart);

    public function getTimeSlotEnd(): ?string;
    public function setTimeSlotEnd(string $timeSlotEnd);
}
