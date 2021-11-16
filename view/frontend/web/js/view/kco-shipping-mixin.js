define([
    'jquery'
], function ($) {
    'use strict';
    return function (Component) {
        return Component.extend({
            defaults: {
                template: 'Ingrid_Checkout/kco-shipping-method.html'
            }
        });
    }
});