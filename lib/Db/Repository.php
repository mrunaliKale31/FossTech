<?php

declare(strict_types=1);

namespace OCA\GitIntegration\Db;

use OCP\AppFramework\Db\Entity;

/**
 * @method string getUserId()
 * @method void setUserId(string $userId)
 * @method int getAccountId()
 * @method void setAccountId(int $accountId)
 * @method string getRepositoryId()
 * @method void setRepositoryId(string $repositoryId)
 * @method string getName()
 * @method void setName(string $name)
 * @method string getFullName()
 * @method void setFullName(string $fullName)
 * @method string getDescription()
 * @method void setDescription(string $description)
 * @method string getUrl()
 * @method void setUrl(string $url)
 * @method string getDefaultBranch()
 * @method void setDefaultBranch(string $defaultBranch)
 * @method string getCurrentBranch()
 * @method void setCurrentBranch(string $currentBranch)
 * @method string getVisibility()
 * @method void setVisibility(string $visibility)
 * @method string getNextcloudPath()
 * @method void setNextcloudPath(string $nextcloudPath)
 * @method string getSyncStatus()
 * @method void setSyncStatus(string $syncStatus)
 * @method int getLastSyncAt()
 * @method void setLastSyncAt(int $lastSyncAt)
 * @method string getLastSyncCommit()
 * @method void setLastSyncCommit(string $lastSyncCommit)
 * @method int getCreatedAt()
 * @method void setCreatedAt(int $createdAt)
 * @method int getUpdatedAt()
 * @method void setUpdatedAt(int $updatedAt)
 */
class Repository extends Entity {
    protected $userId;
    protected $accountId;
    protected $repositoryId;
    protected $name;
    protected $fullName;
    protected $description;
    protected $url;
    protected $defaultBranch;
    protected $currentBranch;
    protected $visibility;
    protected $nextcloudPath;
    protected $syncStatus;
    protected $lastSyncAt;
    protected $lastSyncCommit;
    protected $createdAt;
    protected $updatedAt;

    public function __construct() {
        $this->addType('userId', 'string');
        $this->addType('accountId', 'integer');
        $this->addType('repositoryId', 'string');
        $this->addType('name', 'string');
        $this->addType('fullName', 'string');
        $this->addType('description', 'string');
        $this->addType('url', 'string');
        $this->addType('defaultBranch', 'string');
        $this->addType('currentBranch', 'string');
        $this->addType('visibility', 'string');
        $this->addType('nextcloudPath', 'string');
        $this->addType('syncStatus', 'string');
        $this->addType('lastSyncAt', 'integer');
        $this->addType('lastSyncCommit', 'string');
        $this->addType('createdAt', 'integer');
        $this->addType('updatedAt', 'integer');
    }

    public function jsonSerialize(): array {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'accountId' => $this->accountId,
            'repositoryId' => $this->repositoryId,
            'name' => $this->name,
            'fullName' => $this->fullName,
            'description' => $this->description,
            'url' => $this->url,
            'defaultBranch' => $this->defaultBranch,
            'currentBranch' => $this->currentBranch,
            'visibility' => $this->visibility,
            'nextcloudPath' => $this->nextcloudPath,
            'syncStatus' => $this->syncStatus,
            'lastSyncAt' => $this->lastSyncAt,
            'lastSyncCommit' => $this->lastSyncCommit,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }
}
