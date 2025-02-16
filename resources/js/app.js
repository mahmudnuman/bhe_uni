import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import 'bootstrap/dist/css/bootstrap.min.css';  // Importing Bootstrap CSS globally

import $ from 'jquery';

const app = createApp(App);
app.use(router);  // Use the router
app.mount('#app');  // Mount the app to the DOM
