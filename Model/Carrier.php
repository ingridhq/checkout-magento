<?php
declare(strict_types=1);

namespace Ingrid\Checkout\Model;

use Ingrid\Checkout\Helper\Config;
use Ingrid\Checkout\Model\Exception\NoQuoteException;
use Ingrid\Checkout\Service\IngridSessionService;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Rate\ResultFactory;
use Psr\Log\LoggerInterface;

class Carrier extends AbstractCarrier implements CarrierInterface {

    /**
     * {@inheritDoc}
     */
    protected $_code = 'ingrid';

    /**
     * {@inheritDoc}
     */
    protected $_isFixed = false;

    /**
     * @var ResultFactory
     */
    private $rateResultFactory;

    /**
     * @var MethodFactory
     */
    private $rateMethodFactory;
    /**
     * @var IngridSessionService
     */
    private $sessionProvider;

    /**
     * @var LoggerInterface
     */
    protected $_logger;
    /**
     * @var Config
     */
    private $config;
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * Carrier constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param IngridSessionService $sessionService
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        IngridSessionService $sessionService,
        Config $config,
        CheckoutSession $checkoutSession,
        array $data = []
    ) {
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);

        $this->sessionProvider = $sessionService;
        $this->config = $config;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Custom Shipping Rates Collector
     *
     * @param RateRequest $request
     * @return Result|bool
     */
    public function collectRates(RateRequest $request) {
        if (!$this->getConfigFlag('active')) {
            $this->_logger->warning('carrier not active');
            return false;
        }
        $this->_logger->info('collect rates');

        try {
            return $this->collect($request);
        } catch (NoQuoteException $e) {
            $this->_logger->warning($e->getMessage());
            return false;
        } catch (\Exception $e) {
            $this->_logger->error('failed to collect rate: '.$e->getMessage());
        }

        // TODO return fallback
        return false;
    }

    /**
     * @return array
     */
    public function getAllowedMethods() {
        return [$this->_code => 'Ingrid'];
    }

    /**
     * @param RateRequest $request
     * @return Result
     */
    private function collect(RateRequest $request): Result {
        /** @var Result $result */
        $result = $this->rateResultFactory->create();
        /** @var Method $method */
        $method = $this->rateMethodFactory->create();

        try {
            $session = $this->sessionProvider->sessionForCheckout();
            $this->_logger->info('session '.$session->getId());
            $shippingResult = $session->getResult();

            $cat = $session->getResult()->getCategory();

            $method->setCarrier($this->getCarrierCode());

            if ($shippingResult->getShipping() != null && $location = $shippingResult->getShipping()->getLocation()) {
                $method->setCarrierTitle($cat->getName());
                $method->setMethodTitle($location->getName());
            } else {
                $carrierTitle = $shippingResult->getShipping() != null ? $shippingResult->getShipping()->getCarrier() : null;
                if (!$carrierTitle) {
                    // carrier title is expected to always be set (vendor/magento/module-checkout/view/frontend/web/js/view/shipping-information.js)
                    // but as we may not have that, we set it to category name if carrier is not available yet
                    $method->setCarrierTitle($cat->getName());
                } else {
                    $method->setCarrierTitle($carrierTitle);
                    $method->setMethodTitle($cat->getName());
                }
            }

            $method->setMethod("ingrid");

            $price = $shippingResult->getPricing()->getPrice() / 100;
            $method->setPrice($price);
            $method->setCost($price);

            $this->_logger->info('collected method '.$method->toString());

            $result->append($method);
        } catch (\Exception $e) {
            $method->setCarrier($this->getCarrierCode());
            $method->setCarrierTitle($this->config->fallbackName($this->checkoutSession->getQuote()->getStore()));
            $method->setMethod("ingrid");
            $result->append($method);
        }

        return $result;
    }
}
