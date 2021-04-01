<?php

declare(strict_types=1);

namespace Ingrid\Checkout\Api;

use Ingrid\Checkout\Api\Siw\Model\CompleteSessionRequest;
use Ingrid\Checkout\Api\Siw\Model\CompleteSessionResponse;
use Ingrid\Checkout\Api\Siw\Model\CreateSessionRequest;
use Ingrid\Checkout\Api\Siw\Model\CreateSessionResponse;
use Ingrid\Checkout\Api\Siw\Model\GetSessionResponse;
use Ingrid\Checkout\Api\Siw\Model\UpdateSessionRequest;
use Ingrid\Checkout\Api\Siw\Model\UpdateSessionResponse;

interface SiwClientInterface {
    public function completeSession(CompleteSessionRequest $req) : CompleteSessionResponse;

    public function createSession(CreateSessionRequest $req) : CreateSessionResponse;

    public function getSession(string $id) : GetSessionResponse;

    public function updateSession(UpdateSessionRequest $req) : UpdateSessionResponse;
}
