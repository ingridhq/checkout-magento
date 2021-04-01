<?php
declare(strict_types=1);

namespace Ingrid\Checkout\Api;

use Ingrid\Checkout\Model\IngridSession;
use Magento\Framework\Exception\NoSuchEntityException;

interface IngridSessionRepositoryInterface {

    /**
     * @param string $ingridSessionId
     * @return IngridSession
     */
    public function getByIngridSessionId(string $ingridSessionId) : IngridSession;

    /**
     * @param int $orderId
     * @return IngridSession
     * @throws NoSuchEntityException
     */
    public function getByOrderId(int $orderId) : IngridSession;
}
