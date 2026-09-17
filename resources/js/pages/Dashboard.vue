<script setup>
import { onMounted, ref } from 'vue';
import api from '@/lib/api';
import { useAuthStore } from '@/stores/auth';
import AppLayout from '@/layouts/AppLayout.vue';
import { FileText, Clock, CheckCircle2 } from '@lucide/vue';

const auth = useAuthStore();
const stats = ref({ total: 0, en_cours: 0, validees: 0 });
const chargement = ref(true);

onMounted(async () => {
    const { data } = await api.get('/dashboard/stats');
    stats.value = data.data;
    chargement.value = false;
});
</script>

<template>
    <AppLayout>
        <h1 class="mb-1 text-2xl font-bold text-navy">
            Bonjour {{ auth.user?.name?.split(' ')[0] }} 👋
        </h1>
        <p class="mb-8 text-slate-500">Voici un aperçu de votre activité.</p>

        <div class="grid gap-6 sm:grid-cols-3">
            <div class="flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-accent/10 text-accent">
                    <FileText class="h-5 w-5" />
                </span>
                <div>
                    <p class="text-2xl font-bold text-navy">{{ chargement ? '…' : stats.total }}</p>
                    <p class="text-sm text-slate-500">Demandes</p>
                </div>
            </div>

            <div class="flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                    <Clock class="h-5 w-5" />
                </span>
                <div>
                    <p class="text-2xl font-bold text-navy">{{ chargement ? '…' : stats.en_cours }}</p>
                    <p class="text-sm text-slate-500">En cours</p>
                </div>
            </div>

            <div class="flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                    <CheckCircle2 class="h-5 w-5" />
                </span>
                <div>
                    <p class="text-2xl font-bold text-navy">{{ chargement ? '…' : stats.validees }}</p>
                    <p class="text-sm text-slate-500">Validées</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
