import { createRouter, createWebHistory } from 'vue-router';
import { useAuth } from '../composables/auth.js';
import AuthenticatedLayout from '../layouts/AuthenticatedLayout.vue';
import Dashboard from '../pages/Dashboard.vue';
import Settings from '../pages/Settings.vue';
import Login from '../pages/Login.vue';
import Register from '../pages/Register.vue';
import Products from '../pages/Products.vue';

const routes = [
  {
    path: '/',
    component: AuthenticatedLayout,
    meta: { requiresAuth: true },
    children: [
      {
        path: 'dashboard',
        name: 'Dashboard',
        component: Dashboard,
      },
      {
        path: 'settings',
        name: 'Settings',
        component: Settings,
      },
      {
        path: 'products',
        name: 'Products',
        component: Products,
      },
    ]
  },
  {
    path: '/login',
    name: 'Login',
    component: Login
  },
  {
    path: '/Register',
    name: 'Register',
    component: Register
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

const { isAuthenticated } = useAuth();

router.beforeEach((to, from, next) => {
  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (!isAuthenticated.value) {
      next({ name: 'Login' });
    } else {
      next();
    }
  } else {
    next();
  }
});

export default router;