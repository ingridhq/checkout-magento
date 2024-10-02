<?php
declare(strict_types=1);

namespace Ingrid\Checkout\Model;

use Ingrid\Checkout\Helper\Config;
use Ingrid\Checkout\Model\Exception\NoQuoteException;
use Ingrid\Checkout\Service\IngridSessionService;
use Magento\Quote\Api\CartRepositoryInterface;
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
use Magento\Tax\Model\Calculation;
use Magento\Tax\Helper\Data;
use Magento\Checkout\Model\Session as CheckoutSession;

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
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var \Magento\Tax\Model\Calculation
     */
    private $taxCalculation;

    /**
     * @var \Magento\Tax\Helper\Data
     */
    private $taxHelper;

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
     * @param Config $config
     * @param CartRepositoryInterface $quoteRepository
     * @param Calculation $taxCalculation
     * @param Data $taxHelper
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
        CartRepositoryInterface $quoteRepository,
        Calculation $taxCalculation,
        Data $taxHelper,
        CheckoutSession $checkoutSession,
        array $data = []
    ) {
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);

        $this->sessionProvider = $sessionService;
        $this->config = $config;
        $this->quoteRepository = $quoteRepository;
        $this->taxCalculation = $taxCalculation;
        $this->taxHelper = $taxHelper;
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
            $shippingResult = $session->getDeliveryGroups()[0];

            $cat = $session->getDeliveryGroups()[0]->getCategory();

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
            
            $price = floatval($shippingResult->getPricing()->getPrice() / 100);
            $quoteId = $request->getAllItems()[0]->getQuoteId();
            $quote = $this->quoteRepository->get($quoteId);
            $storeId = $quote->getStoreId();
            //set shipping method
            $quote->getShippingAddress()->setShippingMethod('ingrid_ingrid');

            // Fetch the shipping tax class ID from configuration
            $shippingTaxClassId = $this->taxHelper->getShippingTaxClass($storeId);

            // Calculate the tax rate based on the shipping address
            $taxRateRequest = $this->taxCalculation->getRateRequest(
                $quote->getShippingAddress(),
                $quote->getBillingAddress(),
                $quote->getCustomerTaxClassId(),
                $storeId
            );

            $taxRateRequest->setProductClassId($shippingTaxClassId);
            $taxRate = $this->taxCalculation->getRate($taxRateRequest);

            // Calculate the shipping tax amount
            $shippingTaxAmount = $this->taxCalculation->calcTaxAmount($price, $taxRate, false, true);

            // Determine if the price should include tax
            $priceIncludesTax = $this->taxHelper->shippingPriceIncludesTax($storeId);

            if ($priceIncludesTax) {
                $priceWithTax = $price;
                //$priceWithoutTax = $price - $shippingTaxAmount;
            } else {
                //$priceWithoutTax = $price;
                $priceWithTax = $price + $shippingTaxAmount;
            }

            // Use the tax-inclusive or tax-exclusive price as required
            $method->setPrice($priceWithTax);
            //set base price
            $method->setBasePrice($price);
            $method->setCost($priceWithTax);

            $this->_logger->info('collected method '.$method->toString());
            //set method in shipping assignment
            return $result->append($method);
        } catch (\Exception $e) {
            $method->setCarrier($this->getCarrierCode());
            $method->setCarrierTitle($this->config->fallbackName($this->checkoutSession->getQuote()->getStore()));
            $method->setMethod("ingrid");
            $result->append($method);
        }

        return $result;
    }
}
