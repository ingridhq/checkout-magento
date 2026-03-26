<?php
declare(strict_types=1);

namespace Ingrid\Checkout\Controller\Api;

use Ingrid\Checkout\Api\SiwClientInterface;
use Ingrid\Checkout\Api\Siw\Model\UpdateSessionRequest;
use Ingrid\Checkout\Api\Siw\Model\Address;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Psr\Log\LoggerInterface;

/**
 * API call to feed Ingrid with new data from checkout.
 * After successful call, frontend should trigger reload of Ingrid widget as well as Magento shipping and totals.
 *
 * @package Ingrid\Checkout\Controller\Api
 */
class Checkout extends BaseAction {
    /**
     * @var Context
     */
    private $context;
    /**
     * @var LoggerInterface
     */
    private $log;
    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var SiwClientInterface
     */
    private $siwClient;

    /**
     * @param Context $context
     * @param CheckoutSession $checkoutSession
     * @param LoggerInterface $logger
     * @param JsonFactory $resultJsonFactory
     * @param SiwClientInterface $siwClient
     */
    public function __construct(
        Context $context,
        CheckoutSession $checkoutSession,
        LoggerInterface $logger,
        JsonFactory $resultJsonFactory,
        SiwClientInterface $siwClient
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->log = $logger;
        $this->context = $context;
        $this->checkoutSession = $checkoutSession;
        $this->siwClient = $siwClient;
    }

    /**
     * @return ResultInterface|ResponseInterface
     */
    public function execute() {
        $logCtx = $this->getExtendedContext($this->logCtx());
        $this->log->debug('checkout data callback: start', $logCtx);

        $request = $this->getRequest();
        if (!$request->isPost()) {
            $this->log->error('not post request', $logCtx);

            $resultPage = $this->resultJsonFactory->create();
            $resultPage->setHttpResponseCode(405);
            return $resultPage;
        }

        $ingridSessionId = $this->checkoutSession->getQuote()->getIngridSessionId();
        if ($ingridSessionId === null) {
            $msg = 'checkout session missing Ingrid ID, please reload the page';
            $this->log->warning($msg, $logCtx);
            $result = $this->resultJsonFactory->create();
            $result->setData(['msg' => $msg]);
            $result->setHttpResponseCode(400);
            return $result;
        }
        $this->log->debug('checkout session id='.$ingridSessionId, $logCtx);

        // Build UpdateSessionRequest as in the other controller
        $updateReq = new UpdateSessionRequest();
        $updateReq->setId($ingridSessionId);

        $quote = $this->checkoutSession->getQuote();
        $shippingAddr = $quote->getShippingAddress();

        $addr = new Address();
        $addr->setCity($shippingAddr->getCity());
        $addr->setCountry($shippingAddr->getCountryId());
        $addr->setPostalCode($shippingAddr->getPostcode());

        if ($quote->getCustomerIsGuest()) {
            $addr->setRegion($shippingAddr->getRegionCode());
        } else {
            if ($shippingAddr->getRegion() != null) {
                $region = $shippingAddr->getRegion();
                if (is_object($region) && method_exists($region, 'getRegionCode')) {
                    $addr->setRegion($region->getRegionCode());
                } else {
                    $addr->setRegion(is_string($region) ? $region : '');
                }
            }
        }

        $addrLines = self::cleanStreet($shippingAddr->getStreet());
        if ($addrLines) {
            $addr->setAddressLines($addrLines);
        }

        if ($addr->getCountry() != '') {
            $updateReq->setSearchAddress($addr);
        }

        try {
            $this->siwClient->updateSession($updateReq);
        } catch (\Exception $e) {
            $this->log->error('failed to update session: '.$e->getMessage(), $logCtx);
        }

        $this->log->debug('checkout data callback: success', $logCtx);
        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $resultRaw->setContents("");
        $resultRaw->setHttpResponseCode(200);
        return $resultRaw;
    }

    /**
     * @return HttpRequest
     */
    public function getRequest() {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->context->getRequest();
    }

    /**
     * Extending the logging context
     *
     * @param array $context
     * @return array
     */
    private function logCtx($context = []) : array {
        if (!isset($context['ingrid_session_id'])) {
            $context['ingrid_session_id'] = $this->checkoutSession->getQuote()->getIngridSessionId();
        }
        return $context;
    }

    /**
     * @param string|array $street
     * @return array|null
     */
    public static function cleanStreet($street): ?array {
        if (!is_array($street)) {
            $street = [$street];
        }

        $street = array_map(function ($line) {
            if ($line !== null) {
                return trim($line);
            }
            return null;
        }, $street);
        $street = array_filter($street);

        if (count($street) > 0) {
            return $street;
        }

        return null;
    }
}
