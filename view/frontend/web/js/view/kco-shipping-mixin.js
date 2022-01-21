define([
    'jquery'
], function ($) {
    'use strict';
    return function (Component) {
        var ingridIsEnabled = window.checkoutConfig.ingrid.isActive;
        if (ingridIsEnabled == "1") {
            return Component.extend({
                defaults: {
                    template: 'Ingrid_Checkout/kco-shipping-method.html'
                }
            });
        } else {
            return Component;
        }
    }
});