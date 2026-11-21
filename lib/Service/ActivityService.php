<?php

declare(strict_types=1);

namespace OCA\GitIntegration\Service;

use OCA\GitIntegration\Db\Activity;
use OCA\GitIntegration\Db\ActivityMapper;

class ActivityService {
    private ActivityMapper $mapper;

    public function __construct(ActivityMapper $mapper) {
        $this->mapper = $mapper;
    }

    /**
     * Log an activity
     */
    public function log(
        string $userId,
        ?int $repositoryId,
        string $eventType,
        array $eventData,
        string $status,
        ?string $errorMessage = null
    ): Activity {
        $activity = new Activity();
        $activity->setUserId($userId);
        $activity->setRepositoryId($repositoryId);
        $activity->setEventType($eventType);
        $activity->setEventData(json_encode($eventData));
        $activity->setStatus($status);
        $activity->setErrorMessage($errorMessage);
        $activity->setCreatedAt(time());

        return $this->mapper->insert($activity);
    }

    /**
     * Get activities for a user
     */
    public function getForUser(string $userId, int $limit = 100): array {
        return $this->mapper->findForUser($userId, $limit);
    }

    /**
     * Get activities for a repository
     */
    public function getForRepository(int $repositoryId, int $limit = 100): array {
        return $this->mapper->findForRepository($repositoryId, $limit);
    }
}
