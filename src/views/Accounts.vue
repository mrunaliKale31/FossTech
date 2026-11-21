<template>
    <div class="accounts">
        <div class="git-header">
            <h3>Connected Accounts</h3>
            <button class="git-button" @click="showAddModal = true">
                ➕ Add Account
            </button>
        </div>

        <div v-if="loading" class="git-loading">
            <div class="git-spinner"></div>
            <p>Loading accounts...</p>
        </div>

        <div v-else-if="accounts.length === 0" class="git-empty">
            <div class="git-empty-icon">🔗</div>
            <div class="git-empty-text">No accounts connected</div>
            <div class="git-empty-hint">Connect a GitLab account to get started</div>
            <button class="git-button" @click="showAddModal = true">
                Add Account
            </button>
        </div>

        <div v-else>
            <div v-for="account in accounts" :key="account.id" class="git-card">
                <div class="git-card-title">{{ account.provider.toUpperCase() }}</div>
                <div class="git-card-meta">
                    <span>👤 {{ account.username }}</span>
                    <span>🌐 {{ account.providerUrl }}</span>
                    <span>
                        <span :class="['git-status-badge', account.status === 'active' ? 'success' : 'error']">
                            {{ account.status }}
                        </span>
                    </span>
                </div>
                <div class="git-card-actions">
                    <button class="git-button secondary" @click="testConnection(account.id)">
                        Test Connection
                    </button>
                    <button class="git-button secondary" @click="editAccount(account)">
                        Edit
                    </button>
                    <button class="git-button secondary" style="background: var(--color-error);" @click="deleteAccount(account.id)">
                        Delete
                    </button>
                </div>
            </div>
        </div>

        <!-- Add/Edit Account Modal -->
        <div v-if="showAddModal || showEditModal" class="git-modal-overlay" @click.self="closeModals">
            <div class="git-modal">
                <div class="git-modal-header">
                    <h3 class="git-modal-title">{{ showEditModal ? 'Edit Account' : 'Add Account' }}</h3>
                    <button class="git-modal-close" @click="closeModals">×</button>
                </div>

                <form class="git-form" @submit.prevent="submitAccount">
                    <div class="git-form-group">
                        <label>Provider</label>
                        <select v-model="form.provider" :disabled="showEditModal">
                            <option value="gitlab">GitLab</option>
                            <option value="gitea" disabled>Gitea (Coming Soon)</option>
                            <option value="forgejo" disabled>Forgejo (Coming Soon)</option>
                        </select>
                    </div>

                    <div class="git-form-group">
                        <label>Provider URL</label>
                        <input 
                            v-model="form.providerUrl" 
                            type="url" 
                            placeholder="https://gitlab.com"
                            required
                            :disabled="showEditModal"
                        />
                        <div class="git-form-hint">Use https://gitlab.com for GitLab.com or your self-hosted URL</div>
                    </div>

                    <div class="git-form-group">
                        <label>Username</label>
                        <input 
                            v-model="form.username" 
                            type="text" 
                            placeholder="your-username"
                            required
                        />
                    </div>

                    <div class="git-form-group">
                        <label>Personal Access Token</label>
                        <input 
                            v-model="form.accessToken" 
                            type="password" 
                            placeholder="glpat-xxxx..."
                            required
                        />
                        <div class="git-form-hint">
                            Create a Personal Access Token with api, read_repository, and write_repository scopes
                        </div>
                    </div>

                    <div class="git-modal-actions">
                        <button type="button" class="git-button secondary" @click="closeModals">
                            Cancel
                        </button>
                        <button type="submit" class="git-button" :disabled="submitting">
                            {{ submitting ? 'Saving...' : (showEditModal ? 'Update' : 'Add Account') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import api from '../api/client';

export default {
    name: 'Accounts',
    data() {
        return {
            loading: true,
            submitting: false,
            accounts: [],
            showAddModal: false,
            showEditModal: false,
            editingAccount: null,
            form: {
                provider: 'gitlab',
                providerUrl: 'https://gitlab.com',
                username: '',
                accessToken: '',
            },
        };
    },
    async mounted() {
        await this.loadAccounts();
    },
    methods: {
        async loadAccounts() {
            this.loading = true;
            try {
                this.accounts = await api.accounts.list();
            } catch (error) {
                console.error('Failed to load accounts:', error);
            } finally {
                this.loading = false;
            }
        },
        async testConnection(id) {
            try {
                const result = await api.accounts.test(id);
                if (result.success) {
                    alert(`Connection successful! Username: ${result.username}`);
                    await this.loadAccounts();
                } else {
                    alert(`Connection failed: ${result.message}`);
                }
            } catch (error) {
                console.error('Test connection failed:', error);
                alert('Test connection failed: ' + (error.error || error.message || 'Unknown error'));
            }
        },
        editAccount(account) {
            this.editingAccount = account;
            this.form = {
                provider: account.provider,
                providerUrl: account.providerUrl,
                username: account.username,
                accessToken: '', // Don't populate the token for security
            };
            this.showEditModal = true;
        },
        async deleteAccount(id) {
            if (!confirm('Are you sure you want to delete this account? This will also unlink all associated repositories.')) {
                return;
            }

            try {
                await api.accounts.delete(id);
                await this.loadAccounts();
                alert('Account deleted successfully');
            } catch (error) {
                console.error('Failed to delete account:', error);
                alert('Failed to delete account: ' + (error.error || error.message || 'Unknown error'));
            }
        },
        async submitAccount() {
            this.submitting = true;
            try {
                if (this.showEditModal) {
                    await api.accounts.update(
                        this.editingAccount.id,
                        this.form.username,
                        this.form.accessToken
                    );
                    alert('Account updated successfully');
                } else {
                    await api.accounts.create(
                        this.form.provider,
                        this.form.providerUrl,
                        this.form.username,
                        this.form.accessToken
                    );
                    alert('Account added successfully');
                }
                await this.loadAccounts();
                this.closeModals();
            } catch (error) {
                console.error('Failed to save account:', error);
                alert('Failed to save account: ' + (error.error || error.message || 'Unknown error'));
            } finally {
                this.submitting = false;
            }
        },
        closeModals() {
            this.showAddModal = false;
            this.showEditModal = false;
            this.editingAccount = null;
            this.form = {
                provider: 'gitlab',
                providerUrl: 'https://gitlab.com',
                username: '',
                accessToken: '',
            };
        },
    },
};
</script>

<style scoped>
.accounts h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}
</style>
