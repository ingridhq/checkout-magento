define([
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
    'mage/storage',
    'Ingrid_Checkout/js/model/config',
], function (wrapper, quote, storage, config) {
    'use strict';

    return function (setShippingInformationAction) {
        return wrapper.wrap(setShippingInformationAction, function (originalAction) {
            
            var shippingAddress = quote.shippingAddress();
            var payload = {
                email: shippingAddress.email || quote.guestEmail,
                address: {
                    countryId: shippingAddress.countryId,
                    postcode: shippingAddress.postcode,
                    city: shippingAddress.city,
                    email: shippingAddress.email || quote.guestEmail,
                    regionCode: shippingAddress.region ? shippingAddress.region.code : '',
                    street: shippingAddress.street,
                    telephone: shippingAddress.telephone,
                },
            };

            storage.post(config.checkoutUrl, JSON.stringify(payload))
            .done(function () {
            }).fail(function () {
            });

            return originalAction().done(function () {
            });
        });
    };
});