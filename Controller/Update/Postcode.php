<?php
namespace Ingrid\Checkout\Controller\Update;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Ingrid\Checkout\Api\SiwClientInterface;
use Ingrid\Checkout\Api\Siw\Model\Address;
use Ingrid\Checkout\Api\Siw\Model\UpdateSessionRequest;

class Postcode extends Action implements HttpPostActionInterface
{

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var SiwClientInterface
     */
    private $siwClient;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        \Magento\Checkout\Model\Session $_checkoutSession,
        SiwClientInterface $siwClient,
        )
    {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_checkoutSession = $_checkoutSession;
        $this->siwClient = $siwClient;
    }
    public function execute() {
        $resultJson = $this->resultJsonFactory->create();
        $ingridSessionId = $this->_checkoutSession->getQuote()->getIngridSessionId();
        $postcode = $this->getRequest()->getParam('postcode');
        if (!$postcode || !$ingridSessionId) {
            return $resultJson->setData(['updated' => 'false']);
        }
        $updateReq = new UpdateSessionRequest();
        $updateReq->setId($ingridSessionId);
        $addr = new Address();
        $addr->setPostalCode($postcode);
        $addr->setCountry($this->_checkoutSession->getQuote()->getShippingAddress()->getCountryId());
        if ($this->_checkoutSession->getQuote()->getShippingAddress()->getRegion()) {
            $addr->setRegion($this->_checkoutSession->getQuote()->getShippingAddress()->getRegion());
        }
        $updateReq->setSearchAddress($addr);
        try {
            $this->siwClient->updateSession($updateReq);
        } catch (\Exception $e) {
            return $resultJson->setData(['updated' => 'false']);
        }

        return $resultJson->setData(['updated' => 'true']);
    }
}
