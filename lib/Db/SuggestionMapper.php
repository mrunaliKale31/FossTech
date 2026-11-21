<?php

declare(strict_types=1);

namespace OCA\GitIntegration\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class SuggestionMapper extends QBMapper {
    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'gitintegration_suggestions', Suggestion::class);
    }

    /**
     * @param int $repositoryId
     * @return Suggestion[]
     */
    public function findByRepository(int $repositoryId): array {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from($this->getTableName())
            ->where($qb->expr()->eq('repository_id', $qb->createNamedParameter($repositoryId, IQueryBuilder::PARAM_INT)))
            ->orderBy('created_at', 'DESC');

        return $this->findEntities($qb);
    }

    /**
     * @param string $userId
     * @return Suggestion[]
     */
    public function findByUser(string $userId): array {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from($this->getTableName())
            ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR)))
            ->orderBy('created_at', 'DESC');

        return $this->findEntities($qb);
    }

    /**
     * @param string $status
     * @return Suggestion[]
     */
    public function findByStatus(string $status): array {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from($this->getTableName())
            ->where($qb->expr()->eq('status', $qb->createNamedParameter($status, IQueryBuilder::PARAM_STR)))
            ->orderBy('created_at', 'DESC');

        return $this->findEntities($qb);
    }

    /**
     * @param int $id
     * @return Suggestion
     * @throws DoesNotExistException
     */
    public function findById(int $id): Suggestion {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from($this->getTableName())
            ->where($qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)));

        return $this->findEntity($qb);
    }
}
