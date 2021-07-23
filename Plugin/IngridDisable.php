<?php

namespace Ingrid\Checkout\Plugin;

use Magento\Checkout\Block\Checkout\LayoutProcessor;
use Ingrid\Checkout\Helper\Config;

/**
 * Class IngridDisable
 *
 * @package Vendorname\SidebarActivation\Plugin
 */
class IngridDisable {

    /**
     * @var Config
     */
    private $config;
    
    /**
     * IngridDisable constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }


    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $processor
     * @param array $jsLayout
     *
     * @return array
     */
    public function afterProcess(
        LayoutProcessor $processor,
        array $jsLayout
    ){
        if(!$this->isActive()){
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['before-shipping-method-form']['children']['ingrid_checkout']['config']['componentDisabled'] = true;
            $jsLayout['components']['checkout']['children']['sidebar']['children']['ingrid_checkout']['config']['componentDisabled'] = true;
        }

        return $jsLayout;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->config->getConfig('active');
    }
}