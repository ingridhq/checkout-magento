<?php

declare(strict_types=1);

namespace Ingrid\Checkout\Model;

use Ingrid\Checkout\Api\IngridSessionInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class IngridSession extends AbstractModel implements IdentityInterface, IngridSessionInterface {
    protected $_eventPrefix = 'ingrid_checkout';

    protected $_eventObject = 'session';

    const CACHE_TAG = 'ingrid_session';

    const KEY_MAGENTO_ORDER_ID = 'order_id';
    const KEY_MAGENTO_ORDER_INCREMENT_ID = 'order_increment_id';
    const KEY_INGRID_SESSION_ID = 'session_id';
    const KEY_TOS_ID = 'tos_id';
    const KEY_IS_COMPLETED = 'is_completed';
    const KEY_EXTERNAL_METHOD_ID = 'external_method_id';
    const KEY_SHIPPING_METHOD = 'shipping_method';
    const KEY_CATEGORY_NAME = 'category_name';
    const KEY_LOCATION_ID = 'location_id';
    const KEY_LOCATION_NAME = 'location_name';
    const KEY_IS_TEST = 'is_test';
    const KEY_TIME_SLOT_ID = 'time_slot_id';
    const KEY_TIME_SLOT_START = 'time_slot_start';
    const KEY_TIME_SLOT_END = 'time_slot_end';
    const KEY_CARRIER = 'carrier';
    const KEY_PRODUCT = 'product';

    /**
     * Constructor
     *
     * @codeCoverageIgnore
     * @codingStandardsIgnoreLine
     */
    protected function _construct() {
        $this->_init(ResourceModel\IngridSession::class);
    }

    /**
     * Get Identities
     *
     * @return array
     */
    public function getIdentities() {
        return [self::CACHE_TAG.'_'.$this->getSessionId()];
    }

    /**
     * @return string Ingrid TOS ID
     */
    public function getTosId(): string {
        return $this->_getData(self::KEY_TOS_ID);
    }

    /**
     * @param string $tosId
     * @return $this
     */
    public function setTosId(string $tosId) {
        $this->setData(self::KEY_TOS_ID, $tosId);
        return $this;
    }

    /**
     * @return string Ingrid Session ID
     */
    public function getSessionId(): string {
        return $this->_getData(self::KEY_INGRID_SESSION_ID);
    }

    /**
     * @param string $sessionId
     * @return $this
     */
    public function setSessionId(string $sessionId) {
        $this->setData(self::KEY_INGRID_SESSION_ID, $sessionId);
        return $this;
    }

    /**
     * @return string Magento Order ID
     */
    public function getOrderId(): int {
        return (int) $this->_getData(self::KEY_MAGENTO_ORDER_ID);
    }

    /**
     * @param int $orderId
     * @return $this
     */
    public function setOrderId(int $orderId) {
        $this->setData(self::KEY_MAGENTO_ORDER_ID, $orderId);
        return $this;
    }

    /**
     * @param string $orderIncrementId
     * @return $this
     */
    public function setOrderIncrementId(string $orderIncrementId) {
        $this->setData(self::KEY_MAGENTO_ORDER_INCREMENT_ID, $orderIncrementId);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsCompleted(bool $completed) {
        $this->setData(self::KEY_IS_COMPLETED, $completed);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isCompleted(): bool {
        return (bool) $this->_getData(self::KEY_IS_COMPLETED);
    }

    /**
     * @return string|null
     */
    public function getExternalMethodId(): ?string {
        return $this->_getData(self::KEY_EXTERNAL_METHOD_ID);
    }

    /**
     * @param string $externalMethodId
     * @return $this
     */
    public function setExternalMethodId(string $externalMethodId) {
        $this->setData(self::KEY_EXTERNAL_METHOD_ID, $externalMethodId);
        return $this;
    }

    /**
     * @return string
     */
    public function getShippingMethod(): string {
        return $this->_getData(self::KEY_SHIPPING_METHOD);
    }

    /**
     * @param string $shippingMethod
     * @return $this
     */
    public function setShippingMethod(string $shippingMethod) {
        $this->setData(self::KEY_SHIPPING_METHOD, $shippingMethod);
        return $this;
    }

    /**
     * @return string
     */
    public function getCategoryName(): string {
        return $this->_getData(self::KEY_CATEGORY_NAME);
    }

    /**
     * @param string $categoryName
     * @return $this
     */
    public function setCategoryName(string $categoryName) {
        $this->setData(self::KEY_CATEGORY_NAME, $categoryName);
        return $this;
    }

    public function getLocationId(): ?string {
        return $this->_getData(self::KEY_LOCATION_ID);
    }

    public function getTimeSlotId(): ?string {
        return $this->_getData(self::KEY_TIME_SLOT_ID);
    }

    public function setLocationId(string $locationId) {
        $this->setData(self::KEY_LOCATION_ID, $locationId);
        return $this;
    }

    public function getLocationName(): ?string {
        return $this->_getData(self::KEY_LOCATION_NAME);
    }

    public function setLocationName(string $locationName) {
        $this->setData(self::KEY_LOCATION_NAME, $locationName);
        return $this;
    }

    public function isTest(): bool {
        return (bool) $this->_getData(self::KEY_IS_TEST);
    }

    public function setTest(bool $isTest) {
        $this->setData(self::KEY_IS_TEST, $isTest);
        return $this;
    }

    public function setTimeSlotId(string $timeSlotId) {
        $this->setData(self::KEY_TIME_SLOT_ID, $timeSlotId);
        return $this;
    }

    public function getTimeSlotStart(): ?string {
        return $this->_getData(self::KEY_TIME_SLOT_START);
    }

    public function getTimeSlotEnd(): ?string {
        return $this->_getData(self::KEY_TIME_SLOT_END);
    }

    public function setTimeSlotStart(string $timeSlotTime) {
        $this->setData(self::KEY_TIME_SLOT_START, $timeSlotTime);
        return $this;
    }

    public function setTimeSlotEnd(string $timeSlotEnd) {
        $this->setData(self::KEY_TIME_SLOT_END, $timeSlotEnd);
        return $this;
    }

    public function getCarrier(): ?string {
        return $this->_getData(self::KEY_CARRIER);
    }

    public function setCarrier(string $carrier) {
        $this->setData(self::KEY_CARRIER, $carrier);
        return $this;
    }

    public function getProduct(): ?string {
        return $this->_getData(self::KEY_PRODUCT);
    }

    public function setProduct(string $product) {
        $this->setData(self::KEY_PRODUCT, $product);
        return $this;
    }
}
