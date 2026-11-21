<?php

declare(strict_types=1);

namespace OCA\GitIntegration\Controller;

use OCA\GitIntegration\AppInfo\Application;
use OCA\GitIntegration\Service\ActivityService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

class ActivityController extends Controller {
    private ActivityService $service;
    private ?string $userId;

    public function __construct(IRequest $request, ActivityService $service, ?string $userId) {
        parent::__construct(Application::APP_ID, $request);
        $this->service = $service;
        $this->userId = $userId;
    }

    /**
     * @NoAdminRequired
     */
    public function list(int $limit = 100): JSONResponse {
        try {
            $activities = $this->service->getForUser($this->userId, $limit);
            return new JSONResponse($activities);
        } catch (\Exception $e) {
            return new JSONResponse(['error' => $e->getMessage()], Http::STATUS_BAD_REQUEST);
        }
    }

    /**
     * @NoAdminRequired
     */
    public function repository(int $id, int $limit = 100): JSONResponse {
        try {
            $activities = $this->service->getForRepository($id, $limit);
            return new JSONResponse($activities);
        } catch (\Exception $e) {
            return new JSONResponse(['error' => $e->getMessage()], Http::STATUS_BAD_REQUEST);
        }
    }
}
