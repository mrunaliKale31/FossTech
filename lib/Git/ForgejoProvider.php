<?php

declare(strict_types=1);

namespace OCA\GitIntegration\Git;

use Psr\Log\LoggerInterface;

/**
 * Forgejo provider (placeholder for future implementation)
 */
class ForgejoProvider implements GitProviderInterface {
    private string $baseUrl;
    private string $accessToken;
    private LoggerInterface $logger;

    public function __construct(string $baseUrl, string $accessToken, LoggerInterface $logger) {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->accessToken = $accessToken;
        $this->logger = $logger;
    }

    public function testConnection(): array {
        throw new \RuntimeException('Forgejo provider not yet implemented');
    }

    public function getRepositories(int $page = 1, int $perPage = 20): array {
        throw new \RuntimeException('Forgejo provider not yet implemented');
    }

    public function getRepositoryDetails(string $repositoryId): array {
        throw new \RuntimeException('Forgejo provider not yet implemented');
    }

    public function getBranches(string $repositoryId): array {
        throw new \RuntimeException('Forgejo provider not yet implemented');
    }

    public function getCommits(string $repositoryId, string $branch, int $page = 1, int $perPage = 20): array {
        throw new \RuntimeException('Forgejo provider not yet implemented');
    }

    public function getFileContent(string $repositoryId, string $filePath, string $branch): array {
        throw new \RuntimeException('Forgejo provider not yet implemented');
    }

    public function getTree(string $repositoryId, string $branch, string $path = ''): array {
        throw new \RuntimeException('Forgejo provider not yet implemented');
    }

    public function getTags(string $repositoryId): array {
        throw new \RuntimeException('Forgejo provider not yet implemented');
    }

    public function getReleases(string $repositoryId): array {
        throw new \RuntimeException('Forgejo provider not yet implemented');
    }

    public function commitFile(string $repositoryId, string $branch, string $filePath, string $content, string $commitMessage): array {
        throw new \RuntimeException('Forgejo provider not yet implemented');
    }

    public function createBranch(string $repositoryId, string $branchName, string $sourceBranch): array {
        throw new \RuntimeException('Forgejo provider not yet implemented');
    }
}
