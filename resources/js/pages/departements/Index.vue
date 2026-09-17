<script setup>
import { onMounted, ref } from 'vue';
import api from '@/lib/api';
import AppLayout from '@/layouts/AppLayout.vue';
import { Plus, Trash2 } from '@lucide/vue';

const departements = ref([]);
const chargement = ref(true);
const erreur = ref(null);
const enCours = ref(false);
const afficherFormulaire = ref(false);

const nouveauDepartement = ref({ nom: '', code: '', description: '' });

async function charger() {
    chargement.value = true;
    const { data } = await api.get('/departments');
    departements.value = data.data;
    chargement.value = false;
}

onMounted(charger);

async function creerDepartement() {
    erreur.value = null;
    enCours.value = true;

    try {
        await api.post('/departments', nouveauDepartement.value);
        nouveauDepartement.value = { nom: '', code: '', description: '' };
        afficherFormulaire.value = false;
        await charger();
    } catch (e) {
        erreur.value = Object.values(e.response?.data?.errors ?? {})[0]?.[0] ?? 'Erreur lors de la création.';
    } finally {
        enCours.value = false;
    }
}

async function basculerActif(departement) {
    enCours.value = true;
    try {
        await api.put(`/departments/${departement.id}`, { actif: !departement.actif });
        await charger();
    } finally {
        enCours.value = false;
    }
}

async function supprimerDepartement(departement) {
    if (!window.confirm(`Supprimer définitivement "${departement.nom}" ?`)) {
        return;
    }

    enCours.value = true;
    try {
        await api.delete(`/departments/${departement.id}`);
        await charger();
    } catch (e) {
        erreur.value = e.response?.data?.message ?? 'Suppression impossible.';
    } finally {
        enCours.value = false;
    }
}
</script>

<template>
    <AppLayout>
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-navy">Départements</h1>
            <button
                @click="afficherFormulaire = !afficherFormulaire"
                class="flex items-center gap-1.5 rounded-lg bg-accent px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-accent/90"
            >
                <Plus class="h-4 w-4" />
                Nouveau département
            </button>
        </div>

        <form
            v-if="afficherFormulaire"
            @submit.prevent="creerDepartement"
            class="mb-6 grid gap-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:grid-cols-3"
        >
            <label class="block">
                <span class="mb-1.5 block text-sm font-medium text-slate-700">Nom</span>
                <input
                    v-model="nouveauDepartement.nom"
                    required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-navy outline-none focus:border-accent focus:ring-2 focus:ring-accent/30"
                />
            </label>
            <label class="block">
                <span class="mb-1.5 block text-sm font-medium text-slate-700">Code</span>
                <input
                    v-model="nouveauDepartement.code"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-navy outline-none focus:border-accent focus:ring-2 focus:ring-accent/30"
                />
            </label>
            <label class="block">
                <span class="mb-1.5 block text-sm font-medium text-slate-700">Description</span>
                <input
                    v-model="nouveauDepartement.description"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-navy outline-none focus:border-accent focus:ring-2 focus:ring-accent/30"
                />
            </label>
            <div class="sm:col-span-3">
                <button
                    type="submit"
                    :disabled="enCours"
                    class="rounded-lg bg-accent px-4 py-2 text-sm font-medium text-white transition hover:bg-accent/90 disabled:opacity-50"
                >
                    Créer
                </button>
            </div>
        </form>

        <p v-if="erreur" class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-2.5 text-sm text-red-600">
            {{ erreur }}
        </p>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <p v-if="chargement" class="p-6 text-slate-500">Chargement…</p>

            <table v-else class="w-full text-left text-sm text-slate-700">
                <thead class="border-b border-slate-200 text-xs uppercase text-slate-400">
                    <tr>
                        <th class="px-6 py-3">Nom</th>
                        <th class="px-6 py-3">Code</th>
                        <th class="px-6 py-3">Statut</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="departement in departements" :key="departement.id" class="border-b border-slate-100">
                        <td class="px-6 py-4 font-medium text-navy">{{ departement.nom }}</td>
                        <td class="px-6 py-4">{{ departement.code }}</td>
                        <td class="px-6 py-4">
                            <button
                                @click="basculerActif(departement)"
                                class="rounded-full px-2.5 py-1 text-xs font-medium transition"
                                :class="departement.actif ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500'"
                            >
                                {{ departement.actif ? 'Actif' : 'Inactif' }}
                            </button>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button
                                :disabled="enCours"
                                @click="supprimerDepartement(departement)"
                                class="rounded-lg p-1.5 text-slate-400 transition hover:bg-red-50 hover:text-red-500"
                                title="Supprimer"
                            >
                                <Trash2 class="h-4 w-4" />
                            </button>
                        </td>
                    </tr>

                    <tr v-if="!departements.length">
                        <td colspan="4" class="px-6 py-8 text-center text-slate-400">Aucun département.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
