<?php

declare(strict_types=1);

namespace Ingrid\Checkout\Block\Shipping;

use Ingrid\Checkout\Service\IngridSessionService;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Psr\Log\LoggerInterface;
use Ingrid\Checkout\Helper\Config;
use Ingrid\Checkout\Model\ConfigProvider;
use Magento\Csp\Helper\CspNonceProvider;

class IngridCheckout extends Template {

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var string
     */
    protected $_template = 'Ingrid_Checkout::checkout/ingrid-checkout.phtml';

    /**
     * @var HttpContext
     */
    protected $httpContext;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var IngridSessionService
     */
    private $sessionService;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var ConfigProvider
    */
    private $configProvider;

    /**
     * @var CspNonceProvider
     */
    private $cspNonceProvider;

    /**
     * Ingrid Checkout block
     * @param TemplateContext $context
     * @param HttpContext $httpContext
     * @param LoggerInterface $logger
     * @param IngridSessionService $sessionService
     * @param array $data
     */
    public function __construct(
        TemplateContext $context,
        HttpContext $httpContext,
        LoggerInterface $logger,
        IngridSessionService $sessionService,
        Config $config,
        ConfigProvider $configProvider,
        CspNonceProvider $cspNonceProvider,
        array $data = []
    ) {
        $this->httpContext = $httpContext;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
        $this->logger = $logger;

        $logger->debug('checkout block init');
        $this->sessionService = $sessionService;
        $this->config = $config;
        $this->configProvider = $configProvider;
        $this->cspNonceProvider = $cspNonceProvider;
    }

    public function getCheckoutHtml() {
        $this->logger->debug('block::getCheckoutHtml');
        $htmlForCheckout = $this->sessionService->sessionHtmlForCheckout();
        $nonce = $this->cspNonceProvider->generateNonce();
        $htmlForCheckout = preg_replace('/<script(.*?)>/i', '<script$1 nonce="' . $nonce . '">', $htmlForCheckout);
        return $htmlForCheckout;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->config->getConfig('active');
    }

    /**
     * Get widget config to be included in custom checkout
     *
     * @return false|string
     */
    public function getWidgetConfigJson()
    {
        $checkoutConfig = $this->configProvider->getConfig();
        return \json_encode($checkoutConfig) ?: '{}';
    }
}
