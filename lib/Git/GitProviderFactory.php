<?php

declare(strict_types=1);

namespace OCA\GitIntegration\Git;

use OCA\GitIntegration\Db\Account;
use Psr\Log\LoggerInterface;

class GitProviderFactory {
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    /**
     * Create a Git provider instance from an account
     * 
     * @param Account $account
     * @return GitProviderInterface
     * @throws \InvalidArgumentException
     */
    public function createFromAccount(Account $account): GitProviderInterface {
        return $this->create(
            $account->getProvider(),
            $account->getProviderUrl(),
            $account->getAccessToken()
        );
    }

    /**
     * Create a Git provider instance
     * 
     * @param string $provider
     * @param string $providerUrl
     * @param string $accessToken
     * @return GitProviderInterface
     * @throws \InvalidArgumentException
     */
    public function create(string $provider, string $providerUrl, string $accessToken): GitProviderInterface {
        switch ($provider) {
            case 'gitlab':
                return new GitLabProvider($providerUrl, $accessToken, $this->logger);
            
            case 'gitea':
                return new GiteaProvider($providerUrl, $accessToken, $this->logger);
            
            case 'forgejo':
                return new ForgejoProvider($providerUrl, $accessToken, $this->logger);
            
            default:
                throw new \InvalidArgumentException('Unsupported provider: ' . $provider);
        }
    }

    /**
     * Get list of supported providers
     * 
     * @return array
     */
    public function getSupportedProviders(): array {
        return [
            'gitlab' => [
                'name' => 'GitLab',
                'implemented' => true,
                'default_url' => 'https://gitlab.com',
            ],
            'gitea' => [
                'name' => 'Gitea',
                'implemented' => false,
                'default_url' => '',
            ],
            'forgejo' => [
                'name' => 'Forgejo',
                'implemented' => false,
                'default_url' => '',
            ],
        ];
    }
}
