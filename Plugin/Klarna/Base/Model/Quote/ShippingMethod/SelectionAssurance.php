<?php
namespace Ingrid\Checkout\Plugin\Klarna\Base\Model\Quote\ShippingMethod;

class SelectionAssurance
{
    public function aroundEnsureShippingMethodSelectedWithPreCollect(
        \Klarna\Base\Model\Quote\ShippingMethod\SelectionAssurance $subject,
        callable $proceed,
        $quote
    ) {
        //$proceed($quote);
        return $subject;
    }
        
}