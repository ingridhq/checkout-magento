<?php

declare(strict_types=1);

namespace Ingrid\Checkout\Logger;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Monolog\Level;
use Monolog\JsonSerializableDateTimeImmutable;

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
     * Adds a log record.
     *
     * @param  int               $level    The logging level (a Monolog or RFC 5424 level)
     * @param  string            $message  The log message
     * @param  mixed[]           $context  The log context
     * @param  JsonSerializableDateTimeImmutable|null $datetime Optional log date to log into the past or future
     * @return bool              Whether the record has been processed
     *
     * @phpstan-param Level $level
     */
    public function addRecord(int|Level $level, string $message, array $context = [], JsonSerializableDateTimeImmutable|null $datetime = null): bool
    {
        return parent::addRecord($level, $message, $context, $datetime);
    }
}
