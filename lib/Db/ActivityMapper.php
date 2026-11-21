<?php

declare(strict_types=1);

namespace OCA\GitIntegration\Db;

use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class ActivityMapper extends QBMapper {
    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'gitintegration_activity', Activity::class);
    }

    /**
     * @param string $userId
     * @param int $limit
     * @return Activity[]
     */
    public function findForUser(string $userId, int $limit = 100): array {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from($this->getTableName())
            ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR)))
            ->orderBy('created_at', 'DESC')
            ->setMaxResults($limit);

        return $this->findEntities($qb);
    }

    /**
     * @param int $repositoryId
     * @param int $limit
     * @return Activity[]
     */
    public function findForRepository(int $repositoryId, int $limit = 100): array {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from($this->getTableName())
            ->where($qb->expr()->eq('repository_id', $qb->createNamedParameter($repositoryId, IQueryBuilder::PARAM_INT)))
            ->orderBy('created_at', 'DESC')
            ->setMaxResults($limit);

        return $this->findEntities($qb);
    }
}
