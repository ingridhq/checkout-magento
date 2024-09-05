<?php
namespace Ingrid\Checkout\Plugin\Klarna\Kco\Model;

use Magento\Checkout\Model\Session;
/**
 * Class WorkflowProvider
 */
class WorkflowProvider
{

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * WorkflowProvider constructor.
     */
    public function __construct(
        Session $checkoutSession
    ) {
        $this->checkoutSession = $checkoutSession;
    }

    public function afterGetMagentoQuote(
        \Klarna\Kco\Model\WorkflowProvider $subject,
        \Magento\Quote\Api\Data\CartInterface $result
    ) {
        if ($result->getId() != $this->checkoutSession->getQuote()->getId()) {
            $this->checkoutSession->replaceQuote($result);
        }
        return $result;
    }
}
