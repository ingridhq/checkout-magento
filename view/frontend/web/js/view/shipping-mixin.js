define([
    'jquery',
    'uiRegistry'
], function ($, registry) {
    'use strict';
    var ingridIsEnabled = window.checkoutConfig.ingrid.isActive;
    return function (Component) {
        if (ingridIsEnabled == "1") {
            return Component.extend({
                defaults: {
                    shippingMethodListTemplate: 'Ingrid_Checkout/shipping-address/shipping-method-list.html'
                },
                initialize: function () {
                    this._super();

                registry.async('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.postcode')(
                    function (postcodeField) {
                        postcodeField.focused.subscribe(function (isFocused) {
                            if (!isFocused) {
                                let value = postcodeField.value();
                                if (value) {
                                    $.ajax({
                                        url: '/ingrid/update/postcode',
                                        type: 'POST',
                                        data: { postcode: value },
                                        dataType: 'json'
                                    }).done(function (response) {
                                        //console.log("Backend response:", response);
                                    });
                                }
                            }
                        });
                    }
                );
                    return this;
                }
            });
        } else {
            return Component;
        }
    }
});