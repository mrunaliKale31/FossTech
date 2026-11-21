import Vue from 'vue';
import App from './App.vue';
import './styles/main.css';

// Configure Vue
Vue.config.productionTip = false;
Vue.config.devtools = true;

// Create and mount the Vue app
const app = new Vue({
    el: '#app',
    render: h => h(App),
});

export default app;
