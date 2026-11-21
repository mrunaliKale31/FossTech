import axios from '@nextcloud/axios';
import { generateUrl } from '@nextcloud/router';

const apiClient = {
    // Base API call
    async call(method, endpoint, data = null) {
        const url = generateUrl(`/apps/gitintegration${endpoint}`);
        try {
            const response = await axios({
                method,
                url,
                data,
            });
            return response.data;
        } catch (error) {
            console.error(`API Error [${method} ${endpoint}]:`, error);
            throw error.response?.data || error;
        }
    },

    // Account endpoints
    accounts: {
        list() {
            return apiClient.call('GET', '/api/accounts');
        },
        get(id) {
            return apiClient.call('GET', `/api/accounts/${id}`);
        },
        create(provider, providerUrl, username, accessToken) {
            return apiClient.call('POST', '/api/accounts', {
                provider,
                providerUrl,
                username,
                accessToken,
            });
        },
        update(id, username, accessToken) {
            return apiClient.call('PUT', `/api/accounts/${id}`, {
                username,
                accessToken,
            });
        },
        delete(id) {
            return apiClient.call('DELETE', `/api/accounts/${id}`);
        },
        test(id) {
            return apiClient.call('POST', `/api/accounts/${id}/test`);
        },
    },

    // Repository endpoints
    repositories: {
        list() {
            return apiClient.call('GET', '/api/repositories');
        },
        get(id) {
            return apiClient.call('GET', `/api/repositories/${id}`);
        },
        fetch(accountId, page = 1, perPage = 20) {
            return apiClient.call('POST', '/api/repositories/fetch', {
                accountId,
                page,
                perPage,
            });
        },
        link(accountId, repositoryId, name, fullName, description, url, defaultBranch, visibility, nextcloudPath) {
            return apiClient.call('POST', '/api/repositories/link', {
                accountId,
                repositoryId,
                name,
                fullName,
                description,
                url,
                defaultBranch,
                visibility,
                nextcloudPath,
            });
        },
        unlink(id) {
            return apiClient.call('POST', `/api/repositories/${id}/unlink`);
        },
        details(id) {
            return apiClient.call('GET', `/api/repositories/${id}/details`);
        },
    },

    // Sync endpoints
    sync: {
        sync(repositoryId) {
            return apiClient.call('POST', `/api/repositories/${repositoryId}/sync`);
        },
        status(repositoryId) {
            return apiClient.call('GET', `/api/repositories/${repositoryId}/sync/status`);
        },
        history(repositoryId, limit = 50) {
            return apiClient.call('GET', `/api/repositories/${repositoryId}/sync/history?limit=${limit}`);
        },
        conflicts(repositoryId) {
            return apiClient.call('GET', `/api/repositories/${repositoryId}/sync/conflicts`);
        },
    },

    // Branch endpoints
    branches: {
        list(repositoryId) {
            return apiClient.call('GET', `/api/repositories/${repositoryId}/branches`);
        },
        switch(repositoryId, branch) {
            return apiClient.call('POST', `/api/repositories/${repositoryId}/branches/switch`, {
                branch,
            });
        },
    },

    // Commit endpoints
    commits: {
        list(repositoryId, page = 1, perPage = 20) {
            return apiClient.call('GET', `/api/repositories/${repositoryId}/commits?page=${page}&perPage=${perPage}`);
        },
        get(repositoryId, sha) {
            return apiClient.call('GET', `/api/repositories/${repositoryId}/commits/${sha}`);
        },
    },

    // Release endpoints
    releases: {
        list(repositoryId) {
            return apiClient.call('GET', `/api/repositories/${repositoryId}/releases`);
        },
        tags(repositoryId) {
            return apiClient.call('GET', `/api/repositories/${repositoryId}/tags`);
        },
        generateNotes(repositoryId, fromTag, toTag) {
            return apiClient.call('POST', `/api/repositories/${repositoryId}/release-notes/generate`, {
                fromTag,
                toTag,
            });
        },
        saveNotes(repositoryId, content, filename) {
            return apiClient.call('POST', `/api/repositories/${repositoryId}/release-notes/save`, {
                content,
                filename,
            });
        },
    },

    // Activity endpoints
    activity: {
        list(limit = 100) {
            return apiClient.call('GET', `/api/activity?limit=${limit}`);
        },
        repository(repositoryId, limit = 100) {
            return apiClient.call('GET', `/api/activity/repository/${repositoryId}?limit=${limit}`);
        },
    },

    // Documentation endpoints
    documentation: {
        list(repositoryId) {
            return apiClient.call('GET', `/api/repositories/${repositoryId}/docs`);
        },
        get(repositoryId, filePath) {
            return apiClient.call('GET', `/api/repositories/${repositoryId}/docs/file?path=${encodeURIComponent(filePath)}`);
        },
        render(repositoryId, filePath, content) {
            return apiClient.call('POST', `/api/repositories/${repositoryId}/docs/render`, {
                filePath,
                content,
            });
        },
    },

    // Suggestion endpoints
    suggestions: {
        list(repositoryId = null) {
            const params = repositoryId ? `?repositoryId=${repositoryId}` : '';
            return apiClient.call('GET', `/api/suggestions${params}`);
        },
        get(id) {
            return apiClient.call('GET', `/api/suggestions/${id}`);
        },
        create(repositoryId, filePath, originalContent, proposedContent, description) {
            return apiClient.call('POST', '/api/suggestions', {
                repositoryId,
                filePath,
                originalContent,
                proposedContent,
                description,
            });
        },
        update(id, proposedContent, description) {
            return apiClient.call('PUT', `/api/suggestions/${id}`, {
                proposedContent,
                description,
            });
        },
        approve(id, comment) {
            return apiClient.call('POST', `/api/suggestions/${id}/approve`, {
                comment,
            });
        },
        reject(id, comment) {
            return apiClient.call('POST', `/api/suggestions/${id}/reject`, {
                comment,
            });
        },
        commit(id, commitMessage, branch) {
            return apiClient.call('POST', `/api/suggestions/${id}/commit`, {
                commitMessage,
                branch,
            });
        },
    },

    // Settings endpoints
    settings: {
        get() {
            return apiClient.call('GET', '/api/settings');
        },
        update(settings) {
            return apiClient.call('PUT', '/api/settings', settings);
        },
    },
};

export default apiClient;
