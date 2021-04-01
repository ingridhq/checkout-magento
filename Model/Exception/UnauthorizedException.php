<?php
declare(strict_types=1);

namespace Ingrid\Checkout\Model\Exception;

class UnauthorizedException extends \Exception {

    /**
     * UnauthorizedException constructor.
     */
    public function __construct() {
        parent::__construct('unauthorized, please check your api key');
    }
}
