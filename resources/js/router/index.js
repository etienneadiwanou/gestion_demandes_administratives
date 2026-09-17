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
        path: '/dashboard',
        name: 'dashboard',
        component: () => import('@/pages/Dashboard.vue'),
        meta: { requiresAuth: true },
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
    {
        path: '/demandes/:id',
        name: 'demandes.show',
        component: () => import('@/pages/demandes/Show.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/documents',
        name: 'documents.index',
        component: () => import('@/pages/documents/Index.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/affectations',
        name: 'affectations.index',
        component: () => import('@/pages/affectations/Index.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/utilisateurs',
        name: 'utilisateurs.index',
        component: () => import('@/pages/utilisateurs/Index.vue'),
        meta: { requiresAuth: true, adminOnly: true },
    },
    {
        path: '/departements',
        name: 'departements.index',
        component: () => import('@/pages/departements/Index.vue'),
        meta: { requiresAuth: true, adminOnly: true },
    },
    {
        path: '/parametres',
        name: 'parametres.index',
        component: () => import('@/pages/parametres/TypeDemandes.vue'),
        meta: { requiresAuth: true, adminOnly: true },
    },
    { path: '/', redirect: '/dashboard' },
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
        return { name: 'dashboard' };
    }

    if (to.meta.adminOnly && auth.role !== 'administrateur') {
        return { name: 'dashboard' };
    }
});

export default router;
