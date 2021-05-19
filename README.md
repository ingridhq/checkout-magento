# Ingrid Magento

## Install and Configure

### Super Fast Install

```
composer require ingrid/checkout-magento
``` 

### Install

Unpack the archive in to `app/code/Ingrid/Checkout`.

Enable the Ingrid module:

    magento module:enable Ingrid_Checkout
    magento setup:upgrade

### Configure

Configure Ingrid in admin at `Stores > Configuration > Sales > Shipping Methods > Ingrid Delivery Experience Platform`.

Enter at minimum Test Api key and select test mode to get started.

Or setting the paths:
 
- carriers/ingrid/active: true
- carriers/ingrid/test_mode: true
- carriers/ingrid/stage_api_key: <your api key>

Disable all other shipping methods, as they will conflict with Ingrid.
    
## Frontend

### Ingrid Checkout Widget

The Checkout Widget is added by default before the `shippingAddress` form in checkout.

To put it somewhere else include this in you checkout layout.

    <item name="ingrid_checkout" xsi:type="array">
        <item name="component" xsi:type="string">Ingrid_Checkout/js/view/checkout/ingrid-checkout</item>
    </item>

### Checkout data sync

Ingrid does not by itself auto-update when consumer/address data is entered in checkout.

Ingrid will however update when `shippingAddress` change on the `quote`.
As such, you may trigger update of Ingrid using `Magento_Checkout/js/model/checkout-data-resolver#resolveShippingAddress()`.

Alternatively, Ingrid can be called directly using `Ingrid_Checkout/js/model/checkout#updateData()`.

## Admin

On orders completed using Ingrid you will, in addition to the standard Shipping Method, find Ingrid shipping details in the Order overview.  

## Integration points

- Coupons/Cart Price Rules
    - Specific Coupons
    - Free Shipping
- Magento Shipping Method
    - For display of the shipping line item on the order, does not carry sufficient information for booking
- Magento Api (Sales Order Api)
    - Complete information for booking are preset on the Sales Order, at `extension_attributes.ingrid`, this includes
        - selected option
        - external_method_id
        - location
        - timeslot
- Sales Order admin view
    - Complete information for booking
