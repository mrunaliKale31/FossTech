<?php

declare(strict_types=1);

namespace OCA\GitIntegration\Db;

use OCP\AppFramework\Db\Entity;

/**
 * @method string getUserId()
 * @method void setUserId(string $userId)
 * @method int getRepositoryId()
 * @method void setRepositoryId(int $repositoryId)
 * @method string getEventType()
 * @method void setEventType(string $eventType)
 * @method string getEventData()
 * @method void setEventData(string $eventData)
 * @method string getStatus()
 * @method void setStatus(string $status)
 * @method string getErrorMessage()
 * @method void setErrorMessage(string $errorMessage)
 * @method int getCreatedAt()
 * @method void setCreatedAt(int $createdAt)
 */
class Activity extends Entity {
    protected $userId;
    protected $repositoryId;
    protected $eventType;
    protected $eventData;
    protected $status;
    protected $errorMessage;
    protected $createdAt;

    public function __construct() {
        $this->addType('userId', 'string');
        $this->addType('repositoryId', 'integer');
        $this->addType('eventType', 'string');
        $this->addType('eventData', 'string');
        $this->addType('status', 'string');
        $this->addType('errorMessage', 'string');
        $this->addType('createdAt', 'integer');
    }

    public function jsonSerialize(): array {
        $eventData = $this->eventData ? json_decode($this->eventData, true) : [];
        
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'repositoryId' => $this->repositoryId,
            'eventType' => $this->eventType,
            'eventData' => $eventData,
            'status' => $this->status,
            'errorMessage' => $this->errorMessage,
            'createdAt' => $this->createdAt,
        ];
    }
}
