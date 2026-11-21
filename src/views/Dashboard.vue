<template>
    <div class="dashboard">
        <div v-if="loading" class="git-loading">
            <div class="git-spinner"></div>
            <p>Loading dashboard...</p>
        </div>

        <div v-else>
            <!-- Stats Overview -->
            <div class="git-grid" style="margin-bottom: 24px;">
                <div class="git-card">
                    <div class="git-card-title">Connected Accounts</div>
                    <div style="font-size: 32px; font-weight: 700; color: var(--color-primary-element);">
                        {{ accounts.length }}
                    </div>
                </div>
                <div class="git-card">
                    <div class="git-card-title">Linked Repositories</div>
                    <div style="font-size: 32px; font-weight: 700; color: var(--color-primary-element);">
                        {{ repositories.length }}
                    </div>
                </div>
                <div class="git-card">
                    <div class="git-card-title">Recent Syncs</div>
                    <div style="font-size: 32px; font-weight: 700; color: var(--color-primary-element);">
                        {{ recentActivities.filter(a => a.eventType === 'sync_completed').length }}
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="git-container">
                <h3>Quick Actions</h3>
                <div class="git-card-actions">
                    <button class="git-button" @click="$parent.currentTab = 'accounts'">
                        ➕ Add Account
                    </button>
                    <button class="git-button secondary" @click="$parent.currentTab = 'repositories'">
                        📁 View Repositories
                    </button>
                    <button class="git-button secondary" @click="syncAll" :disabled="syncing">
                        {{ syncing ? '🔄 Syncing...' : '🔄 Sync All' }}
                    </button>
                </div>
            </div>

            <!-- Linked Repositories -->
            <div class="git-container" style="margin-top: 20px;">
                <h3>Linked Repositories</h3>
                <div v-if="repositories.length === 0" class="git-empty">
                    <div class="git-empty-icon">📁</div>
                    <div class="git-empty-text">No repositories linked yet</div>
                    <div class="git-empty-hint">Link a repository to get started</div>
                    <button class="git-button" @click="$parent.currentTab = 'repositories'">
                        Link Repository
                    </button>
                </div>
                <div v-else>
                    <div v-for="repo in repositories" :key="repo.id" class="git-card">
                        <div class="git-card-title">{{ repo.name }}</div>
                        <div class="git-card-description">{{ repo.description || 'No description' }}</div>
                        <div class="git-card-meta">
                            <span>📂 {{ repo.nextcloudPath }}</span>
                            <span>🌿 {{ repo.currentBranch }}</span>
                            <span>
                                <span 
                                    :class="['git-status-badge', getStatusClass(repo.syncStatus)]"
                                >
                                    {{ getStatusText(repo.syncStatus) }}
                                </span>
                            </span>
                        </div>
                        <div class="git-card-actions">
                            <button class="git-button secondary" @click="syncRepository(repo.id)">
                                Sync
                            </button>
                            <button class="git-button secondary" @click="openRepository(repo)">
                                Open
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="git-container" style="margin-top: 20px;">
                <h3>Recent Activity</h3>
                <div v-if="recentActivities.length === 0" class="git-empty">
                    <div class="git-empty-text">No recent activity</div>
                </div>
                <ul v-else class="git-list">
                    <li v-for="activity in recentActivities.slice(0, 10)" :key="activity.id" class="git-list-item">
                        <div>
                            <strong>{{ getActivityLabel(activity.eventType) }}</strong>
                            <div style="font-size: 13px; color: var(--color-text-maxcontrast);">
                                {{ formatDate(activity.createdAt) }}
                            </div>
                        </div>
                        <span 
                            :class="['git-status-badge', getStatusClass(activity.status)]"
                        >
                            {{ activity.status }}
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
import api from '../api/client';

export default {
    name: 'Dashboard',
    data() {
        return {
            loading: true,
            syncing: false,
            accounts: [],
            repositories: [],
            recentActivities: [],
        };
    },
    async mounted() {
        await this.loadData();
    },
    methods: {
        async loadData() {
            this.loading = true;
            try {
                const [accounts, repositories, activities] = await Promise.all([
                    api.accounts.list(),
                    api.repositories.list(),
                    api.activity.list(20),
                ]);
                this.accounts = accounts;
                this.repositories = repositories;
                this.recentActivities = activities;
            } catch (error) {
                console.error('Failed to load dashboard data:', error);
            } finally {
                this.loading = false;
            }
        },
        async syncRepository(id) {
            try {
                await api.sync.sync(id);
                await this.loadData();
                alert('Sync completed successfully');
            } catch (error) {
                console.error('Sync failed:', error);
                alert('Sync failed: ' + (error.error || error.message || 'Unknown error'));
            }
        },
        async syncAll() {
            if (this.repositories.length === 0) return;
            this.syncing = true;
            try {
                for (const repo of this.repositories) {
                    await api.sync.sync(repo.id);
                }
                await this.loadData();
                alert('All repositories synced successfully');
            } catch (error) {
                console.error('Sync all failed:', error);
                alert('Some syncs failed');
            } finally {
                this.syncing = false;
            }
        },
        openRepository(repo) {
            // Navigate to repository details (placeholder)
            console.log('Opening repository:', repo);
        },
        getStatusClass(status) {
            const map = {
                'completed': 'success',
                'active': 'success',
                'success': 'success',
                'error': 'error',
                'failed': 'error',
                'syncing': 'info',
                'in_progress': 'info',
                'idle': 'info',
            };
            return map[status] || 'info';
        },
        getStatusText(status) {
            const map = {
                'completed': 'Completed',
                'syncing': 'Syncing',
                'error': 'Error',
                'idle': 'Idle',
                'in_progress': 'In Progress',
            };
            return map[status] || status;
        },
        getActivityLabel(eventType) {
            const map = {
                'account_connected': 'Account Connected',
                'account_updated': 'Account Updated',
                'account_disconnected': 'Account Disconnected',
                'repository_linked': 'Repository Linked',
                'repository_unlinked': 'Repository Unlinked',
                'sync_completed': 'Sync Completed',
                'sync_failed': 'Sync Failed',
                'branch_switched': 'Branch Switched',
            };
            return map[eventType] || eventType;
        },
        formatDate(timestamp) {
            const date = new Date(timestamp * 1000);
            return date.toLocaleString();
        },
    },
};
</script>

<style scoped>
.dashboard h3 {
    margin: 0 0 16px 0;
    font-size: 18px;
    font-weight: 600;
}
</style>
