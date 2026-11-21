<?php

declare(strict_types=1);

namespace OCA\GitIntegration\Controller;

use OCA\GitIntegration\AppInfo\Application;
use OCA\GitIntegration\Service\SyncService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

class SyncController extends Controller {
    private SyncService $service;
    private ?string $userId;

    public function __construct(IRequest $request, SyncService $service, ?string $userId) {
        parent::__construct(Application::APP_ID, $request);
        $this->service = $service;
        $this->userId = $userId;
    }

    /**
     * @NoAdminRequired
     */
    public function sync(int $id): JSONResponse {
        try {
            $result = $this->service->syncRepository($id, $this->userId);
            return new JSONResponse($result);
        } catch (\Exception $e) {
            return new JSONResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], Http::STATUS_BAD_REQUEST);
        }
    }

    /**
     * @NoAdminRequired
     */
    public function status(int $id): JSONResponse {
        try {
            $status = $this->service->getSyncStatus($id, $this->userId);
            return new JSONResponse($status);
        } catch (\Exception $e) {
            return new JSONResponse(['error' => $e->getMessage()], Http::STATUS_BAD_REQUEST);
        }
    }

    /**
     * @NoAdminRequired
     */
    public function history(int $id, int $limit = 50): JSONResponse {
        try {
            $history = $this->service->getSyncHistory($id, $limit);
            return new JSONResponse($history);
        } catch (\Exception $e) {
            return new JSONResponse(['error' => $e->getMessage()], Http::STATUS_BAD_REQUEST);
        }
    }

    /**
     * @NoAdminRequired
     */
    public function conflicts(int $id): JSONResponse {
        // Placeholder for conflict detection functionality
        return new JSONResponse(['conflicts' => []]);
    }

    /**
     * @NoAdminRequired
     */
    public function resolveConflict(int $conflictId, string $resolution): JSONResponse {
        // Placeholder for conflict resolution functionality
        return new JSONResponse(['success' => true]);
    }
}
