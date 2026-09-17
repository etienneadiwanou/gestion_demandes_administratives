<script setup>
import { onMounted, ref } from 'vue';
import { RouterLink } from 'vue-router';
import api from '@/lib/api';
import { useAuthStore } from '@/stores/auth';
import AppLayout from '@/layouts/AppLayout.vue';

const auth = useAuthStore();

const demandesAAffecter = ref([]);
const demandesEnCours = ref([]);
const chargement = ref(true);
const enCours = ref(false);
const erreur = ref(null);

const estAgent = auth.role === 'agent';

async function charger() {
    chargement.value = true;

    const [soumises, verification] = await Promise.all([
        api.get('/demandes', { params: { statut: 'soumise' } }),
        api.get('/demandes', { params: { statut: 'en_verification' } }),
    ]);

    demandesAAffecter.value = soumises.data.data;
    demandesEnCours.value = verification.data.data.filter((demande) =>
        demande.affectations?.some((a) => a.statut === 'active' && a.agent?.id === auth.user?.id),
    );

    chargement.value = false;
}

onMounted(charger);

async function affecter(demande) {
    erreur.value = null;
    enCours.value = true;

    try {
        await api.post(`/demandes/${demande.id}/affectations`, { agent_id: auth.user.id });
        await charger();
    } catch (e) {
        erreur.value = e.response?.data?.message ?? "Erreur lors de l'affectation.";
    } finally {
        enCours.value = false;
    }
}

async function transmettre(demande) {
    erreur.value = null;
    enCours.value = true;

    try {
        await api.post(`/demandes/${demande.id}/transmettre-validation`);
        await charger();
    } catch (e) {
        erreur.value = e.response?.data?.message ?? 'Erreur lors de la transmission.';
    } finally {
        enCours.value = false;
    }
}
</script>

<template>
    <AppLayout>
        <h1 class="mb-6 text-2xl font-bold text-navy">Affectations</h1>

        <p v-if="erreur" class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-2.5 text-sm text-red-600">
            {{ erreur }}
        </p>

        <div class="space-y-8">
            <section>
                <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-slate-500">
                    À affecter ({{ demandesAAffecter.length }})
                </h2>

                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <p v-if="chargement" class="p-6 text-slate-500">Chargement…</p>

                    <ul v-else class="divide-y divide-slate-100">
                        <li v-for="demande in demandesAAffecter" :key="demande.id" class="flex items-center justify-between px-6 py-4">
                            <div>
                                <RouterLink
                                    :to="{ name: 'demandes.show', params: { id: demande.id } }"
                                    class="font-medium text-navy hover:text-accent"
                                >
                                    {{ demande.reference }}
                                </RouterLink>
                                <p class="text-sm text-slate-400">
                                    {{ demande.type_demande?.nom }} — {{ demande.demandeur?.name }}
                                </p>
                            </div>
                            <button
                                v-if="estAgent"
                                :disabled="enCours"
                                @click="affecter(demande)"
                                class="rounded-lg bg-accent px-3 py-1.5 text-sm font-medium text-white transition hover:bg-accent/90 disabled:opacity-50"
                            >
                                M'affecter
                            </button>
                        </li>

                        <li v-if="!demandesAAffecter.length" class="px-6 py-8 text-center text-sm text-slate-400">
                            Aucune demande à affecter.
                        </li>
                    </ul>
                </div>
            </section>

            <section v-if="estAgent">
                <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-slate-500">
                    Mes demandes en cours ({{ demandesEnCours.length }})
                </h2>

                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <p v-if="chargement" class="p-6 text-slate-500">Chargement…</p>

                    <ul v-else class="divide-y divide-slate-100">
                        <li v-for="demande in demandesEnCours" :key="demande.id" class="flex items-center justify-between px-6 py-4">
                            <div>
                                <RouterLink
                                    :to="{ name: 'demandes.show', params: { id: demande.id } }"
                                    class="font-medium text-navy hover:text-accent"
                                >
                                    {{ demande.reference }}
                                </RouterLink>
                                <p class="text-sm text-slate-400">{{ demande.type_demande?.nom }}</p>
                            </div>
                            <button
                                :disabled="enCours"
                                @click="transmettre(demande)"
                                class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm font-medium text-navy transition hover:bg-slate-50 disabled:opacity-50"
                            >
                                Transmettre en validation
                            </button>
                        </li>

                        <li v-if="!demandesEnCours.length" class="px-6 py-8 text-center text-sm text-slate-400">
                            Aucune demande en cours.
                        </li>
                    </ul>
                </div>
            </section>
        </div>
    </AppLayout>
</template>
