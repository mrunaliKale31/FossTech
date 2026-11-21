<?php

declare(strict_types=1);

namespace OCA\GitIntegration\Git;

interface GitProviderInterface {
    /**
     * Test connection to Git provider
     * 
     * @return array{success: bool, message: string, username?: string}
     */
    public function testConnection(): array;

    /**
     * Get list of repositories accessible to the user
     * 
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getRepositories(int $page = 1, int $perPage = 20): array;

    /**
     * Get repository details
     * 
     * @param string $repositoryId
     * @return array
     */
    public function getRepositoryDetails(string $repositoryId): array;

    /**
     * Get repository branches
     * 
     * @param string $repositoryId
     * @return array
     */
    public function getBranches(string $repositoryId): array;

    /**
     * Get repository commits
     * 
     * @param string $repositoryId
     * @param string $branch
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getCommits(string $repositoryId, string $branch, int $page = 1, int $perPage = 20): array;

    /**
     * Get file content from repository
     * 
     * @param string $repositoryId
     * @param string $filePath
     * @param string $branch
     * @return array{content: string, encoding: string, size: int}
     */
    public function getFileContent(string $repositoryId, string $filePath, string $branch): array;

    /**
     * Get repository tree (file structure)
     * 
     * @param string $repositoryId
     * @param string $branch
     * @param string $path
     * @return array
     */
    public function getTree(string $repositoryId, string $branch, string $path = ''): array;

    /**
     * Get repository tags
     * 
     * @param string $repositoryId
     * @return array
     */
    public function getTags(string $repositoryId): array;

    /**
     * Get repository releases
     * 
     * @param string $repositoryId
     * @return array
     */
    public function getReleases(string $repositoryId): array;

    /**
     * Commit a file to repository
     * 
     * @param string $repositoryId
     * @param string $branch
     * @param string $filePath
     * @param string $content
     * @param string $commitMessage
     * @return array{commit_id: string, url: string}
     */
    public function commitFile(string $repositoryId, string $branch, string $filePath, string $content, string $commitMessage): array;

    /**
     * Create a new branch
     * 
     * @param string $repositoryId
     * @param string $branchName
     * @param string $sourceBranch
     * @return array
     */
    public function createBranch(string $repositoryId, string $branchName, string $sourceBranch): array;
}
