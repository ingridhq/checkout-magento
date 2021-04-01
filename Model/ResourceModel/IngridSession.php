<?php

declare(strict_types=1);

namespace Ingrid\Checkout\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Sales\Api\Data\OrderInterface as MageOrder;

class IngridSession extends AbstractDb {

    /**
     * Get order identifier by Ingrid Order ID
     *
     * @param string $tosId
     * @return int|false
     */
    public function getIdByTosId(string $tosId) {
        $connection = $this->getConnection();

        $select = $connection->select()->from($this->getMainTable(), 'id')
                             ->where('tos_id = :tos_id');

        $bind = [':tos_id' => $tosId];

        return $connection->fetchOne($select, $bind);
    }

    /**
     * Get order identifier by order
     *
     * @param MageOrder $mageOrder
     * @return false|int
     */
    public function getIdByOrder(MageOrder $mageOrder) {
        $connection = $this->getConnection();

        $select = $connection->select()->from($this->getMainTable(), 'id')
                             ->where('order_id = :order_id');

        $bind = [':order_id' => $mageOrder->getEntityId()];

        return $connection->fetchOne($select, $bind);
    }

    /**
     * Get Entity ID by COS Session ID
     *
     * @param string $sessionId
     * @return int
     */
    public function getIdByIngridSessionId(string $ingridSessionId) {
        $connection = $this->getConnection();

        $select = $connection->select()->from($this->getMainTable(), 'id')
                             ->where('session_id = :session_id');

        $bind = [':session_id' => $ingridSessionId];

        return $connection->fetchOne($select, $bind);
    }

    /**
     * Get Entity ID by Magento Internal Order ID
     *
     * @param string $sessionId
     * @return int
     */
    public function getIdByOrderId(int $orderId) {
        $connection = $this->getConnection();

        $select = $connection->select()->from($this->getMainTable(), 'id')
            ->where('order_id = :order_id');

        $bind = [':order_id' => $orderId];

        return $connection->fetchOne($select, $bind);
    }

    /**
     * Constructor
     *
     * @codeCoverageIgnore
     * @codingStandardsIgnoreLine
     */
    protected function _construct() {
        $this->_init('ingrid_session', 'id');
    }
}
