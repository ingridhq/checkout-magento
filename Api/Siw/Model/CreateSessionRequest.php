<?php
/**
 * CreateSessionRequest
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
 * CreateSessionRequest Class Doc Comment
 *
 * @category Class
 * @description Contains the required information to initialize a new session.
 * @package  Ingrid
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class CreateSessionRequest implements ModelInterface, ArrayAccess {
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'CreateSessionRequest';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'purchase_country' => 'string',
        'purchase_currency' => 'string',
        'locale' => 'string',
        'customer_info' => '\Ingrid\Checkout\Api\Siw\Model\CustomerInfo',
        'cart' => '\Ingrid\Checkout\Api\Siw\Model\Cart',
        'external_id' => 'string',
        'customer' => '\Ingrid\Checkout\Api\Siw\Model\CustomerInfo',
        'search_address' => '\Ingrid\Checkout\Api\Siw\Model\Address',
        'prefill_delivery_address' => '\Ingrid\Checkout\Api\Siw\Model\Address',
        'locales' => 'string[]',
        'meta' => 'map[string,string]',
        'snippet_configuration' => '\Ingrid\Checkout\Api\Siw\Model\SnippetConfiguration',    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'purchase_country' => null,
        'purchase_currency' => null,
        'locale' => null,
        'customer_info' => null,
        'cart' => null,
        'external_id' => null,
        'customer' => null,
        'search_address' => null,
        'prefill_delivery_address' => null,
        'locales' => null,
        'meta' => null,
        'snippet_configuration' => null,    ];

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
        'purchase_country' => 'purchase_country',
        'purchase_currency' => 'purchase_currency',
        'locale' => 'locale',
        'customer_info' => 'customer_info',
        'cart' => 'cart',
        'external_id' => 'external_id',
        'customer' => 'customer',
        'search_address' => 'search_address',
        'prefill_delivery_address' => 'prefill_delivery_address',
        'locales' => 'locales',
        'meta' => 'meta',
        'snippet_configuration' => 'snippet_configuration',    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'purchase_country' => 'setPurchaseCountry',
        'purchase_currency' => 'setPurchaseCurrency',
        'locale' => 'setLocale',
        'customer_info' => 'setCustomerInfo',
        'cart' => 'setCart',
        'external_id' => 'setExternalId',
        'customer' => 'setCustomer',
        'search_address' => 'setSearchAddress',
        'prefill_delivery_address' => 'setPrefillDeliveryAddress',
        'locales' => 'setLocales',
        'meta' => 'setMeta',
        'snippet_configuration' => 'setSnippetConfiguration',    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'purchase_country' => 'getPurchaseCountry',
        'purchase_currency' => 'getPurchaseCurrency',
        'locale' => 'getLocale',
        'customer_info' => 'getCustomerInfo',
        'cart' => 'getCart',
        'external_id' => 'getExternalId',
        'customer' => 'getCustomer',
        'search_address' => 'getSearchAddress',
        'prefill_delivery_address' => 'getPrefillDeliveryAddress',
        'locales' => 'getLocales',
        'meta' => 'getMeta',
        'snippet_configuration' => 'getSnippetConfiguration',    ];

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
        $this->container['purchase_country'] = isset($data['purchase_country']) ? $data['purchase_country'] : null;
        $this->container['purchase_currency'] = isset($data['purchase_currency']) ? $data['purchase_currency'] : null;
        $this->container['locale'] = isset($data['locale']) ? $data['locale'] : null;
        $this->container['customer_info'] = isset($data['customer_info']) ? $data['customer_info'] : null;
        $this->container['cart'] = isset($data['cart']) ? $data['cart'] : null;
        $this->container['external_id'] = isset($data['external_id']) ? $data['external_id'] : null;
        $this->container['customer'] = isset($data['customer']) ? $data['customer'] : null;
        $this->container['search_address'] = isset($data['search_address']) ? $data['search_address'] : null;
        $this->container['prefill_delivery_address'] = isset($data['prefill_delivery_address']) ? $data['prefill_delivery_address'] : null;
        $this->container['locales'] = isset($data['locales']) ? $data['locales'] : null;
        $this->container['meta'] = isset($data['meta']) ? $data['meta'] : null;
        $this->container['snippet_configuration'] = isset($data['snippet_configuration']) ? $data['snippet_configuration'] : null;
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
     * Gets purchase_country
     *
     * @return string
     */
    public function getPurchaseCountry() {
        return $this->container['purchase_country'];
    }

    /**
     * Sets purchase_country
     *
     * @param string $purchase_country purchase_country
     *
     * @return $this
     */
    public function setPurchaseCountry($purchase_country) {
        $this->container['purchase_country'] = $purchase_country;

        return $this;
    }

    /**
     * Gets purchase_currency
     *
     * @return string
     */
    public function getPurchaseCurrency() {
        return $this->container['purchase_currency'];
    }

    /**
     * Sets purchase_currency
     *
     * @param string $purchase_currency purchase_currency
     *
     * @return $this
     */
    public function setPurchaseCurrency($purchase_currency) {
        $this->container['purchase_currency'] = $purchase_currency;

        return $this;
    }

    /**
     * Gets locale
     *
     * @return string
     */
    public function getLocale() {
        return $this->container['locale'];
    }

    /**
     * Sets locale
     *
     * @param string $locale locale
     *
     * @return $this
     */
    public function setLocale($locale) {
        $this->container['locale'] = $locale;

        return $this;
    }

    /**
     * Gets customer_info
     *
     * @return \Ingrid\Checkout\Api\Siw\Model\CustomerInfo
     */
    public function getCustomerInfo() {
        return $this->container['customer_info'];
    }

    /**
     * Sets customer_info
     *
     * @param \Ingrid\Checkout\Api\Siw\Model\CustomerInfo $customer_info customer_info
     *
     * @return $this
     */
    public function setCustomerInfo($customer_info) {
        $this->container['customer_info'] = $customer_info;

        return $this;
    }

    /**
     * Gets cart
     *
     * @return \Ingrid\Checkout\Api\Siw\Model\Cart
     */
    public function getCart() {
        return $this->container['cart'];
    }

    /**
     * Sets cart
     *
     * @param \Ingrid\Checkout\Api\Siw\Model\Cart $cart cart
     *
     * @return $this
     */
    public function setCart($cart) {
        $this->container['cart'] = $cart;

        return $this;
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
     * @param string $external_id external_id
     *
     * @return $this
     */
    public function setExternalId($external_id) {
        $this->container['external_id'] = $external_id;

        return $this;
    }

    /**
     * Gets customer
     *
     * @return \Ingrid\Checkout\Api\Siw\Model\CustomerInfo
     */
    public function getCustomer() {
        return $this->container['customer'];
    }

    /**
     * Sets customer
     *
     * @param \Ingrid\Checkout\Api\Siw\Model\CustomerInfo $customer customer
     *
     * @return $this
     */
    public function setCustomer($customer) {
        $this->container['customer'] = $customer;

        return $this;
    }

    /**
     * Gets search_address
     *
     * @return \Ingrid\Checkout\Api\Siw\Model\Address
     */
    public function getSearchAddress() {
        return $this->container['search_address'];
    }

    /**
     * Sets search_address
     *
     * @param \Ingrid\Checkout\Api\Siw\Model\Address $search_address search_address
     *
     * @return $this
     */
    public function setSearchAddress($search_address) {
        $this->container['search_address'] = $search_address;

        return $this;
    }

    /**
     * Gets prefill_delivery_address
     *
     * @return bool
     */
    public function getPrefillDeliveryAddress() {
        return $this->container['prefill_delivery_address'];
    }

    /**
     * Sets prefill_delivery_address
     *
     * @param bool $prefill_delivery_address prefill_delivery_address
     *
     * @return $this
     */
    public function setPrefillDeliveryAddress($prefill_delivery_address) {
        $this->container['prefill_delivery_address'] = $prefill_delivery_address;

        return $this;
    }

    /**
     * Gets locales
     *
     * @return string[]
     */
    public function getLocales() {
        return $this->container['locales'];
    }

    /**
     * Sets locales
     *
     * @param string[] $locales locales
     *
     * @return $this
     */
    public function setLocales($locales) {
        $this->container['locales'] = $locales;

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
     * @param map[string,string] $meta Generic key/value object that is used for supplying complementing information.
     *
     * @return $this
     */
    public function setMeta($meta) {
        $this->container['meta'] = $meta;

        return $this;
    }

    /**
     * Gets snippet_configuration
     *
     * @return \Ingrid\Checkout\Api\Siw\Model\SnippetConfiguration
     */
    public function getSnippetConfiguration() {
        return $this->container['snippet_configuration'];
    }

    /**
     * Sets snippet_configuration
     *
     * @param \Ingrid\Checkout\Api\Siw\Model\SnippetConfiguration $snippet_configuration snippet_configuration
     *
     * @return $this
     */
    public function setSnippetConfiguration($snippet_configuration) {
        $this->container['snippet_configuration'] = $snippet_configuration;

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
