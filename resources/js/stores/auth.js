import { defineStore } from 'pinia';
import api from '@/lib/api';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: localStorage.getItem('adminflow_token') || null,
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
        role: (state) => state.user?.role?.slug ?? null,
    },

    actions: {
        async login(email, password) {
            // Réponse de /login : { user: {...}, token: "..." } (pas d'enveloppe "data").
            const { data } = await api.post('/login', { email, password });

            this.token = data.token;
            this.user = data.user;
            localStorage.setItem('adminflow_token', data.token);
        },

        async logout() {
            try {
                await api.post('/logout');
            } finally {
                this.token = null;
                this.user = null;
                localStorage.removeItem('adminflow_token');
            }
        },

        async fetchUser() {
            // Réponse de /me : { data: {...} } (Resource classique, enveloppée).
            const { data } = await api.get('/me');
            this.user = data.data;
        },
    },
});
