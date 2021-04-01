<?php

declare(strict_types=1);

namespace Ingrid\Checkout\Block\Shipping;

use Ingrid\Checkout\Service\IngridSessionService;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Psr\Log\LoggerInterface;

class IngridCheckout extends Template {

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
        array $data = []
    ) {
        $this->httpContext = $httpContext;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
        $this->logger = $logger;

        $logger->debug('checkout block init');
        $this->sessionService = $sessionService;
    }

    public function getCheckoutHtml() {
        $this->logger->debug('block::getCheckoutHtml');
        return $this->sessionService->sessionHtmlForCheckout();
    }
}
