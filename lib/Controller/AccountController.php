<?php

declare(strict_types=1);

namespace OCA\GitIntegration\Controller;

use OCA\GitIntegration\AppInfo\Application;
use OCA\GitIntegration\Service\AccountService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

class AccountController extends Controller {
    private AccountService $service;
    private ?string $userId;

    public function __construct(IRequest $request, AccountService $service, ?string $userId) {
        parent::__construct(Application::APP_ID, $request);
        $this->service = $service;
        $this->userId = $userId;
    }

    /**
     * @NoAdminRequired
     */
    public function list(): JSONResponse {
        $accounts = $this->service->findAll($this->userId);
        return new JSONResponse($accounts);
    }

    /**
     * @NoAdminRequired
     */
    public function get(int $id): JSONResponse {
        try {
            $account = $this->service->find($id, $this->userId);
            return new JSONResponse($account);
        } catch (\Exception $e) {
            return new JSONResponse(['error' => $e->getMessage()], Http::STATUS_NOT_FOUND);
        }
    }

    /**
     * @NoAdminRequired
     */
    public function create(
        string $provider,
        string $providerUrl,
        string $username,
        string $accessToken
    ): JSONResponse {
        try {
            $account = $this->service->create(
                $this->userId,
                $provider,
                $providerUrl,
                $username,
                $accessToken
            );
            return new JSONResponse($account, Http::STATUS_CREATED);
        } catch (\Exception $e) {
            return new JSONResponse(['error' => $e->getMessage()], Http::STATUS_BAD_REQUEST);
        }
    }

    /**
     * @NoAdminRequired
     */
    public function update(int $id, string $username, string $accessToken): JSONResponse {
        try {
            $account = $this->service->update($id, $this->userId, $username, $accessToken);
            return new JSONResponse($account);
        } catch (\Exception $e) {
            return new JSONResponse(['error' => $e->getMessage()], Http::STATUS_BAD_REQUEST);
        }
    }

    /**
     * @NoAdminRequired
     */
    public function delete(int $id): JSONResponse {
        try {
            $this->service->delete($id, $this->userId);
            return new JSONResponse(['success' => true]);
        } catch (\Exception $e) {
            return new JSONResponse(['error' => $e->getMessage()], Http::STATUS_BAD_REQUEST);
        }
    }

    /**
     * @NoAdminRequired
     */
    public function test(int $id): JSONResponse {
        try {
            $result = $this->service->testConnection($id, $this->userId);
            return new JSONResponse($result);
        } catch (\Exception $e) {
            return new JSONResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], Http::STATUS_BAD_REQUEST);
        }
    }
}
