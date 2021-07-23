define([
    'jquery'
], function ($) {
    'use strict';
    var ingridIsEnabled = window.checkoutConfig.ingrid.isActive;
    return function (Component) {
        if (ingridIsEnabled == "1") {
            return Component.extend({
                defaults: {
                    shippingMethodListTemplate: 'Ingrid_Checkout/shipping-address/shipping-method-list.html'
                }
            });
        } else {
            return Component;
        }
    }
});