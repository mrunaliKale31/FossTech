<?php

declare(strict_types=1);

namespace OCA\GitIntegration\Db;

use OCP\AppFramework\Db\Entity;

/**
 * @method string getUserId()
 * @method void setUserId(string $userId)
 * @method string getProvider()
 * @method void setProvider(string $provider)
 * @method string getProviderUrl()
 * @method void setProviderUrl(string $providerUrl)
 * @method string getUsername()
 * @method void setUsername(string $username)
 * @method string getAccessToken()
 * @method void setAccessToken(string $accessToken)
 * @method string getStatus()
 * @method void setStatus(string $status)
 * @method int getLastChecked()
 * @method void setLastChecked(int $lastChecked)
 * @method int getCreatedAt()
 * @method void setCreatedAt(int $createdAt)
 * @method int getUpdatedAt()
 * @method void setUpdatedAt(int $updatedAt)
 */
class Account extends Entity {
    protected $userId;
    protected $provider;
    protected $providerUrl;
    protected $username;
    protected $accessToken;
    protected $status;
    protected $lastChecked;
    protected $createdAt;
    protected $updatedAt;

    public function __construct() {
        $this->addType('userId', 'string');
        $this->addType('provider', 'string');
        $this->addType('providerUrl', 'string');
        $this->addType('username', 'string');
        $this->addType('accessToken', 'string');
        $this->addType('status', 'string');
        $this->addType('lastChecked', 'integer');
        $this->addType('createdAt', 'integer');
        $this->addType('updatedAt', 'integer');
    }

    public function jsonSerialize(): array {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'provider' => $this->provider,
            'providerUrl' => $this->providerUrl,
            'username' => $this->username,
            // Never expose the access token
            'status' => $this->status,
            'lastChecked' => $this->lastChecked,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }
}
