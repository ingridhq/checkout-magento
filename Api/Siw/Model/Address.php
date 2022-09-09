<?php
/**
 * Address
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
 * Address Class Doc Comment
 *
 * @category Class
 * @description Common address entity that used almost everywhere in Ingrid&#x27;s API.
 * @package  Ingrid
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class Address implements ModelInterface, ArrayAccess {
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'Address';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'name' => 'string',
        'care_of' => 'string',
        'attn' => 'string',
        'address_lines' => 'string[]',
        'city' => 'string',
        'region' => 'string',
        'postal_code' => 'string',
        'country' => 'string',
        'coordinates' => '\Ingrid\Checkout\Api\Siw\Model\Coordinates',
        'door_code' => 'string',    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'name' => null,
        'care_of' => null,
        'attn' => null,
        'address_lines' => null,
        'city' => null,
        'region' => null,
        'postal_code' => null,
        'country' => null,
        'coordinates' => null,
        'door_code' => null,    ];

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
        'name' => 'name',
        'care_of' => 'care_of',
        'attn' => 'attn',
        'address_lines' => 'address_lines',
        'city' => 'city',
        'region' => 'region',
        'postal_code' => 'postal_code',
        'country' => 'country',
        'coordinates' => 'coordinates',
        'door_code' => 'door_code',    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'name' => 'setName',
        'care_of' => 'setCareOf',
        'attn' => 'setAttn',
        'address_lines' => 'setAddressLines',
        'city' => 'setCity',
        'region' => 'setRegion',
        'postal_code' => 'setPostalCode',
        'country' => 'setCountry',
        'coordinates' => 'setCoordinates',
        'door_code' => 'setDoorCode',    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'name' => 'getName',
        'care_of' => 'getCareOf',
        'attn' => 'getAttn',
        'address_lines' => 'getAddressLines',
        'city' => 'getCity',
        'region' => 'getRegion',
        'postal_code' => 'getPostalCode',
        'country' => 'getCountry',
        'coordinates' => 'getCoordinates',
        'door_code' => 'getDoorCode',    ];

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
        $this->container['name'] = isset($data['name']) ? $data['name'] : null;
        $this->container['care_of'] = isset($data['care_of']) ? $data['care_of'] : null;
        $this->container['attn'] = isset($data['attn']) ? $data['attn'] : null;
        $this->container['address_lines'] = isset($data['address_lines']) ? $data['address_lines'] : null;
        $this->container['city'] = isset($data['city']) ? $data['city'] : null;
        $this->container['region'] = isset($data['region']) ? $data['region'] : null;
        $this->container['postal_code'] = isset($data['postal_code']) ? $data['postal_code'] : null;
        $this->container['country'] = isset($data['country']) ? $data['country'] : null;
        $this->container['coordinates'] = isset($data['coordinates']) ? $data['coordinates'] : null;
        $this->container['door_code'] = isset($data['door_code']) ? $data['door_code'] : null;
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
     * Gets name
     *
     * @return string
     */
    public function getName() {
        return $this->container['name'];
    }

    /**
     * Sets name
     *
     * @param string $name name
     *
     * @return $this
     */
    public function setName($name) {
        $this->container['name'] = $name;

        return $this;
    }

    /**
     * Gets care_of
     *
     * @return string
     */
    public function getCareOf() {
        return $this->container['care_of'];
    }

    /**
     * Sets care_of
     *
     * @param string $care_of care_of
     *
     * @return $this
     */
    public function setCareOf($care_of) {
        $this->container['care_of'] = $care_of;

        return $this;
    }

    /**
     * Gets attn
     *
     * @return string
     */
    public function getAttn() {
        return $this->container['attn'];
    }

    /**
     * Sets attn
     *
     * @param string $attn attn
     *
     * @return $this
     */
    public function setAttn($attn) {
        $this->container['attn'] = $attn;

        return $this;
    }

    /**
     * Gets address_lines
     *
     * @return string[]
     */
    public function getAddressLines() {
        return $this->container['address_lines'];
    }

    /**
     * Sets address_lines
     *
     * @param string[] $address_lines address_lines
     *
     * @return $this
     */
    public function setAddressLines($address_lines) {
        $this->container['address_lines'] = $address_lines;

        return $this;
    }

    /**
     * Gets city
     *
     * @return string
     */
    public function getCity() {
        return $this->container['city'];
    }

    /**
     * Sets city
     *
     * @param string $city city
     *
     * @return $this
     */
    public function setCity($city) {
        $this->container['city'] = $city;

        return $this;
    }

    /**
     * Gets region
     *
     * @return string
     */
    public function getRegion() {
        return $this->container['region'];
    }

    /**
     * Sets region
     *
     * @param string $region region
     *
     * @return $this
     */
    public function setRegion($region) {
        $this->container['region'] = $region;

        return $this;
    }

    /**
     * Gets postal_code
     *
     * @return string
     */
    public function getPostalCode() {
        return $this->container['postal_code'];
    }

    /**
     * Sets postal_code
     *
     * @param string $postal_code postal_code
     *
     * @return $this
     */
    public function setPostalCode($postal_code) {
        $this->container['postal_code'] = $postal_code;

        return $this;
    }

    /**
     * Gets country
     *
     * @return string
     */
    public function getCountry() {
        return $this->container['country'];
    }

    /**
     * Sets country
     *
     * @param string $country Country should be specified as two uppercase letters (ISO Alpha-2). Example `SE` for Sweden, `ES` for Spain.
     *
     * @return $this
     */
    public function setCountry($country) {
        $this->container['country'] = mb_strtoupper($country, 'utf-8');

        return $this;
    }

    /**
     * Gets coordinates
     *
     * @return \Ingrid\Checkout\Api\Siw\Model\Coordinates
     */
    public function getCoordinates() {
        return $this->container['coordinates'];
    }

    /**
     * Sets coordinates
     *
     * @param \Ingrid\Checkout\Api\Siw\Model\Coordinates $coordinates coordinates
     *
     * @return $this
     */
    public function setCoordinates($coordinates) {
        $this->container['coordinates'] = $coordinates;

        return $this;
    }

    /**
     * Gets door_code
     *
     * @return string
     */
    public function getDoorCode() {
        return $this->container['door_code'];
    }

    /**
     * Sets door_code
     *
     * @param string $door_code door_code
     *
     * @return $this
     */
    public function setDoorCode($door_code) {
        $this->container['door_code'] = $door_code;

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
