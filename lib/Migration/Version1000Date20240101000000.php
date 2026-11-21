<?php

declare(strict_types=1);

namespace OCA\GitIntegration\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\DB\Types;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

class Version1000Date20240101000000 extends SimpleMigrationStep {
    /**
     * @param IOutput $output
     * @param Closure $schemaClosure
     * @param array $options
     * @return null|ISchemaWrapper
     */
    public function changeSchema(IOutput $output, Closure $schemaClosure, array $options): ?ISchemaWrapper {
        /** @var ISchemaWrapper $schema */
        $schema = $schemaClosure();

        // Accounts table
        if (!$schema->hasTable('gitintegration_accounts')) {
            $table = $schema->createTable('gitintegration_accounts');
            $table->addColumn('id', Types::BIGINT, [
                'autoincrement' => true,
                'notnull' => true,
                'unsigned' => true,
            ]);
            $table->addColumn('user_id', Types::STRING, [
                'notnull' => true,
                'length' => 64,
            ]);
            $table->addColumn('provider', Types::STRING, [
                'notnull' => true,
                'length' => 32,
            ]);
            $table->addColumn('provider_url', Types::STRING, [
                'notnull' => true,
                'length' => 255,
            ]);
            $table->addColumn('username', Types::STRING, [
                'notnull' => true,
                'length' => 255,
            ]);
            $table->addColumn('access_token', Types::TEXT, [
                'notnull' => true,
            ]);
            $table->addColumn('status', Types::STRING, [
                'notnull' => true,
                'length' => 32,
                'default' => 'active',
            ]);
            $table->addColumn('last_checked', Types::BIGINT, [
                'notnull' => false,
                'unsigned' => true,
            ]);
            $table->addColumn('created_at', Types::BIGINT, [
                'notnull' => true,
                'unsigned' => true,
            ]);
            $table->addColumn('updated_at', Types::BIGINT, [
                'notnull' => true,
                'unsigned' => true,
            ]);

            $table->setPrimaryKey(['id']);
            $table->addIndex(['user_id'], 'git_accounts_user_idx');
            $table->addUniqueIndex(['user_id', 'provider', 'provider_url'], 'git_accounts_unique');
        }

        // Repositories table
        if (!$schema->hasTable('gitintegration_repos')) {
            $table = $schema->createTable('gitintegration_repos');
            $table->addColumn('id', Types::BIGINT, [
                'autoincrement' => true,
                'notnull' => true,
                'unsigned' => true,
            ]);
            $table->addColumn('user_id', Types::STRING, [
                'notnull' => true,
                'length' => 64,
            ]);
            $table->addColumn('account_id', Types::BIGINT, [
                'notnull' => true,
                'unsigned' => true,
            ]);
            $table->addColumn('repository_id', Types::STRING, [
                'notnull' => true,
                'length' => 255,
            ]);
            $table->addColumn('name', Types::STRING, [
                'notnull' => true,
                'length' => 255,
            ]);
            $table->addColumn('full_name', Types::STRING, [
                'notnull' => true,
                'length' => 512,
            ]);
            $table->addColumn('description', Types::TEXT, [
                'notnull' => false,
            ]);
            $table->addColumn('url', Types::STRING, [
                'notnull' => true,
                'length' => 512,
            ]);
            $table->addColumn('default_branch', Types::STRING, [
                'notnull' => true,
                'length' => 255,
            ]);
            $table->addColumn('current_branch', Types::STRING, [
                'notnull' => true,
                'length' => 255,
            ]);
            $table->addColumn('visibility', Types::STRING, [
                'notnull' => true,
                'length' => 32,
            ]);
            $table->addColumn('nextcloud_path', Types::STRING, [
                'notnull' => false,
                'length' => 4000,
            ]);
            $table->addColumn('sync_status', Types::STRING, [
                'notnull' => true,
                'length' => 32,
                'default' => 'idle',
            ]);
            $table->addColumn('last_sync_at', Types::BIGINT, [
                'notnull' => false,
                'unsigned' => true,
            ]);
            $table->addColumn('last_sync_commit', Types::STRING, [
                'notnull' => false,
                'length' => 255,
            ]);
            $table->addColumn('created_at', Types::BIGINT, [
                'notnull' => true,
                'unsigned' => true,
            ]);
            $table->addColumn('updated_at', Types::BIGINT, [
                'notnull' => true,
                'unsigned' => true,
            ]);

            $table->setPrimaryKey(['id']);
            $table->addIndex(['user_id'], 'git_repos_user_idx');
            $table->addIndex(['account_id'], 'git_repos_account_idx');
            $table->addUniqueIndex(['user_id', 'account_id', 'repository_id'], 'git_repos_unique');
        }

        // Sync records table
        if (!$schema->hasTable('gitintegration_sync')) {
            $table = $schema->createTable('gitintegration_sync');
            $table->addColumn('id', Types::BIGINT, [
                'autoincrement' => true,
                'notnull' => true,
                'unsigned' => true,
            ]);
            $table->addColumn('repository_id', Types::BIGINT, [
                'notnull' => true,
                'unsigned' => true,
            ]);
            $table->addColumn('status', Types::STRING, [
                'notnull' => true,
                'length' => 32,
            ]);
            $table->addColumn('direction', Types::STRING, [
                'notnull' => true,
                'length' => 32,
            ]);
            $table->addColumn('files_added', Types::INTEGER, [
                'notnull' => true,
                'default' => 0,
            ]);
            $table->addColumn('files_modified', Types::INTEGER, [
                'notnull' => true,
                'default' => 0,
            ]);
            $table->addColumn('files_deleted', Types::INTEGER, [
                'notnull' => true,
                'default' => 0,
            ]);
            $table->addColumn('conflicts', Types::INTEGER, [
                'notnull' => true,
                'default' => 0,
            ]);
            $table->addColumn('commit_hash', Types::STRING, [
                'notnull' => false,
                'length' => 255,
            ]);
            $table->addColumn('error_message', Types::TEXT, [
                'notnull' => false,
            ]);
            $table->addColumn('started_at', Types::BIGINT, [
                'notnull' => true,
                'unsigned' => true,
            ]);
            $table->addColumn('completed_at', Types::BIGINT, [
                'notnull' => false,
                'unsigned' => true,
            ]);

            $table->setPrimaryKey(['id']);
            $table->addIndex(['repository_id'], 'git_sync_repo_idx');
            $table->addIndex(['started_at'], 'git_sync_time_idx');
        }

        // Suggestions table
        if (!$schema->hasTable('gitintegration_suggestions')) {
            $table = $schema->createTable('gitintegration_suggestions');
            $table->addColumn('id', Types::BIGINT, [
                'autoincrement' => true,
                'notnull' => true,
                'unsigned' => true,
            ]);
            $table->addColumn('repository_id', Types::BIGINT, [
                'notnull' => true,
                'unsigned' => true,
            ]);
            $table->addColumn('user_id', Types::STRING, [
                'notnull' => true,
                'length' => 64,
            ]);
            $table->addColumn('file_path', Types::STRING, [
                'notnull' => true,
                'length' => 4000,
            ]);
            $table->addColumn('original_content', Types::TEXT, [
                'notnull' => true,
            ]);
            $table->addColumn('proposed_content', Types::TEXT, [
                'notnull' => true,
            ]);
            $table->addColumn('description', Types::TEXT, [
                'notnull' => false,
            ]);
            $table->addColumn('status', Types::STRING, [
                'notnull' => true,
                'length' => 32,
                'default' => 'draft',
            ]);
            $table->addColumn('reviewer_id', Types::STRING, [
                'notnull' => false,
                'length' => 64,
            ]);
            $table->addColumn('review_comment', Types::TEXT, [
                'notnull' => false,
            ]);
            $table->addColumn('reviewed_at', Types::BIGINT, [
                'notnull' => false,
                'unsigned' => true,
            ]);
            $table->addColumn('commit_message', Types::TEXT, [
                'notnull' => false,
            ]);
            $table->addColumn('commit_sha', Types::STRING, [
                'notnull' => false,
                'length' => 255,
            ]);
            $table->addColumn('committed_at', Types::BIGINT, [
                'notnull' => false,
                'unsigned' => true,
            ]);
            $table->addColumn('created_at', Types::BIGINT, [
                'notnull' => true,
                'unsigned' => true,
            ]);
            $table->addColumn('updated_at', Types::BIGINT, [
                'notnull' => true,
                'unsigned' => true,
            ]);

            $table->setPrimaryKey(['id']);
            $table->addIndex(['repository_id'], 'git_suggestions_repo_idx');
            $table->addIndex(['user_id'], 'git_suggestions_user_idx');
            $table->addIndex(['status'], 'git_suggestions_status_idx');
        }

        // Activity table
        if (!$schema->hasTable('gitintegration_activity')) {
            $table = $schema->createTable('gitintegration_activity');
            $table->addColumn('id', Types::BIGINT, [
                'autoincrement' => true,
                'notnull' => true,
                'unsigned' => true,
            ]);
            $table->addColumn('user_id', Types::STRING, [
                'notnull' => true,
                'length' => 64,
            ]);
            $table->addColumn('repository_id', Types::BIGINT, [
                'notnull' => false,
                'unsigned' => true,
            ]);
            $table->addColumn('event_type', Types::STRING, [
                'notnull' => true,
                'length' => 64,
            ]);
            $table->addColumn('event_data', Types::TEXT, [
                'notnull' => false,
            ]);
            $table->addColumn('status', Types::STRING, [
                'notnull' => true,
                'length' => 32,
            ]);
            $table->addColumn('error_message', Types::TEXT, [
                'notnull' => false,
            ]);
            $table->addColumn('created_at', Types::BIGINT, [
                'notnull' => true,
                'unsigned' => true,
            ]);

            $table->setPrimaryKey(['id']);
            $table->addIndex(['user_id'], 'git_activity_user_idx');
            $table->addIndex(['repository_id'], 'git_activity_repo_idx');
            $table->addIndex(['created_at'], 'git_activity_time_idx');
        }

        return $schema;
    }
}
