<?php
namespace Ingrid\Checkout\Plugin\Klarna\Kco\Model\Api;

/**
 * Class Kasper
 */
class Kasper
{
    /**
     * @var Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * Casper constructor.
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    public function beforeUpdateOrder(
        \Klarna\Kco\Model\Api\Kasper $subject,
        $orderId
    ) {
        //use object manager to create the checkout api in case Klarna_Kco module is disabled/not installed
        $checkoutApi = $this->objectManager->create(\Klarna\Kco\Model\Api\Rest\Service\Checkout::class);
        $quote = $subject->getQuote();
        //collect rates for the quote
        $quote->getShippingAddress()->setCollectShippingRates(true);
        $quote->collectTotals();
        $countryId = $quote->getShippingAddress()->getCountryId();
        $postCode = $quote->getShippingAddress()->getPostcode();
        $street = $quote->getShippingAddress()->getStreet();
        $city = $quote->getShippingAddress()->getCity();
        $data = array(
            "shipping_address" => array(
                "country" => $countryId,
                "postal_code" => $postCode,
                "street_address" => $street[0],
                "city" => $city??''

            ),
            "billing_address" => array(
                "country" => $countryId,
                "postal_code" => $postCode,
                "street_address" => $street[0],
                "city" => $city??''
            )
        );
        if ($quote->getShippingAddress()->getRegion()) {
            $data['shipping_address']['region'] = $quote->getShippingAddress()->getRegionCode();
            $data['billing_address']['region'] = $quote->getShippingAddress()->getRegionCode();
        }
        $response = $checkoutApi->updateOrder($subject->getKcoSession()->getKlarnaQuote()->getKlarnaCheckoutId(), $data, $quote->getQuoteCurrencyCode());

        return [$orderId];
    }
}
