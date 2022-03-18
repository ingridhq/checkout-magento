<?php
declare(strict_types=1);

namespace Ingrid\Checkout\Controller\Api;

use Ingrid\Checkout\Helper\SerializerFactory;
use Ingrid\Checkout\Model\CheckoutUpdateRequest;
use Ingrid\Checkout\Service\IngridSessionService;
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
     * @var \JMS\Serializer\Serializer
     */
    private $serializer;
    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;
    /**
     * @var IngridSessionService
     */
    private $sessionService;

    /**
     * @param Context $context
     * @param CheckoutSession $checkoutSession
     * @param LoggerInterface $logger
     * @param JsonFactory $resultJsonFactory
     * @param IngridSessionService $sessionService
     * @param SerializerFactory $serializerFactory
     */
    public function __construct(
        Context $context,
        CheckoutSession $checkoutSession,
        LoggerInterface $logger,
        JsonFactory $resultJsonFactory,
        IngridSessionService $sessionService,
        SerializerFactory $serializerFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->log = $logger;
        $this->context = $context;
        $this->checkoutSession = $checkoutSession;
        $this->sessionService = $sessionService;
        $this->serializer = $serializerFactory->create();
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

        $body = $request->getContent();
        $this->log->debug('body='.$body, $logCtx);

        /** @var CheckoutUpdateRequest $req */
        $req = $this->serializer->deserialize($body, CheckoutUpdateRequest::class, 'json');

        try {
            $this->sessionService->update($ingridSessionId, $req);
        } catch (\Exception $e) {
            $this->log->error('failed to update session: '.$e->getMessage(), $logCtx);
        }

        $this->log->debug('checkout data callback: success', $logCtx);
        return $this->resultFactory->create(ResultFactory::TYPE_RAW)
            ->setContents("")
            ->setHttpResponseCode(200);
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
}
