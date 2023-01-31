<?php
/**
 * Result
 *
 * PHP version 5
 *
 * @category Class
 * @package  Ingrid
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * SIW API
 *
 * No description provided (generated by Swagger Codegen https://github.com/swagger-api/swagger-codegen)
 *
 * OpenAPI spec version: 1.0
 *
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 * Swagger Codegen version: 3.0.21
 */
/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace Ingrid\Checkout\Api\Siw\Model;

use ArrayAccess;
use Ingrid\Checkout\Api\Siw\ObjectSerializer;

/**
 * Result Class Doc Comment
 *
 * @category Class
 * @description Summary of the shipment.
 * @package  Ingrid
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class DeliveryGroupsItem implements ModelInterface, ArrayAccess {
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'DeliveryGroups';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'shipping' => '\Ingrid\Checkout\Api\Siw\Model\ResultShipping',
        'category' => '\Ingrid\Checkout\Api\Siw\Model\ResultCategory',
        'pricing' => '\Ingrid\Checkout\Api\Siw\Model\ResultPricing',
        'selection' => '\Ingrid\Checkout\Api\Siw\Model\ResultSelection',
        'delivery_time' => '\Ingrid\Checkout\Api\Siw\Model\ResultDeliveryTime',    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'shipping' => null,
        'category' => null,
        'pricing' => null,
        'selection' => null,
        'delivery_time' => null,    ];

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerTypes() {
        return self::$swaggerTypes;
    }

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerFormats() {
        return self::$swaggerFormats;
    }

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    protected static $attributeMap = [
        'shipping' => 'shipping',
        'category' => 'category',
        'pricing' => 'pricing',
        'selection' => 'selection',
        'delivery_time' => 'delivery_time',    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'shipping' => 'setShipping',
        'category' => 'setCategory',
        'pricing' => 'setPricing',
        'selection' => 'setSelection',
        'delivery_time' => 'setDeliveryTime',    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'shipping' => 'getShipping',
        'category' => 'getCategory',
        'pricing' => 'getPricing',
        'selection' => 'getSelection',
        'delivery_time' => 'getDeliveryTime',    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @return array
     */
    public static function attributeMap() {
        return self::$attributeMap;
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @return array
     */
    public static function setters() {
        return self::$setters;
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @return array
     */
    public static function getters() {
        return self::$getters;
    }

    /**
     * The original name of the model.
     *
     * @return string
     */
    public function getModelName() {
        return self::$swaggerModelName;
    }

    /**
     * Associative array for storing property values
     *
     * @var mixed[]
     */
    protected $container = [];

    /**
     * Constructor
     *
     * @param mixed[] $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(array $data = null) {
        $this->container['shipping'] = isset($data['shipping']) ? $data['shipping'] : null;
        $this->container['category'] = isset($data['category']) ? $data['category'] : null;
        $this->container['pricing'] = isset($data['pricing']) ? $data['pricing'] : null;
        $this->container['selection'] = isset($data['selection']) ? $data['selection'] : null;
        $this->container['delivery_time'] = isset($data['delivery_time']) ? $data['delivery_time'] : null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties() {
        $invalidProperties = [];

        return $invalidProperties;
    }

    /**
     * Validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properties are valid
     */
    public function valid() {
        return count($this->listInvalidProperties()) === 0;
    }

    /**
     * Gets shipping
     *
     * @return \Ingrid\Checkout\Api\Siw\Model\ResultShipping
     */
    public function getShipping() {
        return $this->container['shipping'];
    }

    /**
     * Sets shipping
     *
     * @param \Ingrid\Checkout\Api\Siw\Model\ResultShipping $shipping shipping
     *
     * @return $this
     */
    public function setShipping($shipping) {
        $this->container['shipping'] = $shipping;

        return $this;
    }

    /**
     * Gets category
     *
     * @return \Ingrid\Checkout\Api\Siw\Model\ResultCategory
     */
    public function getCategory() {
        return $this->container['category'];
    }

    /**
     * Sets category
     *
     * @param \Ingrid\Checkout\Api\Siw\Model\ResultCategory $category category
     *
     * @return $this
     */
    public function setCategory($category) {
        $this->container['category'] = $category;

        return $this;
    }

    /**
     * Gets pricing
     *
     * @return \Ingrid\Checkout\Api\Siw\Model\ResultPricing
     */
    public function getPricing() {
        return $this->container['pricing'];
    }

    /**
     * Sets pricing
     *
     * @param \Ingrid\Checkout\Api\Siw\Model\ResultPricing $pricing pricing
     *
     * @return $this
     */
    public function setPricing($pricing) {
        $this->container['pricing'] = $pricing;

        return $this;
    }

    /**
     * Gets selection
     *
     * @return \Ingrid\Checkout\Api\Siw\Model\ResultSelection
     */
    public function getSelection() {
        return $this->container['selection'];
    }

    /**
     * Sets selection
     *
     * @param \Ingrid\Checkout\Api\Siw\Model\ResultSelection $selection selection
     *
     * @return $this
     */
    public function setSelection($selection) {
        $this->container['selection'] = $selection;

        return $this;
    }

    /**
     * Gets delivery_time
     *
     * @return \Ingrid\Checkout\Api\Siw\Model\ResultDeliveryTime
     */
    public function getDeliveryTime() {
        return $this->container['delivery_time'];
    }

    /**
     * Sets delivery_time
     *
     * @param \Ingrid\Checkout\Api\Siw\Model\ResultDeliveryTime $delivery_time delivery_time
     *
     * @return $this
     */
    public function setDeliveryTime($delivery_time) {
        $this->container['delivery_time'] = $delivery_time;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     *
     * @param integer $offset Offset
     *
     * @return boolean
     */
    public function offsetExists($offset): bool {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     *
     * @param integer $offset Offset
     *
     * @return mixed
     */
    public function offsetGet($offset): mixed {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    /**
     * Sets value based on offset.
     *
     * @param integer $offset Offset
     * @param mixed   $value  Value to be set
     *
     * @return void
     */
    public function offsetSet($offset, $value): void {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     *
     * @param integer $offset Offset
     *
     * @return void
     */
    public function offsetUnset($offset): void {
        unset($this->container[$offset]);
    }

    /**
     * Gets the string presentation of the object
     *
     * @return string
     */
    public function __toString() {
        if (defined('JSON_PRETTY_PRINT')) { // use JSON pretty print
            return json_encode(
                ObjectSerializer::sanitizeForSerialization($this),
                JSON_PRETTY_PRINT
            );
        }

        return json_encode(ObjectSerializer::sanitizeForSerialization($this));
    }
}