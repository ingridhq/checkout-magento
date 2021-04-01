<?php

declare(strict_types=1);

namespace Ingrid\Checkout\Api;

use Exception;
use GuzzleHttp\Client;
use function GuzzleHttp\default_user_agent;
use Ingrid\Checkout\Api\Siw\Api\DefaultApi as SiwApi;
use Ingrid\Checkout\Api\Siw\ApiException;
use Ingrid\Checkout\Api\Siw\Configuration;
use Ingrid\Checkout\Api\Siw\Model\CompleteSessionRequest;
use Ingrid\Checkout\Api\Siw\Model\CompleteSessionResponse;
use Ingrid\Checkout\Api\Siw\Model\CreateSessionRequest;
use Ingrid\Checkout\Api\Siw\Model\CreateSessionResponse;
use Ingrid\Checkout\Api\Siw\Model\GetSessionResponse;
use Ingrid\Checkout\Api\Siw\Model\UpdateSessionRequest;
use Ingrid\Checkout\Api\Siw\Model\UpdateSessionResponse;
use Ingrid\Checkout\Helper\Build;
use Ingrid\Checkout\Helper\Config;
use Ingrid\Checkout\Model\Exception\NoApiKeyException;
use Ingrid\Checkout\Model\Exception\UnauthorizedException;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Module\ModuleListInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\HTTP\Client\CurlFactory;
use Magento\Framework\Serialize\Serializer\Json;

class SiwClient implements SiwClientInterface {

    /**
     * @var SiwApi
     */
    private $client;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var bool
     */
    private $isDevMode;

    /**
     * SiwClient constructor.
     * @param LoggerInterface $logger
     * @param Config $config
     * @param ProductMetadataInterface $mage
     * @param ModuleListInterface $moduleList
     * @throws NoApiKeyException
     */
    public function __construct(
        LoggerInterface $logger,
        Config $config,
        ProductMetadataInterface $mage,
        ModuleListInterface $moduleList,
        CurlFactory $curlFactory,
        Json $json
        ) {
        $this->logger = $logger;

        $ua = self::buildUA($config, $moduleList, $mage);
        $conf = new Configuration();
        $conf->setApiKey('Authorization', $config->encodedApiKey());
        $conf->setHost($config->siwBaseUrl());
        $conf->setApiKeyPrefix('Authorization', 'Bearer');
        $conf->setUserAgent($ua);
        $this->curlFactory = $curlFactory;
        $this->json = $json;

        $httpConf = [
            'timeout' => 30,
        ];

        if (defined('CURL_HTTP_VERSION_2_0')) {
            $httpConf['curl'] = [
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
            ];
        }

        $httpClient = new Client($httpConf);
        $this->client = new SiwApi($httpClient, $conf, $this->curlFactory, $this->json);
        $this->isDevMode = $config->isMageDevMode();
    }

    private static function buildUA(Config $config, ModuleListInterface $moduleList, ProductMetadataInterface $mage): string {
        $modVersion = $moduleList->getOne(Config::MODULE_NAME)['setup_version'];
        $mageDev = $config->isMageDevMode() ? '/dev' : '';
        $ua = [
            default_user_agent(),
            $mage->getName().'/'.$mage->getEdition().'/'.$mage->getVersion().$mageDev,
            Config::MODULE_NAME.'/'.$modVersion.'/'.Build::BUILD.'/'.Build::TS,
        ];
        return join(' ', $ua);
    }

    /**
     * @param CompleteSessionRequest $req
     * @return CompleteSessionResponse
     * @throws UnauthorizedException|ApiException
     * @throws Exception
     */
    public function completeSession(CompleteSessionRequest $req): CompleteSessionResponse {
        if ($this->isDevMode) {
            $this->logger->info('/session.complete', ['session_id' => $req->getId()]);
        }
        try {
            return $this->client->completeSession($req);
        } catch (ApiException $e) {
            if ($e->getCode() == 401) {
                throw new UnauthorizedException();
            }
            throw $e;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @param CreateSessionRequest $req
     * @return CreateSessionResponse
     * @throws ApiException
     * @throws UnauthorizedException
     * @throws Exception
     */
    public function createSession(CreateSessionRequest $req): CreateSessionResponse {
        if ($this->isDevMode) {
            $this->logger->info('/session.create', ['external_id' => $req->getExternalId()]);
        }
        try {
            return $this->client->createSession($req);
        } catch (ApiException $e) {
            if ($e->getCode() == 401) {
                throw new UnauthorizedException();
            }
            throw $e;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @param string $id
     * @return GetSessionResponse
     * @throws ApiException
     * @throws UnauthorizedException
     * @throws Exception
     */
    public function getSession(string $id): GetSessionResponse {
        if ($this->isDevMode) {
            $this->logger->info('/session.get', ['session_id' => $id]);
        }
        try {
            return $this->client->getSession($id);
        } catch (ApiException $e) {
            if ($e->getCode() == 401) {
                throw new UnauthorizedException();
            }
            throw $e;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @param UpdateSessionRequest $req
     * @return UpdateSessionResponse
     * @throws ApiException
     * @throws UnauthorizedException
     * @throws Exception
     */
    public function updateSession(UpdateSessionRequest $req): UpdateSessionResponse {
        if ($this->isDevMode) {
            $this->logger->info('/session.update', ['session_id' => $req->getId(), 'external_id' => $req->getExternalId()]);
        }
        try {
            return $this->client->updateSession($req);
        } catch (ApiException $e) {
            // handle 403 Forbidden: already completed
            if ($e->getCode() == 401) {
                throw new UnauthorizedException();
            }
            throw $e;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
