<?php

declare(strict_types=1);

namespace OCA\GitIntegration\Service;

use OCA\GitIntegration\Db\Repository;
use OCA\GitIntegration\Db\RepositoryMapper;
use OCA\GitIntegration\Db\AccountMapper;
use OCA\GitIntegration\Db\SyncRecord;
use OCA\GitIntegration\Db\SyncRecordMapper;
use OCA\GitIntegration\Git\GitProviderFactory;
use OCP\Files\IRootFolder;
use OCP\Files\NotFoundException;
use Psr\Log\LoggerInterface;

class SyncService {
    private RepositoryMapper $repositoryMapper;
    private AccountMapper $accountMapper;
    private SyncRecordMapper $syncRecordMapper;
    private AccountService $accountService;
    private GitProviderFactory $providerFactory;
    private ActivityService $activityService;
    private IRootFolder $rootFolder;
    private LoggerInterface $logger;

    public function __construct(
        RepositoryMapper $repositoryMapper,
        AccountMapper $accountMapper,
        SyncRecordMapper $syncRecordMapper,
        AccountService $accountService,
        GitProviderFactory $providerFactory,
        ActivityService $activityService,
        IRootFolder $rootFolder,
        LoggerInterface $logger
    ) {
        $this->repositoryMapper = $repositoryMapper;
        $this->accountMapper = $accountMapper;
        $this->syncRecordMapper = $syncRecordMapper;
        $this->accountService = $accountService;
        $this->providerFactory = $providerFactory;
        $this->activityService = $activityService;
        $this->rootFolder = $rootFolder;
        $this->logger = $logger;
    }

    /**
     * Synchronize repository with Nextcloud
     */
    public function syncRepository(int $repositoryId, string $userId): array {
        $repository = $this->repositoryMapper->findByIdForUser($repositoryId, $userId);
        
        // Create sync record
        $syncRecord = new SyncRecord();
        $syncRecord->setRepositoryId($repositoryId);
        $syncRecord->setStatus('in_progress');
        $syncRecord->setDirection('git_to_nextcloud');
        $syncRecord->setFilesAdded(0);
        $syncRecord->setFilesModified(0);
        $syncRecord->setFilesDeleted(0);
        $syncRecord->setConflicts(0);
        $syncRecord->setStartedAt(time());
        $syncRecord = $this->syncRecordMapper->insert($syncRecord);

        try {
            // Update repository status
            $repository->setSyncStatus('syncing');
            $this->repositoryMapper->update($repository);

            // Get Git provider
            $account = $this->accountMapper->findByIdForUser($repository->getAccountId(), $userId);
            $accessToken = $this->accountService->getAccessToken($account);
            $provider = $this->providerFactory->create(
                $account->getProvider(),
                $account->getProviderUrl(),
                $accessToken
            );

            // Get repository tree
            $tree = $provider->getTree(
                $repository->getRepositoryId(),
                $repository->getCurrentBranch()
            );

            // Get user folder
            $userFolder = $this->rootFolder->getUserFolder($userId);
            $targetPath = $repository->getNextcloudPath();
            
            // Ensure target folder exists
            try {
                $targetFolder = $userFolder->get($targetPath);
            } catch (NotFoundException $e) {
                $targetFolder = $userFolder->newFolder($targetPath);
            }

            // Sync files recursively
            $stats = $this->syncTree($provider, $repository, $targetFolder, $tree, '');

            // Get latest commit for tracking
            $commits = $provider->getCommits($repository->getRepositoryId(), $repository->getCurrentBranch(), 1, 1);
            $latestCommit = $commits[0]['id'] ?? null;

            // Update sync record
            $syncRecord->setStatus('completed');
            $syncRecord->setFilesAdded($stats['added']);
            $syncRecord->setFilesModified($stats['modified']);
            $syncRecord->setFilesDeleted($stats['deleted']);
            $syncRecord->setConflicts($stats['conflicts']);
            $syncRecord->setCommitHash($latestCommit);
            $syncRecord->setCompletedAt(time());
            $this->syncRecordMapper->update($syncRecord);

            // Update repository
            $repository->setSyncStatus('completed');
            $repository->setLastSyncAt(time());
            $repository->setLastSyncCommit($latestCommit);
            $this->repositoryMapper->update($repository);

            $this->activityService->log(
                $userId,
                $repositoryId,
                'sync_completed',
                $stats,
                'success'
            );

            return [
                'success' => true,
                'stats' => $stats,
                'syncRecord' => $syncRecord,
            ];
        } catch (\Exception $e) {
            $this->logger->error('Sync failed', [
                'repository_id' => $repositoryId,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Update sync record
            $syncRecord->setStatus('failed');
            $syncRecord->setErrorMessage($e->getMessage());
            $syncRecord->setCompletedAt(time());
            $this->syncRecordMapper->update($syncRecord);

            // Update repository
            $repository->setSyncStatus('error');
            $this->repositoryMapper->update($repository);

            $this->activityService->log(
                $userId,
                $repositoryId,
                'sync_failed',
                ['error' => $e->getMessage()],
                'error',
                $e->getMessage()
            );

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Recursively sync files from Git tree
     */
    private function syncTree($provider, Repository $repository, $folder, array $tree, string $basePath): array {
        $stats = ['added' => 0, 'modified' => 0, 'deleted' => 0, 'conflicts' => 0];

        foreach ($tree as $item) {
            try {
                if ($item['type'] === 'tree') {
                    // Handle directory
                    $dirName = $item['name'];
                    try {
                        $subFolder = $folder->get($dirName);
                    } catch (NotFoundException $e) {
                        $subFolder = $folder->newFolder($dirName);
                        $stats['added']++;
                    }

                    // Recursively sync subdirectory
                    $subPath = $basePath ? $basePath . '/' . $dirName : $dirName;
                    $subTree = $provider->getTree(
                        $repository->getRepositoryId(),
                        $repository->getCurrentBranch(),
                        $subPath
                    );
                    $subStats = $this->syncTree($provider, $repository, $subFolder, $subTree, $subPath);
                    
                    $stats['added'] += $subStats['added'];
                    $stats['modified'] += $subStats['modified'];
                    $stats['deleted'] += $subStats['deleted'];
                    $stats['conflicts'] += $subStats['conflicts'];
                } elseif ($item['type'] === 'blob') {
                    // Handle file
                    $fileName = $item['name'];
                    $filePath = $basePath ? $basePath . '/' . $fileName : $fileName;

                    // Get file content from Git
                    $fileData = $provider->getFileContent(
                        $repository->getRepositoryId(),
                        $filePath,
                        $repository->getCurrentBranch()
                    );

                    try {
                        $file = $folder->get($fileName);
                        // File exists - update it
                        $file->putContent($fileData['content']);
                        $stats['modified']++;
                    } catch (NotFoundException $e) {
                        // File doesn't exist - create it
                        $file = $folder->newFile($fileName);
                        $file->putContent($fileData['content']);
                        $stats['added']++;
                    }
                }
            } catch (\Exception $e) {
                $this->logger->warning('Failed to sync item', [
                    'item' => $item['name'],
                    'type' => $item['type'],
                    'exception' => $e->getMessage()
                ]);
                // Continue with next item
            }
        }

        return $stats;
    }

    /**
     * Get sync history for a repository
     */
    public function getSyncHistory(int $repositoryId, int $limit = 50): array {
        return $this->syncRecordMapper->findByRepository($repositoryId, $limit);
    }

    /**
     * Get sync status for a repository
     */
    public function getSyncStatus(int $repositoryId, string $userId): array {
        $repository = $this->repositoryMapper->findByIdForUser($repositoryId, $userId);
        $lastSync = $this->syncRecordMapper->findLastSuccessful($repositoryId);

        return [
            'status' => $repository->getSyncStatus(),
            'lastSyncAt' => $repository->getLastSyncAt(),
            'lastSyncCommit' => $repository->getLastSyncCommit(),
            'lastSyncRecord' => $lastSync,
        ];
    }
}
