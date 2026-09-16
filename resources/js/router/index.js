import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const routes = [
    {
        path: '/login',
        name: 'login',
        component: () => import('@/pages/Login.vue'),
        meta: { guestOnly: true },
    },
    {
        path: '/demandes',
        name: 'demandes.index',
        component: () => import('@/pages/demandes/Index.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/demandes/nouvelle',
        name: 'demandes.create',
        component: () => import('@/pages/demandes/Create.vue'),
        meta: { requiresAuth: true },
    },
    { path: '/', redirect: '/demandes' },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to) => {
    const auth = useAuthStore();

    if (to.meta.requiresAuth && !auth.isAuthenticated) {
        return { name: 'login' };
    }

    if (to.meta.guestOnly && auth.isAuthenticated) {
        return { name: 'demandes.index' };
    }
});

export default router;
