<?php

declare(strict_types=1);

namespace OCA\GitIntegration\Service;

use OCA\GitIntegration\Db\Account;
use OCA\GitIntegration\Db\AccountMapper;
use OCA\GitIntegration\Git\GitProviderFactory;
use OCP\IConfig;
use OCP\Security\ICrypto;
use Psr\Log\LoggerInterface;

class AccountService {
    private AccountMapper $mapper;
    private ICrypto $crypto;
    private IConfig $config;
    private GitProviderFactory $providerFactory;
    private ActivityService $activityService;
    private LoggerInterface $logger;

    public function __construct(
        AccountMapper $mapper,
        ICrypto $crypto,
        IConfig $config,
        GitProviderFactory $providerFactory,
        ActivityService $activityService,
        LoggerInterface $logger
    ) {
        $this->mapper = $mapper;
        $this->crypto = $crypto;
        $this->config = $config;
        $this->providerFactory = $providerFactory;
        $this->activityService = $activityService;
        $this->logger = $logger;
    }

    /**
     * Get all accounts for a user
     */
    public function findAll(string $userId): array {
        return $this->mapper->findAllForUser($userId);
    }

    /**
     * Get a single account
     */
    public function find(int $id, string $userId): Account {
        return $this->mapper->findByIdForUser($id, $userId);
    }

    /**
     * Create a new account
     */
    public function create(string $userId, string $provider, string $providerUrl, string $username, string $accessToken): Account {
        // Check if account already exists
        $existing = $this->mapper->findByProviderForUser($userId, $provider, $providerUrl);
        if ($existing !== null) {
            throw new \RuntimeException('Account already exists for this provider');
        }

        $account = new Account();
        $account->setUserId($userId);
        $account->setProvider($provider);
        $account->setProviderUrl($providerUrl);
        $account->setUsername($username);
        // Encrypt the access token
        $account->setAccessToken($this->crypto->encrypt($accessToken));
        $account->setStatus('active');
        $account->setCreatedAt(time());
        $account->setUpdatedAt(time());

        $account = $this->mapper->insert($account);

        $this->activityService->log(
            $userId,
            null,
            'account_connected',
            ['provider' => $provider, 'username' => $username],
            'success'
        );

        return $account;
    }

    /**
     * Update an account
     */
    public function update(int $id, string $userId, string $username, string $accessToken): Account {
        $account = $this->mapper->findByIdForUser($id, $userId);
        
        $account->setUsername($username);
        // Encrypt the new access token
        $account->setAccessToken($this->crypto->encrypt($accessToken));
        $account->setUpdatedAt(time());

        $account = $this->mapper->update($account);

        $this->activityService->log(
            $userId,
            null,
            'account_updated',
            ['provider' => $account->getProvider()],
            'success'
        );

        return $account;
    }

    /**
     * Delete an account
     */
    public function delete(int $id, string $userId): void {
        $account = $this->mapper->findByIdForUser($id, $userId);
        
        $this->mapper->delete($account);

        $this->activityService->log(
            $userId,
            null,
            'account_disconnected',
            ['provider' => $account->getProvider()],
            'success'
        );
    }

    /**
     * Test connection for an account
     */
    public function testConnection(int $id, string $userId): array {
        $account = $this->mapper->findByIdForUser($id, $userId);
        
        try {
            // Decrypt the access token
            $accessToken = $this->crypto->decrypt($account->getAccessToken());
            
            $provider = $this->providerFactory->create(
                $account->getProvider(),
                $account->getProviderUrl(),
                $accessToken
            );

            $result = $provider->testConnection();

            if ($result['success']) {
                $account->setStatus('active');
                $account->setLastChecked(time());
                $this->mapper->update($account);
            } else {
                $account->setStatus('error');
                $this->mapper->update($account);
            }

            return $result;
        } catch (\Exception $e) {
            $this->logger->error('Connection test failed', [
                'account_id' => $id,
                'exception' => $e->getMessage()
            ]);

            $account->setStatus('error');
            $this->mapper->update($account);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get decrypted access token for an account
     */
    public function getAccessToken(Account $account): string {
        return $this->crypto->decrypt($account->getAccessToken());
    }
}
