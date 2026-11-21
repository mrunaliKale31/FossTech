<?php

declare(strict_types=1);

namespace OCA\GitIntegration\Db;

use OCP\AppFramework\Db\Entity;

/**
 * @method int getRepositoryId()
 * @method void setRepositoryId(int $repositoryId)
 * @method string getStatus()
 * @method void setStatus(string $status)
 * @method string getDirection()
 * @method void setDirection(string $direction)
 * @method int getFilesAdded()
 * @method void setFilesAdded(int $filesAdded)
 * @method int getFilesModified()
 * @method void setFilesModified(int $filesModified)
 * @method int getFilesDeleted()
 * @method void setFilesDeleted(int $filesDeleted)
 * @method int getConflicts()
 * @method void setConflicts(int $conflicts)
 * @method string getCommitHash()
 * @method void setCommitHash(string $commitHash)
 * @method string getErrorMessage()
 * @method void setErrorMessage(string $errorMessage)
 * @method int getStartedAt()
 * @method void setStartedAt(int $startedAt)
 * @method int getCompletedAt()
 * @method void setCompletedAt(int $completedAt)
 */
class SyncRecord extends Entity {
    protected $repositoryId;
    protected $status;
    protected $direction;
    protected $filesAdded;
    protected $filesModified;
    protected $filesDeleted;
    protected $conflicts;
    protected $commitHash;
    protected $errorMessage;
    protected $startedAt;
    protected $completedAt;

    public function __construct() {
        $this->addType('repositoryId', 'integer');
        $this->addType('status', 'string');
        $this->addType('direction', 'string');
        $this->addType('filesAdded', 'integer');
        $this->addType('filesModified', 'integer');
        $this->addType('filesDeleted', 'integer');
        $this->addType('conflicts', 'integer');
        $this->addType('commitHash', 'string');
        $this->addType('errorMessage', 'string');
        $this->addType('startedAt', 'integer');
        $this->addType('completedAt', 'integer');
    }

    public function jsonSerialize(): array {
        return [
            'id' => $this->id,
            'repositoryId' => $this->repositoryId,
            'status' => $this->status,
            'direction' => $this->direction,
            'filesAdded' => $this->filesAdded,
            'filesModified' => $this->filesModified,
            'filesDeleted' => $this->filesDeleted,
            'conflicts' => $this->conflicts,
            'commitHash' => $this->commitHash,
            'errorMessage' => $this->errorMessage,
            'startedAt' => $this->startedAt,
            'completedAt' => $this->completedAt,
        ];
    }
}
