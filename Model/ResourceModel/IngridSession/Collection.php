<?php

declare(strict_types=1);

namespace Ingrid\Checkout\Model\ResourceModel\IngridSession;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection {

    /**
     * Constructor
     *
     * @codeCoverageIgnore
     * @codingStandardsIgnoreLine
     */
    protected function _construct() {
        $this->_init(\Ingrid\Checkout\Model\IngridSession::class, \Ingrid\Checkout\Model\ResourceModel\IngridSession::class);
    }
}
