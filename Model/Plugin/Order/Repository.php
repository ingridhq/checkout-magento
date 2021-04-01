<?php
declare(strict_types=1);

namespace Ingrid\Checkout\Model\Plugin\Order;

use Ingrid\Checkout\Model\IngridSessionRepository;
use Ingrid\Checkout\Model\Order;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;

class Repository {
    /**
     * @var IngridSessionRepository
     */
    private $ingridSessionRepository;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Repository constructor.
     */
    public function __construct(IngridSessionRepository $ingridSessionRepository, LoggerInterface $logger) {
        $this->ingridSessionRepository = $ingridSessionRepository;
        $this->logger = $logger;
    }

    public function afterGet(OrderRepositoryInterface $subject, OrderInterface $entity) {
        $orderId = (int)$entity->getEntityId();
        try {
            $this->logger->debug('ingrid exa get '.$orderId);
            $session = $this->ingridSessionRepository->getByOrderId($orderId);

            $extensionAttributes = $entity->getExtensionAttributes();
            $extensionAttributes->setIngrid(new Order($session));

            $entity->setExtensionAttributes($extensionAttributes);
        } catch (\Exception $e) {
            $this->logger->error('failed to load ingrid order for '.$orderId.' :'.$e->getMessage());
        }

        return $entity;
    }

    public function afterGetList(OrderRepositoryInterface $subject, OrderSearchResultInterface $searchCriteria): OrderSearchResultInterface {
        foreach ($searchCriteria->getItems() as $entity) {
            try {
                $orderId = (int) $entity->getEntityId();
                $this->logger->debug('ingrid exa get '.$orderId);
                $session = $this->ingridSessionRepository->getByOrderId($orderId);

                $extensionAttributes = $entity->getExtensionAttributes();
                $extensionAttributes->setIngrid(new Order($session));
                $entity->setExtensionAttributes($extensionAttributes);
            } catch (NoSuchEntityException $e) {
                $this->logger->debug('no ingrid order for '.$orderId.' '.$e->getMessage());
            }
        }

        return $searchCriteria;
    }

    public function afterSave(OrderRepositoryInterface $subject, OrderInterface $entity) {
        // noop: data is read only
        return $entity;
    }
}
