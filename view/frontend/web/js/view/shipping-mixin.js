define([
    'jquery'
], function ($) {
    'use strict';
    return function (Component) {
        return Component.extend({
            defaults: {
                shippingMethodListTemplate: 'Ingrid_Checkout/shipping-address/shipping-method-list.html'
            }
        });
    }
});