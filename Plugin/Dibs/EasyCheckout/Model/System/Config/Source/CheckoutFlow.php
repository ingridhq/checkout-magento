<?php
namespace Ingrid\Checkout\Plugin\Dibs\EasyCheckout\Model\System\Config\Source;

class CheckoutFlow
{
    public function aroundToOptionArray(\Dibs\EasyCheckout\Model\System\Config\Source\CheckoutFlow $subject, callable $proceed, $isMultiselect = false)
    {
        //$options = $proceed($isMultiselect);
        $options[] = [
            'value' => 'EmbeddedCheckout',
            'label' => __('Embedded')
        ];
        $options[] = [
            'value' => \Dibs\EasyCheckout\Api\CheckoutFlow::FLOW_VANILLA,
            'label' => __('Vanila Embeded')
        ];
        $options[] = [
            'value' => 'HostedPaymentPage',
            'label' => __('Redirect')
        ];
        return $options;
    }
}