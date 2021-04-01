<?php
declare(strict_types=1);

namespace Ingrid\Checkout\Service;

use Ingrid\Checkout\Api\Siw\Model\Session;

class SessionHolder {

    /**
     * @var Session
     */
    public $session;
    /**
     * @var string
     */
    public $htmlSnippet;

    public function __construct(Session $session, string $htmlSnippet) {
        $this->session = $session;
        $this->htmlSnippet = $htmlSnippet;
    }
}
