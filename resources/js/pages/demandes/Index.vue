<script setup>
import { onMounted, ref } from 'vue';
import { RouterLink } from 'vue-router';
import api from '@/lib/api';
import AppLayout from '@/layouts/AppLayout.vue';

const demandes = ref([]);
const chargement = ref(true);

onMounted(async () => {
    const { data } = await api.get('/demandes');
    demandes.value = data.data;
    chargement.value = false;
});

const statutStyles = {
    brouillon: 'bg-slate-100 text-slate-600',
    soumise: 'bg-accent/10 text-accent',
    en_verification: 'bg-amber-100 text-amber-700',
    complement_demande: 'bg-orange-100 text-orange-700',
    en_validation: 'bg-accent/10 text-accent',
    approuvee: 'bg-emerald-100 text-emerald-700',
    rejetee: 'bg-red-100 text-red-700',
    archivee: 'bg-slate-100 text-slate-400',
};
</script>

<template>
    <AppLayout>
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-navy">Mes demandes</h1>
            <RouterLink
                :to="{ name: 'demandes.create' }"
                class="rounded-lg bg-accent px-4 py-2 font-medium text-white shadow-sm transition hover:bg-accent/90"
            >
                + Nouvelle demande
            </RouterLink>
        </div>

        <div class="animate-fade-in-up overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <p v-if="chargement" class="p-6 text-slate-500">Chargement…</p>

            <table v-else class="w-full text-left text-sm text-slate-700">
                <thead class="border-b border-slate-200 text-xs uppercase text-slate-400">
                    <tr>
                        <th class="px-6 py-3">Référence</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="demande in demandes"
                        :key="demande.id"
                        class="border-b border-slate-100 transition hover:bg-slate-50"
                    >
                        <td class="px-6 py-4">
                            <RouterLink
                                :to="{ name: 'demandes.show', params: { id: demande.id } }"
                                class="font-medium text-navy hover:text-accent"
                            >
                                {{ demande.reference }}
                            </RouterLink>
                        </td>
                        <td class="px-6 py-4">{{ demande.type_demande?.nom }}</td>
                        <td class="px-6 py-4">
                            <span
                                class="rounded-full px-2.5 py-1 text-xs font-medium"
                                :class="statutStyles[demande.statut] ?? 'bg-slate-100 text-slate-600'"
                            >
                                {{ demande.statut }}
                            </span>
                        </td>
                    </tr>

                    <tr v-if="!demandes.length">
                        <td colspan="3" class="px-6 py-8 text-center text-slate-400">
                            Aucune demande pour le moment.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
