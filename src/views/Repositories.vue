<template>
    <div class="repositories">
        <div class="git-header">
            <h3>Repositories</h3>
            <button class="git-button" @click="showLinkModal = true" :disabled="accounts.length === 0">
                ➕ Link Repository
            </button>
        </div>

        <div v-if="loading" class="git-loading">
            <div class="git-spinner"></div>
            <p>Loading repositories...</p>
        </div>

        <div v-else-if="repositories.length === 0" class="git-empty">
            <div class="git-empty-icon">📁</div>
            <div class="git-empty-text">No repositories linked</div>
            <div class="git-empty-hint">
                {{ accounts.length === 0 ? 'Connect an account first' : 'Link a repository to get started' }}
            </div>
            <button v-if="accounts.length > 0" class="git-button" @click="showLinkModal = true">
                Link Repository
            </button>
            <button v-else class="git-button" @click="$parent.currentTab = 'accounts'">
                Add Account
            </button>
        </div>

        <div v-else>
            <div v-for="repo in repositories" :key="repo.id" class="git-card">
                <div class="git-card-title">{{ repo.name }}</div>
                <div class="git-card-description">{{ repo.description || 'No description' }}</div>
                <div class="git-card-meta">
                    <span>📂 {{ repo.nextcloudPath }}</span>
                    <span>🌿 {{ repo.currentBranch }}</span>
                    <span>🔗 {{ repo.visibility }}</span>
                    <span>
                        <span :class="['git-status-badge', getStatusClass(repo.syncStatus)]">
                            {{ repo.syncStatus }}
                        </span>
                    </span>
                </div>
                <div v-if="repo.lastSyncAt" style="font-size: 13px; color: var(--color-text-maxcontrast); margin-top: 8px;">
                    Last synced: {{ formatDate(repo.lastSyncAt) }}
                </div>
                <div class="git-card-actions">
                    <button class="git-button secondary" @click="syncRepository(repo.id)" :disabled="repo.syncStatus === 'syncing'">
                        {{ repo.syncStatus === 'syncing' ? '⏳ Syncing...' : '🔄 Sync' }}
                    </button>
                    <button class="git-button secondary" @click="viewDetails(repo)">
                        View Details
                    </button>
                    <button class="git-button secondary" style="background: var(--color-error);" @click="unlinkRepository(repo.id)">
                        Unlink
                    </button>
                </div>
            </div>
        </div>

        <!-- Link Repository Modal -->
        <div v-if="showLinkModal" class="git-modal-overlay" @click.self="closeLinkModal">
            <div class="git-modal">
                <div class="git-modal-header">
                    <h3 class="git-modal-title">Link Repository</h3>
                    <button class="git-modal-close" @click="closeLinkModal">×</button>
                </div>

                <div v-if="linkStep === 1" class="git-form">
                    <div class="git-form-group">
                        <label>Select Account</label>
                        <select v-model="selectedAccountId" @change="loadRemoteRepositories">
                            <option :value="null">Choose an account...</option>
                            <option v-for="account in accounts" :key="account.id" :value="account.id">
                                {{ account.provider.toUpperCase() }} - {{ account.username }}
                            </option>
                        </select>
                    </div>

                    <div v-if="loadingRepos" class="git-loading">
                        <div class="git-spinner"></div>
                        <p>Loading repositories...</p>
                    </div>

                    <div v-else-if="remoteRepositories.length > 0">
                        <div class="git-form-group">
                            <label>Select Repository</label>
                            <div style="max-height: 300px; overflow-y: auto; border: 1px solid var(--color-border); border-radius: var(--border-radius);">
                                <div 
                                    v-for="repo in remoteRepositories" 
                                    :key="repo.id"
                                    :class="['git-list-item', { 'selected': selectedRemoteRepo && selectedRemoteRepo.id === repo.id }]"
                                    @click="selectRemoteRepo(repo)"
                                    style="cursor: pointer;"
                                >
                                    <div>
                                        <strong>{{ repo.name }}</strong>
                                        <div style="font-size: 13px; color: var(--color-text-maxcontrast);">
                                            {{ repo.description || 'No description' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="git-modal-actions">
                            <button class="git-button secondary" @click="closeLinkModal">Cancel</button>
                            <button class="git-button" @click="linkStep = 2" :disabled="!selectedRemoteRepo">
                                Next
                            </button>
                        </div>
                    </div>
                </div>

                <div v-else-if="linkStep === 2" class="git-form">
                    <div style="margin-bottom: 16px;">
                        <strong>Repository:</strong> {{ selectedRemoteRepo.name }}
                    </div>

                    <div class="git-form-group">
                        <label>Nextcloud Folder Path</label>
                        <input 
                            v-model="nextcloudPath" 
                            type="text" 
                            placeholder="/GitRepos/my-project"
                            required
                        />
                        <div class="git-form-hint">
                            The repository will be synced to this folder in your Nextcloud
                        </div>
                    </div>

                    <div class="git-modal-actions">
                        <button class="git-button secondary" @click="linkStep = 1">Back</button>
                        <button class="git-button" @click="linkRepository" :disabled="submitting || !nextcloudPath">
                            {{ submitting ? 'Linking...' : 'Link Repository' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Repository Details Modal -->
        <div v-if="showDetailsModal && selectedRepository" class="git-modal-overlay" @click.self="closeDetailsModal">
            <div class="git-modal">
                <div class="git-modal-header">
                    <h3 class="git-modal-title">{{ selectedRepository.name }}</h3>
                    <button class="git-modal-close" @click="closeDetailsModal">×</button>
                </div>

                <div>
                    <div style="margin-bottom: 16px;">
                        <strong>Description:</strong> {{ selectedRepository.description || 'No description' }}
                    </div>
                    <div style="margin-bottom: 16px;">
                        <strong>Full Name:</strong> {{ selectedRepository.fullName }}
                    </div>
                    <div style="margin-bottom: 16px;">
                        <strong>URL:</strong> <a :href="selectedRepository.url" target="_blank">{{ selectedRepository.url }}</a>
                    </div>
                    <div style="margin-bottom: 16px;">
                        <strong>Default Branch:</strong> {{ selectedRepository.defaultBranch }}
                    </div>
                    <div style="margin-bottom: 16px;">
                        <strong>Current Branch:</strong> {{ selectedRepository.currentBranch }}
                    </div>
                    <div style="margin-bottom: 16px;">
                        <strong>Nextcloud Path:</strong> {{ selectedRepository.nextcloudPath }}
                    </div>
                    <div style="margin-bottom: 16px;">
                        <strong>Visibility:</strong> {{ selectedRepository.visibility }}
                    </div>
                    <div style="margin-bottom: 16px;">
                        <strong>Sync Status:</strong> 
                        <span :class="['git-status-badge', getStatusClass(selectedRepository.syncStatus)]">
                            {{ selectedRepository.syncStatus }}
                        </span>
                    </div>
                    <div v-if="selectedRepository.lastSyncAt">
                        <strong>Last Sync:</strong> {{ formatDate(selectedRepository.lastSyncAt) }}
                    </div>
                </div>

                <div class="git-modal-actions">
                    <button class="git-button secondary" @click="closeDetailsModal">Close</button>
                    <button class="git-button" @click="syncRepository(selectedRepository.id)">
                        Sync Now
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import api from '../api/client';

export default {
    name: 'Repositories',
    data() {
        return {
            loading: true,
            loadingRepos: false,
            submitting: false,
            accounts: [],
            repositories: [],
            remoteRepositories: [],
            selectedAccountId: null,
            selectedRemoteRepo: null,
            selectedRepository: null,
            showLinkModal: false,
            showDetailsModal: false,
            linkStep: 1,
            nextcloudPath: '',
        };
    },
    async mounted() {
        await this.loadData();
    },
    methods: {
        async loadData() {
            this.loading = true;
            try {
                const [accounts, repositories] = await Promise.all([
                    api.accounts.list(),
                    api.repositories.list(),
                ]);
                this.accounts = accounts.filter(a => a.status === 'active');
                this.repositories = repositories;
            } catch (error) {
                console.error('Failed to load data:', error);
            } finally {
                this.loading = false;
            }
        },
        async loadRemoteRepositories() {
            if (!this.selectedAccountId) return;

            this.loadingRepos = true;
            try {
                this.remoteRepositories = await api.repositories.fetch(this.selectedAccountId);
            } catch (error) {
                console.error('Failed to load remote repositories:', error);
                alert('Failed to load repositories: ' + (error.error || error.message || 'Unknown error'));
            } finally {
                this.loadingRepos = false;
            }
        },
        selectRemoteRepo(repo) {
            this.selectedRemoteRepo = repo;
            this.nextcloudPath = `/GitRepos/${repo.name}`;
        },
        async linkRepository() {
            if (!this.selectedRemoteRepo || !this.nextcloudPath) return;

            this.submitting = true;
            try {
                await api.repositories.link(
                    this.selectedAccountId,
                    this.selectedRemoteRepo.id,
                    this.selectedRemoteRepo.name,
                    this.selectedRemoteRepo.full_name,
                    this.selectedRemoteRepo.description,
                    this.selectedRemoteRepo.url,
                    this.selectedRemoteRepo.default_branch,
                    this.selectedRemoteRepo.visibility,
                    this.nextcloudPath
                );
                alert('Repository linked successfully');
                await this.loadData();
                this.closeLinkModal();
            } catch (error) {
                console.error('Failed to link repository:', error);
                alert('Failed to link repository: ' + (error.error || error.message || 'Unknown error'));
            } finally {
                this.submitting = false;
            }
        },
        async unlinkRepository(id) {
            if (!confirm('Are you sure you want to unlink this repository? Files in Nextcloud will not be deleted.')) {
                return;
            }

            try {
                await api.repositories.unlink(id);
                await this.loadData();
                alert('Repository unlinked successfully');
            } catch (error) {
                console.error('Failed to unlink repository:', error);
                alert('Failed to unlink repository: ' + (error.error || error.message || 'Unknown error'));
            }
        },
        async syncRepository(id) {
            try {
                // Update UI immediately
                const repo = this.repositories.find(r => r.id === id);
                if (repo) repo.syncStatus = 'syncing';

                const result = await api.sync.sync(id);
                if (result.success) {
                    alert('Sync completed successfully');
                } else {
                    alert('Sync failed: ' + result.error);
                }
                await this.loadData();
            } catch (error) {
                console.error('Sync failed:', error);
                alert('Sync failed: ' + (error.error || error.message || 'Unknown error'));
                await this.loadData();
            }
        },
        viewDetails(repo) {
            this.selectedRepository = repo;
            this.showDetailsModal = true;
        },
        closeLinkModal() {
            this.showLinkModal = false;
            this.linkStep = 1;
            this.selectedAccountId = null;
            this.selectedRemoteRepo = null;
            this.remoteRepositories = [];
            this.nextcloudPath = '';
        },
        closeDetailsModal() {
            this.showDetailsModal = false;
            this.selectedRepository = null;
        },
        getStatusClass(status) {
            const map = {
                'completed': 'success',
                'idle': 'info',
                'syncing': 'info',
                'error': 'error',
            };
            return map[status] || 'info';
        },
        formatDate(timestamp) {
            const date = new Date(timestamp * 1000);
            return date.toLocaleString();
        },
    },
};
</script>

<style scoped>
.repositories h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

.git-list-item.selected {
    background: var(--color-primary-element-light);
    border-color: var(--color-primary-element);
}
</style>
