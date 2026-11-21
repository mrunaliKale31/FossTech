<?php

declare(strict_types=1);

namespace OCA\GitIntegration\Controller;

use OCA\GitIntegration\AppInfo\Application;
use OCA\GitIntegration\Service\RepositoryService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

class RepositoryController extends Controller {
    private RepositoryService $service;
    private ?string $userId;

    public function __construct(IRequest $request, RepositoryService $service, ?string $userId) {
        parent::__construct(Application::APP_ID, $request);
        $this->service = $service;
        $this->userId = $userId;
    }

    /**
     * @NoAdminRequired
     */
    public function list(): JSONResponse {
        $repositories = $this->service->findAll($this->userId);
        return new JSONResponse($repositories);
    }

    /**
     * @NoAdminRequired
     */
    public function get(int $id): JSONResponse {
        try {
            $repository = $this->service->find($id, $this->userId);
            return new JSONResponse($repository);
        } catch (\Exception $e) {
            return new JSONResponse(['error' => $e->getMessage()], Http::STATUS_NOT_FOUND);
        }
    }

    /**
     * @NoAdminRequired
     */
    public function fetch(int $accountId, int $page = 1, int $perPage = 20): JSONResponse {
        try {
            $repositories = $this->service->fetchRepositories($accountId, $this->userId, $page, $perPage);
            return new JSONResponse($repositories);
        } catch (\Exception $e) {
            return new JSONResponse(['error' => $e->getMessage()], Http::STATUS_BAD_REQUEST);
        }
    }

    /**
     * @NoAdminRequired
     */
    public function link(
        int $accountId,
        string $repositoryId,
        string $name,
        string $fullName,
        string $description,
        string $url,
        string $defaultBranch,
        string $visibility,
        string $nextcloudPath
    ): JSONResponse {
        try {
            $repository = $this->service->linkRepository(
                $this->userId,
                $accountId,
                $repositoryId,
                $name,
                $fullName,
                $description,
                $url,
                $defaultBranch,
                $visibility,
                $nextcloudPath
            );
            return new JSONResponse($repository, Http::STATUS_CREATED);
        } catch (\Exception $e) {
            return new JSONResponse(['error' => $e->getMessage()], Http::STATUS_BAD_REQUEST);
        }
    }

    /**
     * @NoAdminRequired
     */
    public function unlink(int $id): JSONResponse {
        try {
            $this->service->unlinkRepository($id, $this->userId);
            return new JSONResponse(['success' => true]);
        } catch (\Exception $e) {
            return new JSONResponse(['error' => $e->getMessage()], Http::STATUS_BAD_REQUEST);
        }
    }

    /**
     * @NoAdminRequired
     */
    public function details(int $id): JSONResponse {
        try {
            $details = $this->service->getRepositoryDetails($id, $this->userId);
            return new JSONResponse($details);
        } catch (\Exception $e) {
            return new JSONResponse(['error' => $e->getMessage()], Http::STATUS_BAD_REQUEST);
        }
    }
}
