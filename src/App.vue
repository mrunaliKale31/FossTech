<template>
    <div id="git-integration-app">
        <div class="git-container">
            <div class="git-header">
                <h2>Git Integration</h2>
                <div class="git-tabs">
                    <button 
                        v-for="tab in tabs" 
                        :key="tab.id"
                        :class="['git-tab', { active: currentTab === tab.id }]"
                        @click="currentTab = tab.id"
                    >
                        {{ tab.label }}
                    </button>
                </div>
            </div>

            <div class="git-content">
                <component :is="currentComponent" />
            </div>
        </div>
    </div>
</template>

<script>
import Dashboard from './views/Dashboard.vue';
import Accounts from './views/Accounts.vue';
import Repositories from './views/Repositories.vue';
import Activity from './views/Activity.vue';

export default {
    name: 'App',
    components: {
        Dashboard,
        Accounts,
        Repositories,
        Activity,
    },
    data() {
        return {
            currentTab: 'dashboard',
            tabs: [
                { id: 'dashboard', label: 'Dashboard' },
                { id: 'repositories', label: 'Repositories' },
                { id: 'accounts', label: 'Accounts' },
                { id: 'activity', label: 'Activity' },
            ],
        };
    },
    computed: {
        currentComponent() {
            const componentMap = {
                'dashboard': 'Dashboard',
                'repositories': 'Repositories',
                'accounts': 'Accounts',
                'activity': 'Activity',
            };
            return componentMap[this.currentTab] || 'Dashboard';
        },
    },
};
</script>

<style scoped>
#git-integration-app {
    min-height: 100vh;
}

.git-content {
    padding-top: 20px;
}
</style>
