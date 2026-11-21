<?php

declare(strict_types=1);

namespace OCA\GitIntegration\Git;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

class GitLabProvider implements GitProviderInterface {
    private Client $client;
    private string $baseUrl;
    private string $accessToken;
    private LoggerInterface $logger;

    public function __construct(string $baseUrl, string $accessToken, LoggerInterface $logger) {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->accessToken = $accessToken;
        $this->logger = $logger;

        $this->client = new Client([
            'base_uri' => $this->baseUrl . '/api/v4/',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ],
            'timeout' => 30,
            'verify' => true,
        ]);
    }

    public function testConnection(): array {
        try {
            $response = $this->client->get('user');
            $data = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => true,
                'message' => 'Connection successful',
                'username' => $data['username'] ?? 'Unknown',
            ];
        } catch (GuzzleException $e) {
            $this->logger->error('GitLab connection test failed', ['exception' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage(),
            ];
        }
    }

    public function getRepositories(int $page = 1, int $perPage = 20): array {
        try {
            $response = $this->client->get('projects', [
                'query' => [
                    'membership' => true,
                    'page' => $page,
                    'per_page' => $perPage,
                    'order_by' => 'updated_at',
                ],
            ]);

            $projects = json_decode($response->getBody()->getContents(), true);

            return array_map(function ($project) {
                return [
                    'id' => (string) $project['id'],
                    'name' => $project['name'],
                    'full_name' => $project['path_with_namespace'],
                    'description' => $project['description'] ?? '',
                    'url' => $project['web_url'],
                    'default_branch' => $project['default_branch'] ?? 'main',
                    'visibility' => $project['visibility'] ?? 'private',
                    'last_activity_at' => $project['last_activity_at'] ?? null,
                ];
            }, $projects);
        } catch (GuzzleException $e) {
            $this->logger->error('Failed to fetch GitLab repositories', ['exception' => $e->getMessage()]);
            throw new \RuntimeException('Failed to fetch repositories: ' . $e->getMessage());
        }
    }

    public function getRepositoryDetails(string $repositoryId): array {
        try {
            $response = $this->client->get('projects/' . urlencode($repositoryId));
            $project = json_decode($response->getBody()->getContents(), true);

            return [
                'id' => (string) $project['id'],
                'name' => $project['name'],
                'full_name' => $project['path_with_namespace'],
                'description' => $project['description'] ?? '',
                'url' => $project['web_url'],
                'default_branch' => $project['default_branch'] ?? 'main',
                'visibility' => $project['visibility'] ?? 'private',
                'last_activity_at' => $project['last_activity_at'] ?? null,
            ];
        } catch (GuzzleException $e) {
            $this->logger->error('Failed to fetch GitLab repository details', ['exception' => $e->getMessage()]);
            throw new \RuntimeException('Failed to fetch repository details: ' . $e->getMessage());
        }
    }

    public function getBranches(string $repositoryId): array {
        try {
            $response = $this->client->get('projects/' . urlencode($repositoryId) . '/repository/branches');
            $branches = json_decode($response->getBody()->getContents(), true);

            return array_map(function ($branch) {
                return [
                    'name' => $branch['name'],
                    'commit' => [
                        'id' => $branch['commit']['id'] ?? null,
                        'message' => $branch['commit']['message'] ?? null,
                        'created_at' => $branch['commit']['created_at'] ?? null,
                    ],
                    'protected' => $branch['protected'] ?? false,
                ];
            }, $branches);
        } catch (GuzzleException $e) {
            $this->logger->error('Failed to fetch GitLab branches', ['exception' => $e->getMessage()]);
            throw new \RuntimeException('Failed to fetch branches: ' . $e->getMessage());
        }
    }

    public function getCommits(string $repositoryId, string $branch, int $page = 1, int $perPage = 20): array {
        try {
            $response = $this->client->get('projects/' . urlencode($repositoryId) . '/repository/commits', [
                'query' => [
                    'ref_name' => $branch,
                    'page' => $page,
                    'per_page' => $perPage,
                ],
            ]);

            $commits = json_decode($response->getBody()->getContents(), true);

            return array_map(function ($commit) {
                return [
                    'id' => $commit['id'],
                    'short_id' => $commit['short_id'],
                    'title' => $commit['title'],
                    'message' => $commit['message'],
                    'author_name' => $commit['author_name'],
                    'author_email' => $commit['author_email'],
                    'created_at' => $commit['created_at'],
                    'web_url' => $commit['web_url'] ?? null,
                ];
            }, $commits);
        } catch (GuzzleException $e) {
            $this->logger->error('Failed to fetch GitLab commits', ['exception' => $e->getMessage()]);
            throw new \RuntimeException('Failed to fetch commits: ' . $e->getMessage());
        }
    }

    public function getFileContent(string $repositoryId, string $filePath, string $branch): array {
        try {
            $response = $this->client->get('projects/' . urlencode($repositoryId) . '/repository/files/' . urlencode($filePath), [
                'query' => ['ref' => $branch],
            ]);

            $file = json_decode($response->getBody()->getContents(), true);

            $content = base64_decode($file['content']);

            return [
                'content' => $content,
                'encoding' => $file['encoding'] ?? 'base64',
                'size' => $file['size'] ?? strlen($content),
            ];
        } catch (GuzzleException $e) {
            $this->logger->error('Failed to fetch GitLab file content', [
                'file' => $filePath,
                'exception' => $e->getMessage()
            ]);
            throw new \RuntimeException('Failed to fetch file content: ' . $e->getMessage());
        }
    }

    public function getTree(string $repositoryId, string $branch, string $path = ''): array {
        try {
            $response = $this->client->get('projects/' . urlencode($repositoryId) . '/repository/tree', [
                'query' => [
                    'ref' => $branch,
                    'path' => $path,
                    'recursive' => false,
                ],
            ]);

            $tree = json_decode($response->getBody()->getContents(), true);

            return array_map(function ($item) {
                return [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'type' => $item['type'],
                    'path' => $item['path'],
                    'mode' => $item['mode'] ?? null,
                ];
            }, $tree);
        } catch (GuzzleException $e) {
            $this->logger->error('Failed to fetch GitLab tree', ['exception' => $e->getMessage()]);
            throw new \RuntimeException('Failed to fetch repository tree: ' . $e->getMessage());
        }
    }

    public function getTags(string $repositoryId): array {
        try {
            $response = $this->client->get('projects/' . urlencode($repositoryId) . '/repository/tags');
            $tags = json_decode($response->getBody()->getContents(), true);

            return array_map(function ($tag) {
                return [
                    'name' => $tag['name'],
                    'message' => $tag['message'] ?? '',
                    'commit' => [
                        'id' => $tag['commit']['id'] ?? null,
                        'message' => $tag['commit']['message'] ?? null,
                        'created_at' => $tag['commit']['created_at'] ?? null,
                    ],
                    'release' => $tag['release'] ?? null,
                ];
            }, $tags);
        } catch (GuzzleException $e) {
            $this->logger->error('Failed to fetch GitLab tags', ['exception' => $e->getMessage()]);
            throw new \RuntimeException('Failed to fetch tags: ' . $e->getMessage());
        }
    }

    public function getReleases(string $repositoryId): array {
        try {
            $response = $this->client->get('projects/' . urlencode($repositoryId) . '/releases');
            $releases = json_decode($response->getBody()->getContents(), true);

            return array_map(function ($release) {
                return [
                    'tag_name' => $release['tag_name'],
                    'name' => $release['name'] ?? $release['tag_name'],
                    'description' => $release['description'] ?? '',
                    'created_at' => $release['created_at'] ?? null,
                    'released_at' => $release['released_at'] ?? null,
                    'commit' => $release['commit'] ?? null,
                ];
            }, $releases);
        } catch (GuzzleException $e) {
            $this->logger->error('Failed to fetch GitLab releases', ['exception' => $e->getMessage()]);
            throw new \RuntimeException('Failed to fetch releases: ' . $e->getMessage());
        }
    }

    public function commitFile(string $repositoryId, string $branch, string $filePath, string $content, string $commitMessage): array {
        try {
            $response = $this->client->put('projects/' . urlencode($repositoryId) . '/repository/files/' . urlencode($filePath), [
                'json' => [
                    'branch' => $branch,
                    'content' => $content,
                    'commit_message' => $commitMessage,
                    'encoding' => 'text',
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            return [
                'commit_id' => $result['file_path'] ?? '',
                'url' => $this->baseUrl . '/' . $repositoryId . '/-/commit/' . ($result['file_path'] ?? ''),
            ];
        } catch (GuzzleException $e) {
            $this->logger->error('Failed to commit file to GitLab', [
                'file' => $filePath,
                'exception' => $e->getMessage()
            ]);
            throw new \RuntimeException('Failed to commit file: ' . $e->getMessage());
        }
    }

    public function createBranch(string $repositoryId, string $branchName, string $sourceBranch): array {
        try {
            $response = $this->client->post('projects/' . urlencode($repositoryId) . '/repository/branches', [
                'query' => [
                    'branch' => $branchName,
                    'ref' => $sourceBranch,
                ],
            ]);

            $branch = json_decode($response->getBody()->getContents(), true);

            return [
                'name' => $branch['name'],
                'commit' => $branch['commit'] ?? null,
            ];
        } catch (GuzzleException $e) {
            $this->logger->error('Failed to create GitLab branch', ['exception' => $e->getMessage()]);
            throw new \RuntimeException('Failed to create branch: ' . $e->getMessage());
        }
    }
}
