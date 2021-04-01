<?php

declare(strict_types=1);

namespace Ingrid\Checkout\Model;

use Ingrid\Checkout\Block\Shipping\IngridCheckout;
use Ingrid\Checkout\Helper\Config;
use Ingrid\Checkout\Model\Exception\NoApiKeyException;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\LayoutInterface;
use Psr\Log\LoggerInterface;

class ConfigProvider implements ConfigProviderInterface {

    /**
     * @var LayoutInterface
     */
    protected $_layout;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var UrlInterface
     */
    private $urlBuilder;
    /**
     * @var Config
     */
    private $config;

    public function __construct(LayoutInterface $layout, LoggerInterface $logger, UrlInterface $urlBuilder, Config $config) {
        $this->_layout = $layout;
        $this->logger = $logger;
        $this->urlBuilder = $urlBuilder;
        $this->config = $config;

        $this->logger->debug('ConfigProvider: created');
    }

    public function getConfig() {
        $this->logger->debug('ConfigProvider: getConfig');

        $checkoutHtml = '';
        try {
            $checkoutHtml = $this->_layout->createBlock(IngridCheckout::class)->toHtml();
        } /** @noinspection PhpRedundantCatchClauseInspection */ catch (NoApiKeyException $e) {
            if ($this->config->isMageDevMode()) {
                $checkoutHtml = '<div style="color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: .75rem 1.25rem;">Ingrid error: missing api key</div>';
            }
        } catch (\Exception $e) {
            $this->logger->error('failed to create ingrid checkout block: '.$e->getMessage());
        }
        return [
            'ingrid_checkout_html' => $checkoutHtml,
            'ingrid' => [
                'checkoutUrl' => $this->getUrl('ingrid/api/checkout'),
            ],
        ];
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array  $params
     * @return string
     */
    private function getUrl($route = '', array $params = []) {
        return $this->urlBuilder->getUrl($route, $params);
    }
}
