import { createRouter, createWebHistory } from 'vue-router';

import Home     from '@/views/Home.vue';
import News     from '@/views/News.vue';
import About    from '@/views/About.vue';
import Login    from '@/views/Login.vue';
import Register from '@/views/Register.vue';

const routes = [
  { path: '/',        name: 'Home',     component: Home },
  { path: '/news',    name: 'News',     component: News,     meta: { requiresAuth: true } },
  { path: '/about',   name: 'About',    component: About },
  { path: '/login',   name: 'Login',    component: Login },
  { path: '/register',name: 'Register', component: Register }
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
  scrollBehavior: () => ({ top: 0 })
});

// Global guard: redirect to /login if trying to access a protected route
router.beforeEach((to) => {
  if (to.meta.requiresAuth) {
    if (!localStorage.getItem('userEmail')) {
      return { name: 'Login', query: { redirect: to.fullPath } };
    }
  }
});

export default router;
