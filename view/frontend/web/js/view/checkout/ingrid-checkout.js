define([
    'ko',
    'uiElement',
    'underscore',
    'domReady',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/checkout-data-resolver',
    'Magento_Customer/js/model/address-list',
    'Magento_Customer/js/model/customer',
    'Ingrid_Checkout/js/model/config',
    'Ingrid_Checkout/js/model/checkout',
    'uiRegistry',
    'Klarna_Kco/js/action/select-shipping-method',
    'Magento_Checkout/js/action/set-shipping-information'

], function (
    ko,
    Component,
    _,
    domReady,
    quote,
    checkoutDataResolver,
    addressList,
    customerData,
    config,
    checkout,
    uiRegistry,
    kcoShippingMethod,
    setShippingInformationAction
) {
    'use strict';
    // window.ingridquote = quote;
    // window.ingriduiRegistry = uiRegistry;
    // window.ingridcheckoutDataResolver = checkoutDataResolver;

    return Component.extend({
        defaults: {
            template: 'Ingrid_Checkout/checkout/ingrid-checkout'
        },

        config: config,
        initialize: function () {
            //console.log('ingrid initialize');
            var self = this;
            this._super();

            quote.shippingAddress.subscribe(this.shippingAddressObserver.bind(this));
            quote.billingAddress.subscribe(this.billingAddressObserver.bind(this));

            domReady(function () {
                var checkExist = window.setInterval(function () {
                    if (window._sw) {
                        // console.log('_sw found');
                        if (window.checkoutConfig.saveShippingMethodUrl === undefined) {
                            checkout.attachEvents();
                        } else {
                            checkout.attachDibsEvents();
                        }
                        window.clearInterval(checkExist);
                    } else {
                        // console.log('no _sw yet');
                    }
                }, 1000);
            });
            return this;
        },

        triggerAddressChange: function () {
            // console.log('resolveShippingAddress');
            checkoutDataResolver.resolveShippingAddress();
        },

        shippingAddressObserver: function (addr) {
            // console.log('shippingAddressObserver', quote.shippingAddress());
            var email = quote.guestEmail;
            if (!email && checkoutConfig.customerData) {
                email = checkoutConfig.customerData.email;
            }
            checkout.updateData({
                email: email,
                address: quote.shippingAddress(),
            });
            if (window.checkoutConfig.klarna.klarnaUpdateNeeded) {
                var method = quote.shippingMethod();
                if (method !== null) {
                    kcoShippingMethod(method);
                    setShippingInformationAction();
                }
            }
        },

        billingAddressObserver: function (addr) {
            // console.log('billingAddress', addr);
        },

    });
});
