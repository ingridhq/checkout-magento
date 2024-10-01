<?php

namespace Ingrid\Checkout\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SetBaseTaxAmountObserver implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        $address = $observer->getEvent()->getQuote()->getShippingAddress();
        
        // update base tax amounts to be the same as tax amounts
        if($address->getBaseTaxAmount() != $address->getTaxAmount()){
            $address->setBaseTaxAmount($address->getTaxAmount());
            $address->setBaseShippingTaxAmount($address->getShippingTaxAmount());
            $address->save();
        }
    }
}
