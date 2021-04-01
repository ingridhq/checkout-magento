<?php
declare(strict_types=1);

namespace Ingrid\Checkout\Block\Adminhtml\Order\View;

use Ingrid\Checkout\Model\IngridSession;
use Ingrid\Checkout\Model\IngridSessionRepository;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

class Ingrid extends Template {

    /**
     * @var IngridSessionRepository
     */
    private $ingridSessionRepository;
    /**
     * @var LoggerInterface
     */
    private $log;
    /**
     * @var IngridSession
     */
    private $ingridSession;

    /**
     * Ingrid constructor.
     */
    public function __construct(
        Context $context,
        IngridSessionRepository $ingridSessionRepository,
        LoggerInterface $logger,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->ingridSessionRepository = $ingridSessionRepository;
        $this->log = $logger;

        $orderId = (int) $this->getRequest()->getParam('order_id');
        $this->log->debug('admin view order '.$orderId);
        try {
            $this->ingridSession = $this->ingridSessionRepository->getByOrderId($orderId);
        } catch (NoSuchEntityException $e) {
            $this->log->debug('no ingrid session for order "'.$orderId.'" found: '.$e->getMessage());
            // skips rendering of block
        }
    }

    public function sessionExists(): bool {
        return $this->ingridSession ? true : false;
    }

    public function isFallback(): bool {
        return $this->ingridSession->getShippingMethod() === 'ingrid-fallback';
    }

    /**
     * @return IngridSession
     */
    public function getInfo(): IngridSession {
        return $this->ingridSession;
    }

    public function isProductDisplay(): bool {
        return $this->ingridSession->getCarrier() && $this->ingridSession->getProduct();
    }

    public function getMethodDisplay(): string {
        if ($this->ingridSession->getExternalMethodId()) {
            return $this->ingridSession->getExternalMethodId().' - '.$this->ingridSession->getShippingMethod();
        }
        return $this->ingridSession->getShippingMethod();
    }

    public function getProductDisplay(): string {
        $fullName = $this->ingridSession->getCarrier().' '.$this->ingridSession->getProduct();
        if ($this->ingridSession->getCarrier() == $this->ingridSession->getProduct()) {
            $fullName = $this->ingridSession->getCarrier();
        }

        return $fullName.' ('.$this->getMethodDisplay().')';
    }

    public function getTimeSlot(): string {
        $start = \DateTime::createFromFormat(DATE_RFC3339, $this->ingridSession->getTimeSlotStart());
        $end = \DateTime::createFromFormat(DATE_RFC3339, $this->ingridSession->getTimeSlotEnd());
        if (!$start || !$end) {
            // fallback in case of broken timestamps
            return 'no time available';
        }

        if ($start->format('H:i') == '00:00' && $end->format('H:i') == '00:00') {
            return $start->format('Y-m-d').' - '.$end->format('Y-m-d');
        } elseif ($start->format('Y-m-d') == $end->format('Y-m-d')) {
            if ($start->format('H:i') == $end->format('H:i')) {
                return $start->format('Y-m-d H:i');
            } else {
                return $start->format('Y-m-d H:i').' - '.$end->format('H:i');
            }
        }
        return $start->format('Y-m-d H:i').' - '.$end->format('Y-m-d H:i');
    }
}
