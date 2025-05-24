import { createRouter, createWebHashHistory } from 'vue-router';

import Home   from '@/views/Home.vue';
import News   from '@/views/News.vue';
import About  from '@/views/About.vue';


const routes = [
  { path: '/',        component: Home },
  { path: '/news',    component: News },
  { path: '/about',   component: About },

];

export default createRouter({
  history: createWebHashHistory(),   // hash mode = no server config needed
  routes,
  scrollBehavior: () => ({ top: 0 })
});
