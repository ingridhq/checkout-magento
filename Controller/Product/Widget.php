<?php
namespace Ingrid\Checkout\Controller\Product;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class Widget extends Action {

    private $resultJsonFactory;
    protected $_checkoutSession;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        \Magento\Checkout\Model\Session $_checkoutSession
        )
    {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_checkoutSession = $_checkoutSession;
    }
    public function execute() {

        $resultJson = $this->resultJsonFactory->create();
        $quote = $this->_checkoutSession->getQuote();
        if($quote->getId()){
            //cart object
            $cart = [];
            $cart['cart_id']= $quote->getId();
            $cart['attributes'] = [];
            $cart['total_value']= (int)($quote->getGrandTotal() * 100);
            $cart['items'] = $this->mapItems($quote);
            
            return $resultJson->setData(['cart' => $cart]);
        }

        return $resultJson->setData(['cart' => '']);

    }

    public function mapItems($quote): array {
        $items = [];
        $quoteItems = $quote->getAllVisibleItems();
        foreach ($quoteItems as $key => $quoteItem) {
            $items[$key]['name'] = $quoteItem->getName();
            $items[$key]['price'] = (int)($quoteItem->getPrice() * 100);
            $items[$key]['sku'] = $quoteItem->getSku();
        }
        return $items;
    }
}
