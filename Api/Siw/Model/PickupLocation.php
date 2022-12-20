<?php
/**
 * PickupLocation
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
 * PickupLocation Class Doc Comment
 *
 * @category Class
 * @description Contains information about the pickup service point as returned by the carrier.
 * @package  Ingrid
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class PickupLocation implements ModelInterface, ArrayAccess {
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'PickupLocation';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'external_id' => 'string',
        'name' => 'string',
        'address' => '\Ingrid\Checkout\Api\Siw\Model\Address',
        'distance' => '\Ingrid\Checkout\Api\Siw\Model\Distance',
        'operational_hours' => '\Ingrid\Checkout\Api\Siw\Model\OperationalHours',
        'meta' => 'map[string,string]',
        'location_type' => '\Ingrid\Checkout\Api\Siw\Model\PickupLocationType',
        'sections' => '\Ingrid\Checkout\Api\Siw\Model\Section[]',
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'external_id' => null,
        'name' => null,
        'address' => null,
        'distance' => null,
        'operational_hours' => null,
        'meta' => null,
        'location_type' => null,
        'sections' => null,
    ];

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
        'external_id' => 'external_id',
        'name' => 'name',
        'address' => 'address',
        'distance' => 'distance',
        'operational_hours' => 'operational_hours',
        'meta' => 'meta',
        'location_type' => 'location_type',
        'sections' => 'sections',
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'external_id' => 'setExternalId',
        'name' => 'setName',
        'address' => 'setAddress',
        'distance' => 'setDistance',
        'operational_hours' => 'setOperationalHours',
        'meta' => 'setMeta',
        'location_type' => 'setLocationType',
        'sections' => 'setSections',
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'external_id' => 'getExternalId',
        'name' => 'getName',
        'address' => 'getAddress',
        'distance' => 'getDistance',
        'operational_hours' => 'getOperationalHours',
        'meta' => 'getMeta',
        'location_type' => 'getLocationType',
        'sections' => 'getSections',
    ];

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
        $this->container['external_id'] = isset($data['external_id']) ? $data['external_id'] : null;
        $this->container['name'] = isset($data['name']) ? $data['name'] : null;
        $this->container['address'] = isset($data['address']) ? $data['address'] : null;
        $this->container['distance'] = isset($data['distance']) ? $data['distance'] : null;
        $this->container['operational_hours'] = isset($data['operational_hours']) ? $data['operational_hours'] : null;
        $this->container['meta'] = isset($data['meta']) ? $data['meta'] : null;
        $this->container['location_type'] = isset($data['location_type']) ? $data['location_type'] : null;
        $this->container['sections'] = isset($data['sections']) ? $data['sections'] : null;
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
     * Gets external_id
     *
     * @return string
     */
    public function getExternalId() {
        return $this->container['external_id'];
    }

    /**
     * Sets external_id
     *
     * @param string $external_id Carrier specific ID of the service point location returned by the carrier.
     *
     * @return $this
     */
    public function setExternalId($external_id) {
        $this->container['external_id'] = $external_id;

        return $this;
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
     * Gets address
     *
     * @return \Ingrid\Checkout\Api\Siw\Model\Address
     */
    public function getAddress() {
        return $this->container['address'];
    }

    /**
     * Sets address
     *
     * @param \Ingrid\Checkout\Api\Siw\Model\Address $address address
     *
     * @return $this
     */
    public function setAddress($address) {
        $this->container['address'] = $address;

        return $this;
    }

    /**
     * Gets distance
     *
     * @return \Ingrid\Checkout\Api\Siw\Model\Distance
     */
    public function getDistance() {
        return $this->container['distance'];
    }

    /**
     * Sets distance
     *
     * @param \Ingrid\Checkout\Api\Siw\Model\Distance $distance distance
     *
     * @return $this
     */
    public function setDistance($distance) {
        $this->container['distance'] = $distance;

        return $this;
    }

    /**
     * Gets operational_hours
     *
     * @return \Ingrid\Checkout\Api\Siw\Model\OperationalHours
     */
    public function getOperationalHours() {
        return $this->container['operational_hours'];
    }

    /**
     * Sets operational_hours
     *
     * @param \Ingrid\Checkout\Api\Siw\Model\OperationalHours $operational_hours operational_hours
     *
     * @return $this
     */
    public function setOperationalHours($operational_hours) {
        $this->container['operational_hours'] = $operational_hours;

        return $this;
    }

    /**
     * Gets meta
     *
     * @return map[string,string]
     */
    public function getMeta() {
        return $this->container['meta'];
    }

    /**
     * Sets meta
     *
     * @param map[string,string] $meta meta
     *
     * @return $this
     */
    public function setMeta($meta) {
        $this->container['meta'] = $meta;

        return $this;
    }

    /**
     * Gets location_type
     *
     * @return \Ingrid\Checkout\Api\Siw\Model\PickupLocationType
     */
    public function getLocationType() {
        return $this->container['location_type'];
    }

    /**
     * Sets location_type
     *
     * @param \Ingrid\Checkout\Api\Siw\Model\PickupLocationType $location_type location_type
     *
     * @return $this
     */
    public function setLocationType($location_type) {
        $this->container['location_type'] = $location_type;

        return $this;
    }

    /**
     * Gets sections
     *
     * @return \Ingrid\Checkout\Api\Siw\Model\Section[]
     */
    public function getSections() {
        return $this->container['sections'];
    }

    /**
     * Sets sections
     *
     * @param \Ingrid\Checkout\Api\Siw\Model\Section[] $sections sections
     *
     * @return $this
     */
    public function setSections($sections) {
        $this->container['sections'] = $sections;

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
