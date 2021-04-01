<?php
namespace Ingrid\Checkout\Controller\Api;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;

/**
 * Base action class for all api classes
 */
abstract class BaseAction extends Action implements CsrfAwareActionInterface {

    /**
     * Create exception in case CSRF validation failed.
     * Return null if default exception will suffice.
     *
     * @param RequestInterface $request
     * @return InvalidRequestException|null
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException {
        return null;
    }

    /**
     * Perform custom request validation.
     * Return null if default validation is needed.
     *
     * @param RequestInterface $request
     * @return bool|null
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    public function validateForCsrf(RequestInterface $request): ?bool {
        return null;
    }

    /**
     * Extending the logging context
     *
     * @param array $context
     * @return array
     */
    protected function getExtendedContext($context = []) {
        if (!isset($context['action'])) {
            $context['action'] = $this->getRequest()->getRequestUri();
        }

        return $context;
    }
}
