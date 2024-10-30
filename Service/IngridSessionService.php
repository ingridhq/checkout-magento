<?php

declare(strict_types=1);

namespace Ingrid\Checkout\Service;

use Cocur\Slugify\Slugify;
use Ingrid\Checkout\Api\Siw\ApiException;
use Ingrid\Checkout\Api\Siw\Model\Address;
use Ingrid\Checkout\Api\Siw\Model\Cart;
use Ingrid\Checkout\Api\Siw\Model\CartItem;
use Ingrid\Checkout\Api\Siw\Model\CompleteSessionRequest;
use Ingrid\Checkout\Api\Siw\Model\CompleteSessionResponse;
use Ingrid\Checkout\Api\Siw\Model\CreateSessionRequest;
use Ingrid\Checkout\Api\Siw\Model\CreateSessionResponse;
use Ingrid\Checkout\Api\Siw\Model\CustomerInfo;
use Ingrid\Checkout\Api\Siw\Model\Dimensions;
use Ingrid\Checkout\Api\Siw\Model\Session;
use Ingrid\Checkout\Api\Siw\Model\UpdateSessionRequest;
use Ingrid\Checkout\Api\SiwClientInterface;
use Ingrid\Checkout\Helper\Config;
use Ingrid\Checkout\Model\CheckoutUpdateRequest;
use Ingrid\Checkout\Model\Exception\NoQuoteException;
use Ingrid\Checkout\Model\Exception\UnauthorizedException;
use Ingrid\Checkout\Model\IngridSessionRepository;
use Magento\Bundle\Model\Product\Price;
use Magento\Catalog\Api\AttributeSetRepositoryInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Product\Type;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Model\Store;
use Psr\Log\LoggerInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Catalog\Model\ProductRepository;
use Magento\Directory\Model\RegionFactory;

class IngridSessionService {
    const SESSION_ID_KEY = 'ingrid_session_id';
    const SESSION_FALLBACK_KEY = 'ingrid_session_fallback';

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;
    /**
     * @var LoggerInterface
     */
    private $log;
    /**
     * @var SiwClientInterface
     */
    private $siwClient;
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;
    /**
     * @var AttributeSetRepositoryInterface
     */
    private $attributeSetRepository;
    /**
     * @var Slugify
     */
    private $slugify;
    /**
     * @var IngridSessionRepository
     */
    private $ingridSessionRepository;

    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var RegionFactory
     */
    private $regionFactory;

    /**
     * IngridSessionProvider constructor.
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        CategoryRepositoryInterface $categoryRepository,
        AttributeSetRepositoryInterface $attributeSetRepository,
        IngridSessionRepository $ingridSessionRepository,
        LoggerInterface $logger,
        SiwClientInterface $siwClient,
        Config $config,
        AddressRepositoryInterface $addressRepository,
        ProductRepository $productRepository,
        RegionFactory $regionFactory
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->log = $logger;
        $this->siwClient = $siwClient;
        $this->config = $config;

        $this->categoryRepository = $categoryRepository;
        $this->attributeSetRepository = $attributeSetRepository;
        $this->slugify = new Slugify(['separator' => '_']);
        $this->ingridSessionRepository = $ingridSessionRepository;
        $this->addressRepository = $addressRepository;
        $this->productRepository = $productRepository;
        $this->regionFactory = $regionFactory;
    }

    /**
     * @param Address $addr
     * @param OrderAddressInterface $mageAddr
     * @return CustomerInfo
     */
    public static function mkCustomer(Address $addr, OrderAddressInterface $mageAddr): CustomerInfo {
        $customer = new CustomerInfo();
        $customer->setAddress($addr);
        $email = $mageAddr->getEmail(); // this is getQuote()->getCustomerEmail();
        if ($email && $email !== '') {
            $customer->setEmail($email);
        }

        $phone = $mageAddr->getTelephone();
        if ($phone && $phone !== '') {
            $customer->setPhone($phone);
        }

        return $customer;
    }

    /**
     * @param string|array $street
     * @return array|null
     */
    public static function cleanStreet($street): ?array {
        if (!is_array($street)) {
            $street = [$street];
        }

        $street = array_map(function ($line) {
            if ($line !== null) {
                return trim($line);
            }
            return null;
        }, $street);
        $street = array_filter($street);

        if (count($street) > 0) {
            return $street;
        }

        return null;
    }

    /**
     * @param OrderAddressInterface $a
     * @param string|null $firstName
     * @param string|null $lastName
     * @return Address
     */
    public static function mkAddress(OrderAddressInterface $a, ?string $firstName, ?string $lastName): Address {
        $addr = new Address();
        $addr->setCountry($a->getCountryId());
        $addr->setPostalCode($a->getPostcode());
        $city = $a->getCity();
        if ($city !== '') {
            $addr->setCity($city);
        }

        if ($a->getRegionCode() != '') {
            $addr->setRegion($a->getRegionCode());
        }

        $addrLines = self::cleanStreet($a->getStreet());
        if ($addrLines) {
            $addr->setAddressLines($addrLines);
        }

        if ($firstName && $lastName) {
            $name = join(' ', [$firstName, $lastName]);
            if (!empty($name)) {
                $addr->setName($name);
            }
        }

        return $addr;
    }

    /**
     * @return SessionHolder
     * @throws LocalizedException
     * @throws NoQuoteException
     * @throws NoSuchEntityException
     * @throws UnauthorizedException
     * @throws ApiException
     */
    private function mageCheckoutSession(): SessionHolder {
        $ingridSessionId = $this->checkoutSession->getQuote()->getIngridSessionId();
        $quote = $this->checkoutSession->getQuote();
        
        if ($ingridSessionId == null) {
            $this->log->info('no active Ingrid session on checkout session, creating');
            try {
                $resp = $this->createSession($this->checkoutSession);
                $this->checkoutSession->getQuote()->setIngridSessionId($resp->getSession()->getId())->save();
                return new SessionHolder($resp->getSession(), $resp->getHtmlSnippet());
            } catch (\Exception $e) {
                $this->checkoutSession->setData(self::SESSION_FALLBACK_KEY, true);
                throw $e;
            }
        }
        try {
            $this->log->info('updating current Ingrid session on checkout session');
            $resp = $this->siwClient->getSession($ingridSessionId);
        } catch (\Exception $e) {
            $this->log->info('failed to get session, creating new');
            $this->checkoutSession->getQuote()->setIngridSessionId(null)->save();
            return $this->mageCheckoutSession();
        }
        $ingridCartItems = $resp->getSession()->getCart()->getItems();
        $ingridcart = [];
        $ingridItemCount = 0;
        foreach ($ingridCartItems as $ingridItem) {
            $ingridcart[] = $ingridItem->getSku();
            $ingridItemCount += $ingridItem->getQuantity();
        }
        $mcart = [];
        $mcartItemCount = $quote->getItemsQty();
        foreach ($quote->getAllVisibleItems() as $item) {
            $mcart[] = $item->getSku();
        }
        $diff = count($mcart) != count($ingridcart);
        $diff2 = $ingridItemCount != $mcartItemCount;

        $quoteStoreCode = $quote->getStore()->getCode();
        if ($diff || $diff2 || !in_array('store:'.$quoteStoreCode ,$resp->getSession()->getCart()->getAttributes())) {
            $updateReq = new UpdateSessionRequest();
            $updateReq->setId($ingridSessionId);
            $updateReq->setCart($this->makeCart($quote));
            $this->siwClient->updateSession($updateReq);
            $resp = $this->siwClient->getSession($ingridSessionId);
        }
        //update quote address from search address v1
        $searchAddress = $resp->getSession()->getDeliveryGroups()[0]->getAddresses()->getSearchAddress();
        if($searchAddress != null){
            if($searchAddress->getPostalCode() != null){
                $this->mapAddress($quote, $searchAddress, 'shipping', true);
                $this->mapAddress($quote, $searchAddress, 'billing', true);
                $quote->save();
            }
        }
        //update quote address
        $addresses = $resp->getSession()->getDeliveryGroups()[0]->getAddresses();
        if($addresses->getDeliveryAddress() != null && $addresses->getBillingAddress() != null){
            $quote->getCustomerEmail() == null ? $quote->setCustomerEmail($addresses->getBillingAddress()->getEmail()):"";
            if($addresses->getDeliveryAddress()->getStreet() != null && $addresses->getBillingAddress()->getStreet() != null){
                $this->mapAddress($quote, $addresses->getDeliveryAddress(), 'shipping');
                $this->mapAddress($quote, $addresses->getBillingAddress(), 'billing');
                $quote->save();
            }
        }

        return new SessionHolder($resp->getSession(), $resp->getHtmlSnippet());
    }

    /**
     * @return Session
     * @throws ApiException
     * @throws LocalizedException
     * @throws NoQuoteException
     * @throws NoSuchEntityException
     * @throws UnauthorizedException
     */
    public function sessionForCheckout(): Session {
        return $this->mageCheckoutSession()->session;
    }

    /**
     * @return string
     * @throws ApiException
     * @throws LocalizedException
     * @throws NoQuoteException
     * @throws NoSuchEntityException
     * @throws UnauthorizedException
     */
    public function sessionHtmlForCheckout(): string {
        return $this->mageCheckoutSession()->htmlSnippet;
    }

    /**
     * @param CheckoutSession $checkoutSession
     * @return CreateSessionResponse
     * @throws NoQuoteException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function createSession(CheckoutSession $checkoutSession) {
        $locale = $this->config->getLocale();
        $quote = $checkoutSession->getQuote();
        $quoteId = $quote->getId();
        if ($quoteId == null) {
            throw new NoQuoteException();
        }
        $req = new CreateSessionRequest();
        $purchaseCountry = $this->config->getPurchaseCountry($quote->getStore());
        if($quote->getShippingAddress()->getCountryId() == null){
            $quote->getShippingAddress()->setCountryId($purchaseCountry);
        }
        $currency = $quote->getQuoteCurrencyCode();

        $req->setPurchaseCurrency($currency);
        $req->setPurchaseCountry($purchaseCountry);
        $req->setLocales([$locale]);

        $shippingAddr = $quote->getShippingAddress();
        if(!$quote->getCustomerIsGuest()){
            $shippingAddressId = $quote->getCustomer()->getDefaultShipping();
            if($shippingAddressId != null){
                $shippingAddr = $this->addressRepository->getById($shippingAddressId);
            }
        }
        $addr = new Address();
        $addr->setCity($shippingAddr->getCity());
        $addr->setCountry($shippingAddr->getCountryId());
        $addr->setPostalCode($shippingAddr->getPostcode());
        if($quote->getCustomerIsGuest()){
            $addr->setRegion($shippingAddr->getRegionCode());
        }else{
            if($shippingAddr->getRegion() != null){
                $addr->setRegion($shippingAddr->getRegion()->getRegionCode());
            }
        }

        $addrLines = self::cleanStreet($shippingAddr->getStreet());
        if ($addrLines) {
            $addr->setAddressLines($addrLines);
        }

        if ($addr->getCountry() != '') {
            $req->setSearchAddress($addr);
        }

        $ci = new CustomerInfo();
        if($quote->getCustomerIsGuest()){
            $email = $shippingAddr->getEmail();
        } else {
            $email = $quote->getCustomer()->getEmail();
        }
        if ($email && $email != '') {
            $ci->setEmail($email);
        }
        $phone = $shippingAddr->getTelephone();
        if ($phone && $phone != '') {
            $ci->setPhone($phone);
        }

        if ($ci->getEmail() || $ci->getPhone()) {
            $req->setCustomer($ci);
        }
        if($addr->getPostalCode() && $addr->getCountry()) {
            $ci->setAddress($addr);
            $req->setCustomer($ci);
            $addr->setCompanyName($shippingAddr->getCompany());
            $addr->setFirstName($shippingAddr->getFirstname());
            $addr->setLastName($shippingAddr->getLastname());
            $req->setPrefillDeliveryAddress($addr);
        }

        $this->log->debug('create new session for quote: '.$quote->getId());

        $req->setCart($this->makeCart($quote));

        $resp = $this->siwClient->createSession($req);
        $this->log->info('created session '.$resp->getSession()->getId().' for '.$quote->getId());

        return $resp;
    }

    /**
     * @param Quote $quote
     * @param string|null $ingridSessionId
     * @return Cart
     */
    public function makeCart($quote, ?string $ingridSessionId=null): Cart {
        $currency = $quote->getQuoteCurrencyCode();
        $quote->collectTotals();
        $cart = new Cart();
        $cart->setCartId($quote->getId());
        $cart->setTotalValue(intval($quote->getBaseGrandTotal()*100));
        $cart->setCurrency($currency);
        $discountAmount = $quote->getBaseSubtotal() - $quote->getBaseSubtotalWithDiscount();
        if ($discountAmount > 0) {
            $this->log->debug('cart discount '.$discountAmount);
            $cart->setTotalDiscount(intval($discountAmount* 100));
        }
        $vouchers = [];

        $couponCode = $quote->getCouponCode();
        if ($couponCode != '') {
            // $couponCode is as given by the user which means it can be in any case, enforce lower here
            // TODO this adds generated coupons as well, which is probably not al that useful
            $code = mb_strtolower($couponCode);
            if ($code != 'free_shipping') { // free_shipping is Ingrid reserved name
                $vouchers = [mb_strtolower($couponCode)];
            }
        }
        //set store code in cart attributes[]
        $store = $quote->getStore();
        $storeCode = $store->getCode();
        $label = $this->slugify->slugify('store');
        $value = $this->slugify->slugify($storeCode);
        $keyset = $label.':'.$value;
        $attrs[] = $keyset;
        $attributes = [];
        $attributes[] = $keyset;
        $cart->setAttributes($attributes);

        if ($quote->getExtensionAttributes() != null) {
            $shippingAssignments = $quote->getExtensionAttributes()->getShippingAssignments();
            if ($shippingAssignments && count($shippingAssignments) > 0) {
                // sales rule free shipping gets assigned here
                if ($shippingAssignments[0]->getShipping()->getAddress()->getFreeShipping() === 1) {
                    $this->log->info('shippingAssignments free shipping');
                    $vouchers[] = 'free_shipping';
                }
            }
        }

        if (count($vouchers) > 0) {
            $this->log->info('vouchers ', ['vouchers' => $vouchers]);
            $cart->setVouchers($vouchers);
        }
        // TODO can we do something useful in the case of generated coupons?

        $logCtx = ['ingrid_session_id' => $ingridSessionId];
        $cartItems = [];
        /** @var Item[] $items */
        $items = $quote->getAllVisibleItems();
        foreach ($items as $item) {
            $logCtx = $this->cartLogCx($quote, $item, $logCtx);
            $parentItem = $item->getParentItem() ?: ($item->getParentItemId() ? $quote->getItemById($item->getParentItemId()) : null);
            if ($this->shouldSkip($parentItem, $item)) {
                $this->log->debug('skip item', $logCtx);
                continue;
            }

            $itm = $this->makeCartItem($logCtx, $item, $parentItem);
            $cartItems[] = $itm;
        }
        $cart->setItems($cartItems);

        return $cart;
    }

    public function makeCartItem(array $logCtx, Item $item, ?Item $parentItem): CartItem {
        $product = $item->getProduct();
        $store = $item->getStore();
        $qtyMtp = 1;
        $productType = $item->getProductType();

        if (isset($parentItem)) {
            $product = $parentItem->getProduct();
            $qtyMtp = $parentItem->getQty();
            $productType = $parentItem->getProductType();
        }
        $storeId = $store->getId();
        $product->setStoreId($storeId);

        $attrs = [];
        $attrs[] = 'ptype:'.$productType;

        $opts = $product->getTypeInstance()->getOrderOptions($product);
        if (array_key_exists('attributes_info', $opts)) {
            foreach ($opts['attributes_info'] as $optAttr) {
                $label = $this->slugify->slugify($optAttr['label']);
                $value = $this->slugify->slugify($optAttr['value']);
                $keyset = $label.':'.$value;
                $attrs[] = $keyset;
                $this->log->debug('item order options: '.$keyset, $logCtx);
            }
        }
        //custom attributes
        $_product = $this->productRepository->get($product->getSku());
        $attributes = $_product->getAttributes();
        foreach ($attributes as $attribute) {
            $attributesToSend = $this->config->getProductCustomAttributes($store);
            if (!in_array($attribute->getAttributeCode(), $attributesToSend) || !$attribute->getFrontend()->getValue($_product)) {
                continue;
            }
            if ($attribute->getFrontendInput() == 'multiselect') {
                $values = $attribute->getFrontend()->getValue($_product);
                $values = explode(',', $values);
                foreach ($values as $value) {
                    $label = $this->slugify->slugify($attribute->getFrontendLabel());
                    $value = $this->slugify->slugify($value);
                    $keyset = $label.':'.$value;
                    $attrs[] = $keyset;
                    $this->log->debug('item custom attributes: '.$keyset, $logCtx);
                }
            } else {
                $label = $this->slugify->slugify($attribute->getFrontendLabel());
                $value = $this->slugify->slugify($attribute->getFrontend()->getValue($_product));
                $keyset = $label.':'.$value;
                $attrs[] = $keyset;
                $this->log->debug('item custom attributes: '.$keyset, $logCtx);
            }
        }
        if (array_key_exists('is_downloadable', $opts) && $opts['is_downloadable']) {
            // Magento\Downloadable\Model\Product\Type
            $attrs[] = 'is_downloadable';
        }

        if ($item->getIsVirtual() === '1') {
            $attrs[] = 'virtual';
            $this->log->debug('attr: is virtual', $logCtx);
        }
        if ($item->getFreeShipping() === '1') {
            $attrs[] = 'free_shipping';
            $this->log->debug('attr: is free_shipping', $logCtx);
        }
        if ($item->isShipSeparately()) {
            $attrs[] = 'ship_separately';
            $this->log->debug('attr: is ship_separately', $logCtx);
        }

        $attributeSetId = $product->getAttributeSetId();
        $attributeSet = $this->attributeSetRepository->get($attributeSetId);
        $attributeSetName = $this->slugify->slugify($attributeSet->getAttributeSetName());
        $attrs[] = $attributeSetName;
        $this->log->debug('attributeSet: ['.$attributeSetId.'] '.$attributeSetName, $logCtx);

        $cats = $product->getAvailableInCategories();

        foreach ($cats as $cat) {
            $c = $this->categoryRepository->get((int) $cat, $storeId);
//                $p = $this->categoryRepository->get($c->getParentId());
//                $this->log->debug('cat ['.$c->getId().'] '.$c->getName().' pid='.$c->getParentId().' ac='.$c->getIsActive().' pac='.$p->getIsActive());

            // REVIEW ignore 'Default Category' ? it not checked on product, but still appears as active here
            $catName = $this->slugify->slugify($c->getName());
            $attrs[] = $catName;
            $this->log->debug('category ['.$c->getId().'] '.$c->getName().': '.$catName, $logCtx);
        }

        $itm = new CartItem();

        $dimen = new Dimensions();
        // TODO not tested, fix when fix is available https://github.com/magento/magento2/issues/24948
        $dimen->setLength($product->getIngridDimensionsLength());
        $dimen->setHeight($product->getIngridDimensionsHeight());
        $dimen->setWidth($product->getIngridDimensionsWidth());
        if (!($dimen->getLength() == null || $dimen->getWidth() == null || $dimen->getHeight() == null)) {
            $itm->setDimensions($this->dimensionsMm($store, $dimen));
        }

        if (count($attrs) > 0) {
            sort($attrs); // keep order deterministic
            $itm->setAttributes($attrs);
        }
        $itm->setSku($item->getSku());

        $itm->setPrice(intval(((float) $item->getBasePrice())*100));
        $discountAmount = (float) $item->getBaseDiscountAmount();
        if ($discountAmount > 0) {
            $itm->setDiscount(intval($discountAmount * 100));
        }
        $itm->setName(trim($item->getName()));
        $itm->setOutOfStock(!$product->isInStock());
        $qty = intval($item->getQty()); // getQty is a float regardless of getIsQtyDecimal
        $itm->setQuantity(abs($qty * $qtyMtp));

        if ($product->getTypeInstance()->hasWeight()) {
            $weight = $this->itemWeight($store, $item);
            $itm->setWeight($weight);
        }

        return $itm;
    }

    /**
     * The entire tree of inheritance is present as items in the cart list, which manifest as duplicate items
     * We only want to add the resolved item set to our cart
     *
     * @param Item $parentItem
     * @param Item $item
     * @return bool
     */
    private function shouldSkip($parentItem, Item $item) : bool {
        // Skip if bundle product with a dynamic price type
        if (Type::TYPE_BUNDLE == $item->getProductType() && Price::PRICE_TYPE_DYNAMIC == $item->getProduct()->getPriceType()) {
            return true;
        }

        if (!$parentItem) {
            return false;
        }

        // Skip if child product of a non bundle parent
        if (Type::TYPE_BUNDLE != $parentItem->getProductType()) {
            return true;
        }

        // Skip if non bundle product or if bundled product with a fixed price type
        if (Type::TYPE_BUNDLE != $parentItem->getProductType() || Price::PRICE_TYPE_FIXED == $parentItem->getProduct()->getPriceType()) {
            return true;
        }

        return false;
    }

    private function cartLogCx(CartInterface $quote, Item $item, array $ctx = []) : array {
        $ctx['quote_id'] = $quote->getId();
        $ctx['sku'] = $item->getSku();
        return $ctx;
    }

    private function itemWeight(Store $store, Item $item): int {
        $weight = (float) $item->getWeight();
        if ($weight == null) {
            $this->log->notice('null weight for '.$item->getSku());
            return 0;
        }
        return $this->weightGram($store, $weight);
    }

    public function weightGram(Store $store, float $weight):int {
        $unit = $this->config->weightUnit($store);
        $this->log->debug('weight unit '.$unit);
        if ($unit === 'kg' || $unit === 'kgs') {
            return intval($weight * 1000);
        } elseif ($unit === 'lb' || $unit === 'lbs') {
            return intval($weight * 454);
        } else {
            $this->log->error('unknown weight unit "'.$unit.'"');
            return 0;
        }
    }

    public function dimensionsMm(Store $store, Dimensions $dimens): Dimensions {

        $out = new Dimensions();
        $out->setHeight(intval($dimens->getHeight()));
        $out->setWidth(intval($dimens->getWidth()));
        $out->setLength(intval($dimens->getLength()));

        return $out;
    }

    /**
     * @param string $sessionId
     * @param Order $order
     * @return CompleteSessionResponse
     * @throws UnauthorizedException
     */
    private function completeSession(string $sessionId, Order $order): CompleteSessionResponse {
        $bill = $order->getBillingAddress();
        $orderId = self::mageOrderId($order);
        $req = new CompleteSessionRequest();
        $req->setId($sessionId);
        $req->setExternalId($orderId);

        $addr = self::mkAddress($bill, $order->getCustomerFirstname(), $order->getCustomerLastname());
        $customer = self::mkCustomer($addr, $bill);

        $req->setCustomer($customer);

        $resp = $this->siwClient->completeSession($req);
        $session = $resp->getSession();
        $this->log->info('ingrid completed session tos: '.$session->getTosId(), ['order_id' => $order->getId(), 'quote_id' => $order->getQuoteId(), 'external_id' => $orderId]);

//        $this->checkoutSession->getData(IngridSessionService::SESSION_ID_KEY, true);
//        $this->log->debug('cleared ingrid session from checkout session');
        return $resp;
    }

    /**
     * @param string|null $sessionId
     * @param Order $order
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function complete(?string $sessionId, Order $order) {
        $logCtx = ['order_id' => $order->getId(), 'quote_id' => $order->getQuoteId(), 'ingrid_session_id' => $sessionId];
        if ($sessionId === null) {
            $this->log->warning('no ingrid session present, saving fallback', $logCtx);
            $this->saveFallbackSession($order);
            $this->log->warning('completed Ingrid session using local fallback', $logCtx);
            return;
        }

        try {
            $sessionResp = $this->completeSession($sessionId, $order);
            $this->saveSession($order, $sessionResp->getSession());
        } catch (\Exception $e) {
            $this->log->error('failed to complete Ingrid session: '.$e->getMessage(), $logCtx);
            $this->saveFallbackSession($order, $sessionId);
            $this->log->warning('completed Ingrid session using local fallback', $logCtx);
        }
    }

    public static function mageOrderId(Order $order): string {
        $orderId = ''.$order->getIncrementId(); // this is the order id as shown in admin and received by the customer, entityId is DB id
        return $orderId;
    }

    /**
     * @param Order $order
     * @param Session $session
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    private function saveSession(Order $order, Session $session) {
        $this->log->debug('save completed session');
        $ingridSession = $this->ingridSessionRepository->getByIngridSessionId($session->getId());
        $orderId = self::mageOrderId($order);

        $this->log->debug('order id '.$orderId);

        $result = $session->getDeliveryGroups()[0];
        $shipping = $result->getShipping();

        $ingridSession->setTosId($result->getTosId());
        $ingridSession->setTest($this->config->isTestMode());
        $ingridSession->setIsCompleted(true);
        $ingridSession->setOrderId((int) $order->getEntityId());
        $ingridSession->setOrderIncrementId($orderId);
        $ingridSession->setCategoryName($result->getCategory()->getName());
        $ingridSession->setShippingMethod($shipping->getCarrierProductId());


        $externalMethodId = $shipping->getExternalMethodId();
        if ($externalMethodId) {
            $ingridSession->setExternalMethodId($externalMethodId);
        }

        $carrier = $shipping->getCarrier();
        if ($carrier) {
            if ($carrier === 'Instabox') {
                $ingridSession->setCarrier($carrier.' ('.$shipping->getMeta()['isb.availability_token'].')');
            } else {
                $ingridSession->setCarrier($carrier);
            }
        }
        $product = $shipping->getProduct();
        if ($product) {
            $ingridSession->setProduct($product);
            $deliveryAddons = $shipping->getDeliveryAddons();
            if ($deliveryAddons && is_array($deliveryAddons)) {
                $addons = "";
                foreach ($deliveryAddons as $addon) {
                    $addons .= $addon->getExternalAddonId()." ";
                }
                $ingridSession->setProduct($product." (". $addons .")");
            }
        }

        $loc = $result->getAddresses()->getLocation();
        if ($loc) {
            $ingridSession->setLocationId($loc->getExternalId());
            $ingridSession->setLocationName($loc->getName());
        }

        $ts = $shipping->getDeliveryTime();
        if ($ts) {
            $ingridSession->setTimeSlotId($ts->getId());
            $ingridSession->setTimeSlotStart($ts->getStart());
            $ingridSession->setTimeSlotEnd($ts->getEnd());
        }
        $this->log->debug('save commit completed session');
        $this->ingridSessionRepository->save($ingridSession);
        $this->log->info('ingrid completed session updated repo order: '.$orderId);
    }

    private static function localId(): string {
        return bin2hex(random_bytes(16));
    }

    private static function localTosId(): string {
        return bin2hex(random_bytes(13));
    }

    /**
     * @param Order $order
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    private function saveFallbackSession(Order $order, ?string $sessionId=null) {
        $this->log->debug('save completed session');
        if ($sessionId === null) {
            $sessionId = self::localId();
        }
        $ingridSession = $this->ingridSessionRepository->getByIngridSessionId($sessionId);
        $orderId = self::mageOrderId($order);
        $store = $order->getStore();

        $this->log->debug('order id '.$orderId);

        $ingridSession->setTosId(self::localTosId());
        $ingridSession->setTest($this->config->isTestMode());
        $ingridSession->setIsCompleted(true);
        $ingridSession->setOrderId((int) $order->getEntityId());
        $ingridSession->setOrderIncrementId($orderId);
        $ingridSession->setCategoryName($this->config->fallbackName($store));
        $ingridSession->setShippingMethod('ingrid-fallback');
        $ingridSession->setExternalMethodId($this->config->fallbackShippingMethod($store));

        $this->log->debug('save commit completed session');
        $this->ingridSessionRepository->save($ingridSession);
        $this->log->info('ingrid completed session updated repo order: '.$orderId);
    }

    public function purchaseCountry() : string {
        return $this->config->getPurchaseCountry($this->checkoutSession->getQuote()->getStore());
    }

    public function purchaseCurrency() : string {
        return $this->checkoutSession->getQuote()->getQuoteCurrencyCode();
    }

    /**
     * @param string $sessionId
     * @param CheckoutUpdateRequest $req
     * @throws ApiException
     * @throws UnauthorizedException
     */
    public function update(string $sessionId, CheckoutUpdateRequest $req) {
        $updateReq = new UpdateSessionRequest();
        $updateReq->setId($sessionId);

        $locale = $this->config->getLocale();
        $this->log->debug('update locale '.$locale);
        $updateReq->setPurchaseCurrency($this->purchaseCurrency());
        $updateReq->setPurchaseCountry($this->purchaseCountry());
        $updateReq->setLocales([$locale]);

        $addr = new Address();
        $addr->setCountry($req->address->countryId);
        if ($req->address->city && $req->address->city !== '') {
            $addr->setCity($req->address->city);
        }
        $addr->setPostalCode($req->address->postcode);

        $addrLines = self::cleanStreet($req->address->street);

        if ($addrLines !== null) {
            $addr->setAddressLines($addrLines);
        }

        $addr->setRegion($req->address->regionCode);

        $updateReq->setSearchAddress($addr);

        $cust = new CustomerInfo();
        $cust->setEmail($req->email);
        $cust->setPhone($req->address->telephone);
        if ($addr->getCountry() != '') {
            $cust->setAddress($addr);
        }
        $updateReq->setCustomer($cust);

        $this->siwClient->updateSession($updateReq);
    }

    private function mapAddress($quote, $address, $type, $isSearchAddress = false): void
    {
        $region = $this->regionFactory->create();
        if($address->getCountry() == "US"){
            $region->loadByCode($address->getRegion(), $address->getCountry());
        } else {
            $region->loadByName($address->getRegion(), $address->getCountry());
        }
        if($quote->getCustomerIsGuest()){
            if($type == "shipping") {
                $quote->getShippingAddress()
                    ->setCountryId($address->getCountry() ?? $quote->getShippingAddress()->getCountryId())
                    ->setPostcode($address->getPostalcode() ?? $quote->getShippingAddress()->getPostcode())
                    ->setCity($address->getCity() ?? $quote->getShippingAddress()->getCity())
                    ->setFirstname($address->getFirstname() ?? $quote->getShippingAddress()->getFirstname())
                    ->setLastname($address->getLastname() ?? $quote->getShippingAddress()->getLastname())
                    ->setStreet($isSearchAddress ? ($address->getAddressLines() ?? $quote->getShippingAddress()->getStreet()) : ($address->getStreet()? [$address->getStreet()." ".$address->getStreetNumber()] : $quote->getShippingAddress()->getStreet()))
                    ->setTelephone($isSearchAddress ? $quote->getShippingAddress()->getTelephone() : $address->getPhone())
                    ->setRegionCode($region->getCode() ?? $quote->getShippingAddress()->getRegionCode())
                    ->setRegion($region->getName() ?? $quote->getShippingAddress()->getRegion())
                    ->setRegionId($region->getId() ?? $quote->getShippingAddress()->getRegionId());
            } else {
                $quote->getBillingAddress()
                    ->setCountryId($address->getCountry() ?? $quote->getBillingAddress()->getCountryId())
                    ->setPostcode($address->getPostalcode() ?? $quote->getBillingAddress()->getPostcode())
                    ->setCity($address->getCity() ?? $quote->getBillingAddress()->getCity())
                    ->setFirstname($address->getFirstname() ?? $quote->getBillingAddress()->getFirstname())
                    ->setLastname($address->getLastname() ?? $quote->getBillingAddress()->getLastname())
                    ->setStreet($isSearchAddress ? ($address->getAddressLines() ?? $quote->getBillingAddress()->getStreet()) : ($address->getStreet() ? [$address->getStreet()." ".$address->getStreetNumber()] : $quote->getBillingAddress()->getStreet()))
                    ->setTelephone($isSearchAddress ? $quote->getBillingAddress()->getTelephone() : $address->getPhone())
                    ->setRegionCode($region->getCode() ?? $quote->getBillingAddress()->getRegionCode())
                    ->setRegion($region->getName() ?? $quote->getBillingAddress()->getRegion())
                    ->setRegionId($region->getId() ?? $quote->getBillingAddress()->getRegionId());
            }
        } else {
            if($type == "shipping") {
                $shippingAddressId = $quote->getCustomer()->getDefaultShipping();
                if($shippingAddressId == null){
                    return;
                }
                $shippingAddress = $this->addressRepository->getById($shippingAddressId);
                $shippingAddress
                    ->setCountryId($address->getCountry() ?? $shippingAddress->getCountryId())
                    ->setPostcode($address->getPostalcode() ?? $shippingAddress->getPostcode())
                    ->setCity($address->getCity() ?? $shippingAddress->getCity())
                    ->setFirstname($address->getFirstname() ?? $shippingAddress->getFirstname())
                    ->setLastname($address->getLastname() ?? $shippingAddress->getLastname())
                    ->setStreet($isSearchAddress ? ($address->getAddressLines()?? $quote->getShippingAddress()->getStreet()) : ($address->getStreet() ? [$address->getStreet()." ".$address->getStreetNumber()] : $shippingAddress->getStreet()))
                    ->setTelephone($isSearchAddress ? $shippingAddress->getTelephone() : $address->getPhone());
                    if($region->getId() != null){
                        $shippingAddress->getRegion()
                            ->setRegionCode($region->getCode())
                            ->setRegion($region->getName())
                            ->setRegionId($region->getId());
                    }
                $this->addressRepository->save($shippingAddress);
            } else {
                $billingAddressId = $quote->getCustomer()->getDefaultBilling();
                if($billingAddressId == null){
                    return;
                }
                $billingAddress = $this->addressRepository->getById($billingAddressId);
                $billingAddress
                    ->setCountryId($address->getCountry() ?? $billingAddress->getCountryId())
                    ->setPostcode($address->getPostalcode() ?? $billingAddress->getPostcode())
                    ->setCity($address->getCity() ?? $billingAddress->getCity())
                    ->setFirstname($address->getFirstname() ?? $billingAddress->getFirstname())
                    ->setLastname($address->getLastname() ?? $billingAddress->getLastname())
                    ->setStreet($isSearchAddress ? ($address->getAddressLines() ?? $billingAddress->getStreet()) : ($address->getStreet() ? [$address->getStreet()." ".$address->getStreetNumber()] : $billingAddress->getStreet()))
                    ->setTelephone($isSearchAddress ? $billingAddress->getTelephone() : $address->getPhone());
                if($region->getId() != null){
                    $billingAddress->getRegion()
                        ->setRegionCode($region->getCode())
                        ->setRegion($region->getName())
                        ->setRegionId($region->getId());
                }
                $this->addressRepository->save($billingAddress);
            }
        }
    }

}
