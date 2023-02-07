define(
  [
    'jquery',
    'domReady',
    'mage/url',
    'Magento_Customer/js/customer-data'
  ],
  function (
    $,
    domReady,
    mageurl,
    customerData
  ) {
    'use strict';
    return function (config) {
      var configObject = {
        country: config.country,
        locales: [config.locales],
        currency: config.currency,
        auth_token: config.auth_token,
        cart: {},
        viewed_item: config.viewed_item,
      };

      $.ajax({
        type: "GET",
        context: this,
        url: mageurl.build("/ingrid/product/widget/"),
        success: function (response) {
          if (response.cart) {
            configObject.cart = response.cart
            if (window._ingridPDPWidgetApi) {
              window._ingridPDPWidgetApi.addListener("error", (value) => {
                console.log("Error event: " + value);
              });
              window._ingridPDPWidgetApi.render(config.id, configObject);
            }
          }
        }
      });

      var cart = customerData.get('cart');
      cart.subscribe(function () {
          if (typeof configObject.cart.items === 'undefined') {
            $.ajax({
              type: "GET",
              context: this,
              url: mageurl.build("/ingrid/product/widget/"),
              success: function (response) {
                if (response.cart) {
                  configObject.cart = response.cart
                    if (window._ingridPDPWidgetApi) {
                      window._ingridPDPWidgetApi.addListener("error", (value) => {
                        console.log("Error event: " + value);
                      });
                      window._ingridPDPWidgetApi.render(config.id, configObject);
                    }  
                }
              }
            });
          } else {
            if (cart().summary_count > configObject.cart.items.length) {
              var cartItems = cart().items;
              var addedItem = cartItems.pop();
              window._ingridPDPWidgetApi.onItemAdded(addedItem.product_sku);
            }
          }
      });
    };
  });
