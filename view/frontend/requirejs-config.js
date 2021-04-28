var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/view/shipping': {
                'Ingrid_Checkout/js/view/shipping-mixin': true
            },
            'Klarna_Kco/js/view/shipping-method': {
                'Ingrid_Checkout/js/view/kco-shipping-mixin': true
            }
        }
    }
};
