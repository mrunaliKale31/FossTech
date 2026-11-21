<?php

declare(strict_types=1);

namespace OCA\GitIntegration\Db;

use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class SyncRecordMapper extends QBMapper {
    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'gitintegration_sync', SyncRecord::class);
    }

    /**
     * @param int $repositoryId
     * @param int $limit
     * @return SyncRecord[]
     */
    public function findByRepository(int $repositoryId, int $limit = 50): array {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from($this->getTableName())
            ->where($qb->expr()->eq('repository_id', $qb->createNamedParameter($repositoryId, IQueryBuilder::PARAM_INT)))
            ->orderBy('started_at', 'DESC')
            ->setMaxResults($limit);

        return $this->findEntities($qb);
    }

    /**
     * @param int $repositoryId
     * @return SyncRecord|null
     */
    public function findLastSuccessful(int $repositoryId): ?SyncRecord {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from($this->getTableName())
            ->where($qb->expr()->eq('repository_id', $qb->createNamedParameter($repositoryId, IQueryBuilder::PARAM_INT)))
            ->andWhere($qb->expr()->eq('status', $qb->createNamedParameter('completed', IQueryBuilder::PARAM_STR)))
            ->orderBy('completed_at', 'DESC')
            ->setMaxResults(1);

        try {
            return $this->findEntity($qb);
        } catch (\OCP\AppFramework\Db\DoesNotExistException $e) {
            return null;
        }
    }
}
