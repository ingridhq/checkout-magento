<?php

declare(strict_types=1);

namespace Ingrid\Checkout\Logger;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

class Logger extends \Monolog\Logger {
    /**
     * @var ScopeConfigInterface
     */
    private $config;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Logger constructor.
     *
     * @param string                              $name
     * @param ScopeConfigInterface                $config
     * @param StoreManagerInterface               $storeManager
     * @param \Monolog\Handler\HandlerInterface[] $handlers
     * @param \callable[]                         $processors
     * @codeCoverageIgnore
     */
    public function __construct(
        $name,
        ScopeConfigInterface $config,
        StoreManagerInterface $storeManager,
        array $handlers = [],
        array $processors = []
    ) {
        parent::__construct($name, $handlers, $processors);
        $this->config = $config;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     */
    public function addRecord($level, $message, array $context = []) {
        return parent::addRecord($level, $message, $context);
    }
}
