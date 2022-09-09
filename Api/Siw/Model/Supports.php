<?php
/**
 * Supports
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
 * Supports Class Doc Comment
 *
 * @category Class
 * @description Contains information about features that a shipping option supports. Configurable in Merchant Admin tool.
 * @package  Ingrid
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class Supports implements ModelInterface, ArrayAccess {
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'Supports';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'search' => 'bool',
        'door_code' => 'bool',
        'courier_instructions' => 'bool',
        'customer_number' => 'bool',    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'search' => 'boolean',
        'door_code' => 'boolean',
        'courier_instructions' => 'boolean',
        'customer_number' => 'boolean',    ];

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
        'search' => 'search',
        'door_code' => 'door_code',
        'courier_instructions' => 'courier_instructions',
        'customer_number' => 'customer_number',    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'search' => 'setSearch',
        'door_code' => 'setDoorCode',
        'courier_instructions' => 'setCourierInstructions',
        'customer_number' => 'setCustomerNumber',    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'search' => 'getSearch',
        'door_code' => 'getDoorCode',
        'courier_instructions' => 'getCourierInstructions',
        'customer_number' => 'getCustomerNumber',    ];

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
        $this->container['search'] = isset($data['search']) ? $data['search'] : null;
        $this->container['door_code'] = isset($data['door_code']) ? $data['door_code'] : null;
        $this->container['courier_instructions'] = isset($data['courier_instructions']) ? $data['courier_instructions'] : null;
        $this->container['customer_number'] = isset($data['customer_number']) ? $data['customer_number'] : null;
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
     * Gets search
     *
     * @return bool
     */
    public function getSearch() {
        return $this->container['search'];
    }

    /**
     * Sets search
     *
     * @param bool $search search
     *
     * @return $this
     */
    public function setSearch($search) {
        $this->container['search'] = $search;

        return $this;
    }

    /**
     * Gets door_code
     *
     * @return bool
     */
    public function getDoorCode() {
        return $this->container['door_code'];
    }

    /**
     * Sets door_code
     *
     * @param bool $door_code Indicates if shipping option supports door code. If true, a door code input will be displayed in the shipping selector widget. Only applicable for home delivery type.
     *
     * @return $this
     */
    public function setDoorCode($door_code) {
        $this->container['door_code'] = $door_code;

        return $this;
    }

    /**
     * Gets courier_instructions
     *
     * @return bool
     */
    public function getCourierInstructions() {
        return $this->container['courier_instructions'];
    }

    /**
     * Sets courier_instructions
     *
     * @param bool $courier_instructions Indicates if shipping option supports courier notes. If true, a courier instructions input will be displayed in the shipping selector widget. Only applicable for home delivery type.
     *
     * @return $this
     */
    public function setCourierInstructions($courier_instructions) {
        $this->container['courier_instructions'] = $courier_instructions;

        return $this;
    }

    /**
     * Gets customer_number
     *
     * @return bool
     */
    public function getCustomerNumber() {
        return $this->container['customer_number'];
    }

    /**
     * Sets customer_number
     *
     * @param bool $customer_number Indicates if shipping option supports carrier specific customer number. If true, a carrier number input will be displayed in the shipping selector widget. Only applicable for DHL Germany at this moment, where customer has to provide a number that identifies a pickup locker.
     *
     * @return $this
     */
    public function setCustomerNumber($customer_number) {
        $this->container['customer_number'] = $customer_number;

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
