<?php
declare(strict_types=1);

namespace Ingrid\Checkout\Controller\Update;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Checkout\Model\Session;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * Call to reload Ingrid shipping rate.
 * After successful call, frontend should trigger reload Magento totals.
 *
 * @package Ingrid\Checkout\Controller\Update
 */
class Briqpay extends \Magento\Checkout\Controller\Action
{
    /**
     * @var Session
     */
    protected $_checkoutSession;

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var \Briqpay\Checkout\Model\Checkout\BriqpayCheckout
     */
    private $checkout;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param AccountManagementInterface $accountManagement
     * @param Session $_checkoutSession
     * @param CartRepositoryInterface  $quoteRepository
     * @param SignatureHasher $signatureHasher
     * @param SignatureHasher $signatureHasher
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $accountManagement,
        Session $_checkoutSession,
        CartRepositoryInterface  $quoteRepository,
        array $data = [] //we set class Briqpay\Checkout\Model\Checkout\BriqpayCheckout via di.xml to avoid briqpay dependencies error when briqpay not installed
    ) {
        parent::__construct(
            $context,
            $customerSession,
            $customerRepository,
            $accountManagement
        );
        $this->_checkoutSession = $_checkoutSession;
        $this->quoteRepository = $quoteRepository;
        $this->_data = $data;
    }

    /**
     * @return ResultInterface|ResponseInterface
     */
    public function execute() {
        $quote = $this->_checkoutSession->getQuote();
        //$quote->getExtensionAttributes()->setBriqpayCartSignature($signature."_ingrid");
        $quote->setBriqpayCartSignature('change');
        $quote->setTotalsCollectedFlag(false)->collectTotals();
        $this->quoteRepository->save($quote);
        $briqpayCheckout = $this->_data['checkout'];
        if ($briqpayCheckout instanceof \Briqpay\Checkout\Model\Checkout\BriqpayCheckout) {
            $briqpayCheckout->initCheckout();
        }
        $response = [];
        $blocks = ['cart','messages','briqpay','coupon','newsletter'];
        $response['ok'] = true;
        if ($blocks) {
            $this->_view->loadLayout('briqpay_checkout_order_update');
            foreach ($blocks as $id) {
                $name = "briqpay_checkout.{$id}";
                $block = $this->_view->getLayout()->getBlock($name);
                if ($block) {
                    $response['updates'][$id] = $block->toHtml();
                }
            }
        }

        $this->getResponse()->setBody(json_encode($response));
        
    }
}