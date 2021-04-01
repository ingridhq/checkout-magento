<?php

declare(strict_types=1);

namespace Ingrid\Checkout\Model;

use Ingrid\Checkout\Api\IngridSessionInterface;
use Ingrid\Checkout\Api\IngridSessionRepositoryInterface;
use Ingrid\Checkout\Model\ResourceModel\IngridSession as IngridSessionResource;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class IngridSessionRepository implements IngridSessionRepositoryInterface {

    /**
     * @var IngridSessionFactory
     */
    protected $factory;

    /**
     * @var IngridSessionResource
     */
    protected $resourceModel;

    /**
     * @var array
     */
    protected $instances = [];

    /**
     * @var array
     */
    protected $instancesById = [];

    /**
     * QuoteRepository constructor.
     *
     * @param IngridSessionFactory  $factory
     * @param IngridSessionResource $resourceModel
     * @codeCoverageIgnore
     */
    public function __construct(
        IngridSessionFactory $factory,
        IngridSessionResource $resourceModel
    ) {
        $this->factory = $factory;
        $this->resourceModel = $resourceModel;
    }

    /**
     * {@inheritdoc}
     */
    public function getByIngridSessionId(string $ingridSessionId) : IngridSession {
        $session = $this->factory->create();

        $entityId = $this->resourceModel->getIdByIngridSessionId($ingridSessionId);
        if (!$entityId) {
            $session->setSessionId($ingridSessionId);
            return $session;
        }
        $this->resourceModel->load($session, $entityId);
        return $session;
    }

    /**
     * @param int $orderId
     * @return IngridSession
     * @throws NoSuchEntityException
     */
    public function getByOrderId(int $orderId) : IngridSession {
        $session = $this->factory->create();

        $entityID = $this->resourceModel->getIdByOrderId($orderId);
        if (!$entityID) {
            throw NoSuchEntityException::singleField('order_id', $orderId);
        }

        $this->resourceModel->load($session, $entityID);
        return $session;
    }

    /**
     * {@inheritdoc}
     */
    public function save(IngridSessionInterface $ingridSession) {
        try {
            /** @noinspection PhpParamsInspection */
            return $this->resourceModel->save($ingridSession);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }
    }
}
