<?php

namespace Ingrid\Checkout\Block\Catalog\Product;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Psr\Log\LoggerInterface;

/**
 * @api
 * @since 100.0.2
 */
class IngridWidget extends \Magento\Framework\View\Element\Template
{
    const SUPPORTED_LOCALES = ["da-DK", "de-AT", "de-BE", "de-CH", "de-DE", "de-LU", "el-GR", "en-AU", "en-CA", "en-GB", "en-IE", "en-US", "es-ES", "et-EE", "fi-FI", "fi-SE", "fr-BE", "fr-CA", "fr-CH", "fr-FR", "fr-LU", "hr-HR", "hu-HU", "is-IS", "it-CH", "it-IT", "ja-JP", "ko-KR", "lt-LT", "lv-LV", "ms-MY", "nb-NO", "nl-BE", "nl-NL", "nn-NO", "no-NO", "pl-PL", "pt-PT", "ru-RU", "sk-SK", "sl-SI", "sv-FI", "sv-SE", "th-TH", "tr-TR", "vi-VN", "zh-CN"];
    const SUPPORTED_COUNTRIES = ["CZ", "DK", "AT", "BE", "CH", "DE", "LU", "GR", "AU", "CA", "GB", "IE", "US", "ES", "EE", "FI", "SE", "BE", "CA", "CH", "FR", "LU", "HR", "HU", "IS", "CH", "IT", "JP", "KR", "LT", "LV", "MY", "NO", "BE", "NL", "NO", "NO", "PL", "PT", "RU", "SK", "SI", "FI", "SE", "TH", "TR", "VN", "CN"];
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    protected $encryptor;

    private $registry;


    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->encryptor = $encryptor;
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Get API config value
     *
     * @param string $config
     * @param Store  $store
     * @return mixed
     */
    public function getConfig(string $config, $store = null) {
        if (!$store) {
            $store = $this->storeManager->getStore()->getId();
        }
        $scope = $this->getScope($store);
        $websiteId = $this->storeManager->getStore($store)->getWebsite()->getId();
        $resp = $this->scopeConfig->getValue('carriers/ingrid/' . $config, $scope, $websiteId);
        
        return $resp;
    }

    /**
     * Get the scope value of the store
     *
     * @param Store $store
     * @return string
     */
    private function getScope($store = null): string {
        return ScopeInterface::SCOPE_WEBSITE;
    }

    public function isEnabled(): bool {
        return $this->getConfig('product_widget_active') == 1;
    }

    public function isTestMode(): bool {
        return $this->getConfig('test_mode') == 1;
    }


    public function productWidgetHost(): string {
        if ($this->isTestMode()) {
            return 'https://cdn-stage.ingrid.com';
        }
        return 'https://cdn.ingrid.com';
    }

    /**
     * @return string
     * @throws NoApiKeyException
     */
    public function getProductWidgetKey() : string {
        $key = $this->isTestMode() ? $this->getConfig('stage_product_widget_key') : $this->getConfig('prod_product_widget_key');
        
        return $key ? $this->encryptor->decrypt($key) : "";
    }

    public function getProductWidgetUrl(): string {
        return $this->productWidgetHost().'/product-page-widget/bootstrap.js';
    }

    public function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }

    public function getViewedItem(): string
    {
        $currentProduct = $this->getCurrentProduct();
        $viewedItem = [];
        $viewedItem['attributes'] = [];
        $viewedItem['name'] = $currentProduct->getName();
        $viewedItem['price'] = $currentProduct->getFinalPrice() * 100;
        $viewedItem['sku'] = $currentProduct->getSku();

        return json_encode($viewedItem);
    }

    public function getCurrencyCode()
    {
        return $this->storeManager->getStore()->getCurrentCurrency()->getCode();
    }

    public function getCountryCode()
    {
        $countryCode = $this->storeManager->getStore()->getConfig('general/country/default');
        if (in_array($countryCode, self::SUPPORTED_COUNTRIES)) {
            return $countryCode;
        } else {
            return 'US';
        }
    }

    public function getLocale()
    {
        $locale = str_replace("_", "-", $this->storeManager->getStore()->getConfig('general/locale/code'));
        if (in_array($locale, self::SUPPORTED_LOCALES)) {
            return $locale;
        } else {
            return 'en-US';
        }
    }

}
