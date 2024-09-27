<?php

namespace Ingrid\Checkout\Plugin\Klarna\Orderlines\Model\Calculator;

class Shipping
{
    public function afterGetTaxRateByShippingCosts(
        \Klarna\Orderlines\Model\Calculator\Shipping $subject,
        $result,
        \Magento\Customer\Model\Address\AddressModelInterface $address,
        float $shippingAmount,
    ) {
        if($address->getShippingTaxAmount() != 0) {
            $taxRate = ($address->getShippingInclTax() / ($address->getShippingInclTax() - $address->getShippingTaxAmount()) - 1) * 100;
            return $taxRate;
        }
        return $result;
    }
}