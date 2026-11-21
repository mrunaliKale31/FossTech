<?php

declare(strict_types=1);

namespace OCA\GitIntegration\Db;

use OCP\AppFramework\Db\Entity;

/**
 * @method int getRepositoryId()
 * @method void setRepositoryId(int $repositoryId)
 * @method string getUserId()
 * @method void setUserId(string $userId)
 * @method string getFilePath()
 * @method void setFilePath(string $filePath)
 * @method string getOriginalContent()
 * @method void setOriginalContent(string $originalContent)
 * @method string getProposedContent()
 * @method void setProposedContent(string $proposedContent)
 * @method string getDescription()
 * @method void setDescription(string $description)
 * @method string getStatus()
 * @method void setStatus(string $status)
 * @method string getReviewerId()
 * @method void setReviewerId(string $reviewerId)
 * @method string getReviewComment()
 * @method void setReviewComment(string $reviewComment)
 * @method int getReviewedAt()
 * @method void setReviewedAt(int $reviewedAt)
 * @method string getCommitMessage()
 * @method void setCommitMessage(string $commitMessage)
 * @method string getCommitSha()
 * @method void setCommitSha(string $commitSha)
 * @method int getCommittedAt()
 * @method void setCommittedAt(int $committedAt)
 * @method int getCreatedAt()
 * @method void setCreatedAt(int $createdAt)
 * @method int getUpdatedAt()
 * @method void setUpdatedAt(int $updatedAt)
 */
class Suggestion extends Entity {
    protected $repositoryId;
    protected $userId;
    protected $filePath;
    protected $originalContent;
    protected $proposedContent;
    protected $description;
    protected $status;
    protected $reviewerId;
    protected $reviewComment;
    protected $reviewedAt;
    protected $commitMessage;
    protected $commitSha;
    protected $committedAt;
    protected $createdAt;
    protected $updatedAt;

    public function __construct() {
        $this->addType('repositoryId', 'integer');
        $this->addType('userId', 'string');
        $this->addType('filePath', 'string');
        $this->addType('originalContent', 'string');
        $this->addType('proposedContent', 'string');
        $this->addType('description', 'string');
        $this->addType('status', 'string');
        $this->addType('reviewerId', 'string');
        $this->addType('reviewComment', 'string');
        $this->addType('reviewedAt', 'integer');
        $this->addType('commitMessage', 'string');
        $this->addType('commitSha', 'string');
        $this->addType('committedAt', 'integer');
        $this->addType('createdAt', 'integer');
        $this->addType('updatedAt', 'integer');
    }

    public function jsonSerialize(): array {
        return [
            'id' => $this->id,
            'repositoryId' => $this->repositoryId,
            'userId' => $this->userId,
            'filePath' => $this->filePath,
            'originalContent' => $this->originalContent,
            'proposedContent' => $this->proposedContent,
            'description' => $this->description,
            'status' => $this->status,
            'reviewerId' => $this->reviewerId,
            'reviewComment' => $this->reviewComment,
            'reviewedAt' => $this->reviewedAt,
            'commitMessage' => $this->commitMessage,
            'commitSha' => $this->commitSha,
            'committedAt' => $this->committedAt,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }
}
