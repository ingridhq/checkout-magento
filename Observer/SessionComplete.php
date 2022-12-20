<?php

declare(strict_types=1);

namespace Ingrid\Checkout\Observer;

use Ingrid\Checkout\Service\IngridSessionService;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Ingrid\Checkout\Model\ResourceModel\IngridSession;

/**
 * SessionComplete on sales_order_place_after
 */
class SessionComplete implements ObserverInterface {

    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var IngridSessionService
     */
    private $sessionService;

    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var IngridSession
     */
    protected $ingridSession;


    public function __construct(
        LoggerInterface $logger,
        CheckoutSession $checkoutSession,
        IngridSessionService $sessionService,
        CartRepositoryInterface $quoteRepository,
        IngridSession $ingridSession
    ) {
        $this->logger = $logger;
        $this->checkoutSession = $checkoutSession;
        $this->sessionService = $sessionService;
        $this->quoteRepository = $quoteRepository;
        $this->ingridSession = $ingridSession;
    }

    public function execute(Observer $observer) {
        $this->logger->debug('execute sales_order hook: '.$observer->getEvent()->getName());

        try {
            /** @var  \Magento\Sales\Model\Order $order */
            $order = $observer->getEvent()->getData('order');
            //if session complete return to not save twice
            $entityID = $this->ingridSession->getIdByOrderId((int) $order->getId());
            if ($entityID) {
                return;
            }

            $quote = $this->quoteRepository->get($order->getQuoteId());
            $ingridSessionId = $quote->getIngridSessionId();

            $orderCtx = ['order_id' => $order->getId(), 'quote_id' => $order->getQuoteId(), 'ingrid_session_id' => $ingridSessionId];
            if ($ingridSessionId === null) {

                // isFallback tells us that this is an actual checkout session and not a bogus event fire, even if the checkoutSession don't have a ingrid session
                // if none of these keys are present the event is ignored
                $isFallbackCheckout = $this->checkoutSession->getData(IngridSessionService::SESSION_FALLBACK_KEY, true);
                if (!$isFallbackCheckout) {
                    $this->logger->debug('neither ingrid session id not fallback key on checkout-session, ignoring event');
                    return;
                }

                try {
                    $this->logger->info('complete called without session id, but fallback present', $orderCtx);
                    $session = $this->sessionService->sessionForCheckout();
                    $this->logger->info('created session for checkout '.$session->getId(), $orderCtx);
                    $ingridSessionId = $session->getId();
                } catch (\Exception $e) {
                    $this->logger->warning('unable to create session at checkout, continuing fallback: '.$e->getMessage(), $orderCtx);
                }
            }

            $this->sessionService->complete($ingridSessionId, $order);
        } catch (\Exception $e) {
            $this->logger->error('failed to complete session: '.$e->getMessage(), $orderCtx);
        }
    }
}
