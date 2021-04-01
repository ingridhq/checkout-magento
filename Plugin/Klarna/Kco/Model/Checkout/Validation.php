<?php
namespace Ingrid\Checkout\Plugin\Klarna\Kco\Model\Checkout;

/**
 * Class Validation
 */
class Validation
{

    public function aroundValidateRequestObject(
        \Klarna\Kco\Model\Checkout\Validation $subject,
        callable $proceed
        ): void {
            
            $proceed;
    }
}
