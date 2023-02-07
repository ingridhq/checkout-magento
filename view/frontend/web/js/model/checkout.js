
define([
    'jquery',
    'uiRegistry',
    'mage/url',
    'mage/storage',
    'underscore',
    'Magento_Checkout/js/action/select-payment-method',
    'Magento_Checkout/js/action/select-shipping-address',
    'Magento_Checkout/js/action/select-billing-address',
    'Magento_Checkout/js/model/new-customer-address',
    'Magento_Checkout/js/model/quote',
    'Ingrid_Checkout/js/model/config',
    'Magento_Checkout/js/action/get-totals',
    'Magento_Checkout/js/action/set-shipping-information'
], function (
    $,
    uiRegistry,
    mageurl,
    storage,
    _,
    selectPaymentMethodAction,
    selectShippingAddress,
    selectBillingAddress,
    newAddress,
    quote,
    config,
    getTotals,
    setShippingInformationAction,
) {
    'use strict';
    var refreshInProcess = false;
    var lastUpdate = null;

    var isEqual = function (a, b) {
        return JSON.stringify(a) === JSON.stringify(b);
    };

    return {
        /**
         * Notify Ingrid about new data, will trigger subsequent reload of checkout on completion
         *
         * @param {Object} data Checkout data
         * @param {string} data.email Customer email
         * @param {Object} data.address Shipping Address (as from Quote, Magento_Checkout/js/model/quote)
         * @param {string} data.address.countryId
         * @param {string} data.address.postcode
         * @param {string} data.address.city
         * @param {string} data.address.email
         * @param {string} data.address.regionCode
         * @param {string[]} data.address.street
         * @param {string} data.address.telephone
         */
        updateData: function(data) {
            if (!window._sw) {
                return;
            }
            var postData = {
                email: data.email,
                address: {
                    countryId: data.address.countryId,
                    postcode: data.address.postcode,
                    city: data.address.city,
                    email: data.address.email,
                    regionCode: data.address.regionCode,
                    street: data.address.street,
                    telephone: data.address.telephone,
                },
            };

            // console.log('updateData', postData);
            // console.log('lastUpdate', lastUpdate);
            if (isEqual(postData, lastUpdate)) {
                // console.log('no new data, skipping update');
                return;
            }
            if (refreshInProcess) {
                // console.log('refresh already running');
                return;
            }
            refreshInProcess = true;
            lastUpdate = postData;

            window._sw(function(api) {
                api.suspend();
            });
            // console.log('post '+config.checkoutUrl, postData)
            storage.post(config.checkoutUrl, JSON.stringify(postData))
                .done(function (response) {
                    // console.log('post response ok');
                    setShippingInformationAction().done(
                        function () {
                            getTotals([]);
                        }
                    );
                })
                .fail(function (response) {
                    // console.log('post response fail', response);
                })
                .always(function () {
                    window._sw(function(api) {
                        api.resume();
                    });
                    refreshInProcess = false;
                })
        },
        /**
         * Suspend Ingrid
         */
        suspend: function() {
            if (!window._sw) {
                return;
            }
            window._sw(function(api) {
                api.suspend();
            });
        },
        /**
         * Resume Ingrid
         */
        resume: function() {
            if (!window._sw) {
                return;
            }
            window._sw(function(api) {
                api.resume();
            });
        },
        attachEvents: function () {
            window._sw(function(api) {
                api.on('data_changed', function(m,b) {
                    if (b.pickup_location_changed) {
                        $('.opc-wrapper').css("background", "#fff");
                        $('#klarna_kco').css("visibility", "visible");
                    }
                    if (!b.initial_load && b.shipping_method_changed || b.pickup_location_changed) {
                        if (quote.shippingMethod() != undefined) {
                            setShippingInformationAction().done(
                                function () {
                                    getTotals([]);
                                }
                            );
                        }
                    }
                })
            });
        },
        attachDibsEvents: function () {
            var self = this;
            window._sw(function(api) {
                api.on('shipping_option_changed', function(option) {
                    // console.log('option changed: ', option);
                    getTotals([]);
                    $.ajax({
                        type: "POST",
                        context: this,
                        url: mageurl.build("easycheckout/order/cart/"),
                        success: function (response) {
                            window._dibsCheckout.freezeCheckout();
                            window._dibsCheckout.thawCheckout();
                            var dibsCheckout = uiRegistry.get('nwtdibsCheckout');
                            if (jQuery.parseJSON(response).updates) {
                                var blocks = jQuery.parseJSON(response).updates;
                                var div = null;
                                for (var block in blocks) {
                                    if (blocks.hasOwnProperty(block)) {
                                        div = jQuery('#dibs-easy-checkout_' + block);
                                        if (div.size() > 0) {
                                            div.replaceWith(blocks[block]);
                                            dibsCheckout._bindEvents(block);
                                        }
                                    }
                                }
                            }
                        }
                    });
                });
            });
        },
    }
});
