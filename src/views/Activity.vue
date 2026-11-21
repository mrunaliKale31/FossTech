<template>
    <div class="activity">
        <div class="git-header">
            <h3>Activity History</h3>
            <button class="git-button secondary" @click="loadActivity">
                🔄 Refresh
            </button>
        </div>

        <div v-if="loading" class="git-loading">
            <div class="git-spinner"></div>
            <p>Loading activity...</p>
        </div>

        <div v-else-if="activities.length === 0" class="git-empty">
            <div class="git-empty-icon">📋</div>
            <div class="git-empty-text">No activity yet</div>
            <div class="git-empty-hint">Activity will appear here once you start using Git Integration</div>
        </div>

        <div v-else>
            <div v-for="activity in activities" :key="activity.id" class="git-card">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div style="flex: 1;">
                        <div class="git-card-title">{{ getActivityLabel(activity.eventType) }}</div>
                        <div class="git-card-meta">
                            <span>📅 {{ formatDate(activity.createdAt) }}</span>
                            <span v-if="activity.repositoryId">🗂️ Repository ID: {{ activity.repositoryId }}</span>
                        </div>
                        <div v-if="activity.eventData && Object.keys(activity.eventData).length > 0" style="margin-top: 8px;">
                            <div style="font-size: 13px; color: var(--color-text-maxcontrast);">
                                <div v-for="(value, key) in activity.eventData" :key="key">
                                    <strong>{{ key }}:</strong> {{ formatValue(value) }}
                                </div>
                            </div>
                        </div>
                        <div v-if="activity.errorMessage" style="margin-top: 8px; color: var(--color-error); font-size: 13px;">
                            Error: {{ activity.errorMessage }}
                        </div>
                    </div>
                    <span :class="['git-status-badge', getStatusClass(activity.status)]">
                        {{ activity.status }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import api from '../api/client';

export default {
    name: 'Activity',
    data() {
        return {
            loading: true,
            activities: [],
        };
    },
    async mounted() {
        await this.loadActivity();
    },
    methods: {
        async loadActivity() {
            this.loading = true;
            try {
                this.activities = await api.activity.list(100);
            } catch (error) {
                console.error('Failed to load activity:', error);
            } finally {
                this.loading = false;
            }
        },
        getActivityLabel(eventType) {
            const map = {
                'account_connected': '🔗 Account Connected',
                'account_updated': '✏️ Account Updated',
                'account_disconnected': '🔌 Account Disconnected',
                'repository_linked': '📁 Repository Linked',
                'repository_unlinked': '❌ Repository Unlinked',
                'sync_completed': '✅ Sync Completed',
                'sync_failed': '❌ Sync Failed',
                'branch_switched': '🌿 Branch Switched',
                'suggestion_created': '💡 Suggestion Created',
                'suggestion_approved': '✔️ Suggestion Approved',
                'suggestion_rejected': '✖️ Suggestion Rejected',
                'suggestion_committed': '💾 Suggestion Committed',
            };
            return map[eventType] || eventType;
        },
        getStatusClass(status) {
            const map = {
                'success': 'success',
                'error': 'error',
                'warning': 'warning',
                'info': 'info',
            };
            return map[status] || 'info';
        },
        formatDate(timestamp) {
            const date = new Date(timestamp * 1000);
            return date.toLocaleString();
        },
        formatValue(value) {
            if (typeof value === 'object') {
                return JSON.stringify(value);
            }
            return value;
        },
    },
};
</script>

<style scoped>
.activity h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}
</style>
