<?php
namespace Ingrid\Checkout\Plugin\Dibs\EasyCheckout\Helper;

class Data
{
    protected $_urlBuilder;

    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->_urlBuilder = $urlBuilder;
    }
    
    public function aroundGetCheckoutUrl( \Dibs\EasyCheckout\Helper\Data $subject, callable $proceed, $path = null, $params = [])
    {
        return $this->_urlBuilder->getUrl($subject->getCheckoutPath($path), $params);
    }
}