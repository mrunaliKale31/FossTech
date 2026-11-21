<?php

declare(strict_types=1);

namespace OCA\GitIntegration\Service;

use OCA\GitIntegration\Db\Account;
use OCA\GitIntegration\Db\AccountMapper;
use OCA\GitIntegration\Db\Repository;
use OCA\GitIntegration\Db\RepositoryMapper;
use OCA\GitIntegration\Git\GitProviderFactory;
use Psr\Log\LoggerInterface;

class RepositoryService {
    private RepositoryMapper $mapper;
    private AccountMapper $accountMapper;
    private AccountService $accountService;
    private GitProviderFactory $providerFactory;
    private ActivityService $activityService;
    private LoggerInterface $logger;

    public function __construct(
        RepositoryMapper $mapper,
        AccountMapper $accountMapper,
        AccountService $accountService,
        GitProviderFactory $providerFactory,
        ActivityService $activityService,
        LoggerInterface $logger
    ) {
        $this->mapper = $mapper;
        $this->accountMapper = $accountMapper;
        $this->accountService = $accountService;
        $this->providerFactory = $providerFactory;
        $this->activityService = $activityService;
        $this->logger = $logger;
    }

    /**
     * Get all repositories for a user
     */
    public function findAll(string $userId): array {
        return $this->mapper->findAllForUser($userId);
    }

    /**
     * Get a single repository
     */
    public function find(int $id, string $userId): Repository {
        return $this->mapper->findByIdForUser($id, $userId);
    }

    /**
     * Fetch repositories from Git provider
     */
    public function fetchRepositories(int $accountId, string $userId, int $page = 1, int $perPage = 20): array {
        $account = $this->accountMapper->findByIdForUser($accountId, $userId);
        
        try {
            $accessToken = $this->accountService->getAccessToken($account);
            $provider = $this->providerFactory->create(
                $account->getProvider(),
                $account->getProviderUrl(),
                $accessToken
            );

            return $provider->getRepositories($page, $perPage);
        } catch (\Exception $e) {
            $this->logger->error('Failed to fetch repositories', [
                'account_id' => $accountId,
                'exception' => $e->getMessage()
            ]);
            throw new \RuntimeException('Failed to fetch repositories: ' . $e->getMessage());
        }
    }

    /**
     * Link a repository to a Nextcloud folder
     */
    public function linkRepository(
        string $userId,
        int $accountId,
        string $repositoryId,
        string $name,
        string $fullName,
        string $description,
        string $url,
        string $defaultBranch,
        string $visibility,
        string $nextcloudPath
    ): Repository {
        // Check if repository is already linked
        $existing = $this->mapper->findByRepositoryId($userId, $accountId, $repositoryId);
        if ($existing !== null) {
            throw new \RuntimeException('Repository is already linked');
        }

        $repository = new Repository();
        $repository->setUserId($userId);
        $repository->setAccountId($accountId);
        $repository->setRepositoryId($repositoryId);
        $repository->setName($name);
        $repository->setFullName($fullName);
        $repository->setDescription($description);
        $repository->setUrl($url);
        $repository->setDefaultBranch($defaultBranch);
        $repository->setCurrentBranch($defaultBranch);
        $repository->setVisibility($visibility);
        $repository->setNextcloudPath($nextcloudPath);
        $repository->setSyncStatus('idle');
        $repository->setCreatedAt(time());
        $repository->setUpdatedAt(time());

        $repository = $this->mapper->insert($repository);

        $this->activityService->log(
            $userId,
            $repository->getId(),
            'repository_linked',
            ['name' => $name, 'path' => $nextcloudPath],
            'success'
        );

        return $repository;
    }

    /**
     * Unlink a repository
     */
    public function unlinkRepository(int $id, string $userId): void {
        $repository = $this->mapper->findByIdForUser($id, $userId);
        
        $this->mapper->delete($repository);

        $this->activityService->log(
            $userId,
            $id,
            'repository_unlinked',
            ['name' => $repository->getName()],
            'success'
        );
    }

    /**
     * Update repository sync status
     */
    public function updateSyncStatus(int $id, string $userId, string $status, ?string $commitHash = null): Repository {
        $repository = $this->mapper->findByIdForUser($id, $userId);
        
        $repository->setSyncStatus($status);
        if ($status === 'completed') {
            $repository->setLastSyncAt(time());
            if ($commitHash) {
                $repository->setLastSyncCommit($commitHash);
            }
        }
        $repository->setUpdatedAt(time());

        return $this->mapper->update($repository);
    }

    /**
     * Switch repository branch
     */
    public function switchBranch(int $id, string $userId, string $branch): Repository {
        $repository = $this->mapper->findByIdForUser($id, $userId);
        
        $repository->setCurrentBranch($branch);
        $repository->setUpdatedAt(time());

        $repository = $this->mapper->update($repository);

        $this->activityService->log(
            $userId,
            $id,
            'branch_switched',
            ['branch' => $branch],
            'success'
        );

        return $repository;
    }

    /**
     * Get repository details from provider
     */
    public function getRepositoryDetails(int $id, string $userId): array {
        $repository = $this->mapper->findByIdForUser($id, $userId);
        $account = $this->accountMapper->findByIdForUser($repository->getAccountId(), $userId);
        
        try {
            $accessToken = $this->accountService->getAccessToken($account);
            $provider = $this->providerFactory->create(
                $account->getProvider(),
                $account->getProviderUrl(),
                $accessToken
            );

            return $provider->getRepositoryDetails($repository->getRepositoryId());
        } catch (\Exception $e) {
            $this->logger->error('Failed to get repository details', [
                'repository_id' => $id,
                'exception' => $e->getMessage()
            ]);
            throw new \RuntimeException('Failed to get repository details: ' . $e->getMessage());
        }
    }
}
