<?php

declare(strict_types=1);

namespace OCA\GitIntegration\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class AccountMapper extends QBMapper {
    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'gitintegration_accounts', Account::class);
    }

    /**
     * @param string $userId
     * @return Account[]
     */
    public function findAllForUser(string $userId): array {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from($this->getTableName())
            ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR)));

        return $this->findEntities($qb);
    }

    /**
     * @param int $id
     * @param string $userId
     * @return Account
     * @throws DoesNotExistException
     */
    public function findByIdForUser(int $id, string $userId): Account {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from($this->getTableName())
            ->where($qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)))
            ->andWhere($qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR)));

        return $this->findEntity($qb);
    }

    /**
     * @param string $userId
     * @param string $provider
     * @param string $providerUrl
     * @return Account|null
     */
    public function findByProviderForUser(string $userId, string $provider, string $providerUrl): ?Account {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from($this->getTableName())
            ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR)))
            ->andWhere($qb->expr()->eq('provider', $qb->createNamedParameter($provider, IQueryBuilder::PARAM_STR)))
            ->andWhere($qb->expr()->eq('provider_url', $qb->createNamedParameter($providerUrl, IQueryBuilder::PARAM_STR)));

        try {
            return $this->findEntity($qb);
        } catch (DoesNotExistException $e) {
            return null;
        }
    }
}
