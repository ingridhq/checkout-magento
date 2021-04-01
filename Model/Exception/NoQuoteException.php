<?php
declare(strict_types=1);

namespace Ingrid\Checkout\Model\Exception;

class NoQuoteException extends \Exception {

    /**
     * NoQuoteException constructor.
     */
    public function __construct() {
        parent::__construct('no Quote on Checkout Session');
    }
}
