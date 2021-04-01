<?php

namespace Ingrid\Checkout\Helper;

use Ingrid\Checkout\Model\Exception\NoApiKeyException;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\State as MageState;
use Magento\Framework\Locale\Resolver;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Psr\Log\LoggerInterface;

class Config extends AbstractHelper {
    const MODULE_NAME = 'Ingrid_Checkout';

    /**
     * Payment method
     *
     * @var string
     */
    private $code = '';
    /**
     * Observer event prefix
     *
     * @var string
     */
    private $eventPrefix = '';
    /**
     * @var Resolver
     */
    private $localeResolver;
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;
    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;
    /**
     * @var DirectoryHelper
     */
    private $directoryHelper;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var MageState
     */
    private $mageState;

    /**
     * ConfigHelper constructor.
     *
     * @param Context                     $context
     * @param Resolver                    $resolver
     * @param CustomerRepositoryInterface $customerRepository
     * @param AddressRepositoryInterface  $addressRepository
     * @param string                      $code
     * @param string                      $eventPrefix
     */
    public function __construct(
        Context $context,
        Resolver $localeResolver,
        DirectoryHelper $directoryHelper,
        CustomerRepositoryInterface $customerRepository,
        AddressRepositoryInterface $addressRepository,
        LoggerInterface $logger,
        MageState $mageState,
        $code = 'ingrid_checkout',
        $eventPrefix = 'ingrid'
    ) {
        parent::__construct($context);
        $this->localeResolver = $localeResolver;
        $this->code = $code;
        $this->eventPrefix = $eventPrefix;
        $this->customerRepository = $customerRepository;
        $this->addressRepository = $addressRepository;
        $this->directoryHelper = $directoryHelper;
        $this->logger = $logger;
        $this->mageState = $mageState;
    }

    /**
     * Get API config value
     *
     * @param string $config
     * @param Store  $store
     * @return mixed
     */
    public function getConfig(string $config, $store = null) {
        $scope = $this->getScope($store);
        $resp = $this->scopeConfig->getValue('carriers/ingrid/'.$config, $scope, $store);
        return $resp;
    }

    /**
     * Get the scope value of the store
     *
     * @param Store $store
     * @return string
     */
    private function getScope($store = null): string {
        if ($store === null) {
            return ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        }
        return ScopeInterface::SCOPE_STORES;
    }

    public function isTestMode(): bool {
        return $this->getConfig('test_mode') == 1;
    }

    /**
     * @return string
     * @throws NoApiKeyException
     */
    public function getApiKey() : string {
        $key = $this->isTestMode() ? $this->getConfig('stage_api_key') : $this->getConfig('prod_api_key');
        if (empty($key)) {
            $this->logger->critical('api key is required');
            throw new NoApiKeyException();
        }
        return $key;
    }

    public function isMageDevMode(): bool {
        return $this->mageState->getMode() === MageState::MODE_DEVELOPER;
    }

    /**
     * @return string
     * @throws NoApiKeyException
     */
    public function encodedApiKey(): string {
        $key = $this->getApiKey();
        return base64_encode($key);
    }

    public function apiHost(): string {
        if ($this->isTestMode()) {
            return 'https://api-stage.ingrid.com';
        }
        return 'https://api.ingrid.com';
    }

    public function siwBaseUrl(): string {
        return $this->apiHost().'/v1/siw';
    }

    /**
     * Get locale code
     *
     * @return string
     */
    public function getLocale() : string {
        return str_replace('_', '-', $this->localeResolver->getLocale());
    }

    /**
     * @param Store $store
     * @return string
     */
    public function getPurchaseCountry($store) : string {
        return $this->directoryHelper->getDefaultCountry($store);
    }

//    /**
//     * Get base currency for store
//     *
//     * @param Store $store
//     * @return string
//     */
//    public function getBaseCurrencyCode($store = null) : string {
//        $scope = $this->getScope($store);
//        return $this->scopeConfig->getValue('currency/options/base', $scope, $store);
//    }

    /**
     * @param Store $store
     * @return string one of 'lbs' or 'kg'
     */
    public function weightUnit(Store $store): string {
        return $this->storeConfig($store, 'general/locale/weight_unit');
    }

    /**
     * @param Store $store
     * @param string $key
     * @return mixed
     */
    private function storeConfig(Store $store, string $key) {
        $scope = $this->getScope($store);
        return $this->scopeConfig->getValue($key, $scope, $store);
    }

    public function fallbackShippingMethod(Store $store): string {
        return $this->getConfig('fallback_id', $store);
    }

    public function fallbackName(Store $store): string {
        return $this->getConfig('fallback_name', $store);
    }
}
