<?php
declare(strict_types=1);

namespace Ingrid\Checkout\Model;

use Ingrid\Checkout\Api\Data\OrderInterface;

/**
 * Class Order
 *
 * Ingrid Order data for ExtensionAttribute on SalesOrder
 *
 * @package Ingrid\Checkout\Model
 */
class Order implements OrderInterface {

    /**
     * @var IngridSession
     */
    private $ingridSession;

    /**
     * Order constructor.
     */
    public function __construct(IngridSession $ingridSession) {
        $this->ingridSession = $ingridSession;
    }

    public function getId(): string {
        return $this->ingridSession->getTosId();
    }

    public function isTest(): bool {
        return $this->ingridSession->isTest();
    }

    public function getSessionId(): string {
        return $this->ingridSession->getSessionId();
    }

    public function getShippingMethod(): string {
        return $this->ingridSession->getShippingMethod();
    }

    public function getExternalMethodId(): ?string {
        return $this->ingridSession->getExternalMethodId();
    }

    public function getLocationId(): ?string {
        return $this->ingridSession->getLocationId();
    }

    public function getTimeSlotId(): ?string {
        return $this->ingridSession->getTimeSlotId();
    }

    public function getLocationName(): ?string {
        return $this->ingridSession->getLocationName();
    }

    public function getCarrier(): ?string {
        return $this->ingridSession->getCarrier();
    }

    public function getProduct(): ?string {
        return $this->ingridSession->getProduct();
    }

    public function getTimeSlotStart(): ?string {
        return $this->ingridSession->getTimeSlotStart();
    }

    public function getTimeSlotEnd(): ?string {
        return $this->ingridSession->getTimeSlotEnd();
    }
}
