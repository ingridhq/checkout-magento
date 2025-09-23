<?php
namespace Ingrid\Checkout\Logger\Handler;

use Ingrid\Checkout\Logger\Logger;

class Error extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * Logging level
     * @var int
     */
    protected $loggerType = Logger::ERROR;

    /**
     * File name
     * @var string
     */
    protected $fileName = '/var/log/ingrid_error.log';
}