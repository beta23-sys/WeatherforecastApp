import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import 'bootstrap/dist/css/bootstrap.min.css';
import '@fortawesome/fontawesome-free/css/all.min.css';


const app = createApp(App);

// simple custom directive (auto-focus) – used on News search box
app.directive('focus', {
  mounted(el) { el.focus(); }
});

app.use(router);
app.mount('#app');
