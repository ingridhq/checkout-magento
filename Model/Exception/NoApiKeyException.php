<?php
declare(strict_types=1);

namespace Ingrid\Checkout\Model\Exception;

class NoApiKeyException extends \Exception {

    /**
     * NoApiKeyException constructor.
     */
    public function __construct() {
        parent::__construct('missing api key, please configure Ingrid');
    }
}
