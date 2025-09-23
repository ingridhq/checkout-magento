define([
    'jquery',
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
    'Magento_Checkout/js/action/set-shipping-information',
    'Magento_Checkout/js/action/get-totals'

], function (
    $,
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
    setShippingInformationAction,
    getTotals
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

            //quote.shippingAddress.subscribe(this.shippingAddressObserver.bind(this));
            //quote.billingAddress.subscribe(this.billingAddressObserver.bind(this));
            //quote.totals.subscribe(this.quoteTotalObserver.bind(this));

            domReady(function () {
                var checkExist = window.setInterval(function () {
                    if (window._sw) {
                        // console.log('_sw found');
                        if (window.checkoutConfig.ingrid.hideKlarnaIframe) {
                            $('.opc-wrapper').css("background", "#eeeeee");
                            $('#klarna_kco').css("visibility", "hidden");
                        }
                        if (window.checkoutConfig.ingrid.hideMagentoShippingForm) {
                            $('#co-shipping-form').css("display", "none");
                        } else {
                            $('#co-shipping-form').css("display", "block");
                        }
                        window._sw(function(api) {
                            api.on('address_changed', function(option) {
                                $('.opc-wrapper').css("background", "#fff");
                                $('#klarna_kco').css("visibility", "visible");
                            });
                        });

                        if (window.checkoutConfig.saveShippingMethodUrl === undefined) {
                            checkout.attachEvents();
                        } else {
                              checkout.attachDibsEvents();
                        }
                        window.clearInterval(checkExist);
                    } else {
                        // console.log('no _sw yet');
                    }
                }, 300);
            });
            return this;
        },

        triggerAddressChange: function () {
            // console.log('resolveShippingAddress');
            checkoutDataResolver.resolveShippingAddress();
        },

        shippingAddressObserver: function (address) {
            //console.log('shippingAddressObserver', quote.shippingAddress());
            var addr = quote.shippingAddress();
            if (addr.postcode && addr.email && $('.checkout-klarna-index').length > 0) {
            checkout.updateData({
                    email: addr.email,
                    address: quote.shippingAddress(),
                });
            }
        },

        billingAddressObserver: function (addr) {
            // console.log('billingAddress', addr);
        },

        quoteTotalObserver: function () {
            checkout.suspend();
            checkout.resume();
        }
    });
});
