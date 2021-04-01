<?php
declare(strict_types=1);

namespace Ingrid\Checkout\Api;

use Exception;
use Ingrid\Checkout\Api\Siw\Model\CompleteSessionRequest;
use Ingrid\Checkout\Api\Siw\Model\CompleteSessionResponse;
use Ingrid\Checkout\Api\Siw\Model\CreateSessionRequest;
use Ingrid\Checkout\Api\Siw\Model\CreateSessionResponse;
use Ingrid\Checkout\Api\Siw\Model\GetSessionResponse;
use Ingrid\Checkout\Api\Siw\Model\UpdateSessionRequest;
use Ingrid\Checkout\Api\Siw\Model\UpdateSessionResponse;
use Psr\Log\LoggerInterface;

/**
 * SiwDownClient simulated communication issues with Ingrid for testing
 * @package Ingrid\Checkout\Api
 */
class SiwDownClient implements SiwClientInterface {
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SiwDownClient constructor.
     */
    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    /**
     * @param CompleteSessionRequest $req
     * @return CompleteSessionResponse
     * @throws Exception
     */
    public function completeSession(CompleteSessionRequest $req): CompleteSessionResponse {
        $this->logger->info('/session.complete', ['session_id' => $req->getId()]);
        throw new Exception("broken");
    }

    /**
     * @param CreateSessionRequest $req
     * @return CreateSessionResponse
     * @throws Exception
     */
    public function createSession(CreateSessionRequest $req): CreateSessionResponse {
        $this->logger->info('/session.create', ['external_id' => $req->getExternalId()]);
        throw new Exception("broken");
    }

    /**
     * @param string $id
     * @return GetSessionResponse
     * @throws Exception
     */
    public function getSession(string $id): GetSessionResponse {
        $this->logger->info('/session.get', ['session_id' => $id]);
        throw new Exception("broken");
    }

    /**
     * @param UpdateSessionRequest $req
     * @return UpdateSessionResponse
     * @throws Exception
     */
    public function updateSession(UpdateSessionRequest $req): UpdateSessionResponse {
        $this->logger->info('/session.update', ['session_id' => $req->getId(), 'external_id' => $req->getExternalId()]);
        throw new Exception("broken");
    }
}
