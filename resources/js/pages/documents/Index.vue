<script setup>
import { onMounted, ref } from 'vue';
import { RouterLink } from 'vue-router';
import api from '@/lib/api';
import { useAuthStore } from '@/stores/auth';
import AppLayout from '@/layouts/AppLayout.vue';
import { Paperclip, Download, Trash2 } from '@lucide/vue';

const auth = useAuthStore();

const documents = ref([]);
const chargement = ref(true);
const enCours = ref(false);
const erreur = ref(null);

async function charger() {
    chargement.value = true;
    const { data } = await api.get('/documents');
    documents.value = data.data;
    chargement.value = false;
}

onMounted(charger);

function peutSupprimer(doc) {
    return doc.uploaded_by?.id === auth.user?.id || auth.role === 'administrateur';
}

async function telecharger(doc) {
    const response = await api.get(`/documents/${doc.id}/download`, { responseType: 'blob' });
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const lien = window.document.createElement('a');
    lien.href = url;
    lien.download = doc.nom_original;
    lien.click();
    window.URL.revokeObjectURL(url);
}

async function supprimer(doc) {
    enCours.value = true;
    erreur.value = null;

    try {
        await api.delete(`/documents/${doc.id}`);
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
        <h1 class="mb-6 text-2xl font-bold text-navy">Documents</h1>

        <p v-if="erreur" class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-2.5 text-sm text-red-600">
            {{ erreur }}
        </p>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <p v-if="chargement" class="p-6 text-slate-500">Chargement…</p>

            <table v-else class="w-full text-left text-sm text-slate-700">
                <thead class="border-b border-slate-200 text-xs uppercase text-slate-400">
                    <tr>
                        <th class="px-6 py-3">Document</th>
                        <th class="px-6 py-3">Demande</th>
                        <th class="px-6 py-3">Ajouté par</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="doc in documents" :key="doc.id" class="border-b border-slate-100">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2 text-navy">
                                <Paperclip class="h-4 w-4 shrink-0 text-slate-400" />
                                {{ doc.nom_original }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <RouterLink
                                v-if="doc.demande"
                                :to="{ name: 'demandes.show', params: { id: doc.demande.id } }"
                                class="text-accent hover:underline"
                            >
                                {{ doc.demande.reference }}
                            </RouterLink>
                        </td>
                        <td class="px-6 py-4">{{ doc.uploaded_by?.name }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-1">
                                <button
                                    :disabled="enCours"
                                    @click="telecharger(doc)"
                                    class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-accent"
                                    title="Télécharger"
                                >
                                    <Download class="h-4 w-4" />
                                </button>
                                <button
                                    v-if="peutSupprimer(doc)"
                                    :disabled="enCours"
                                    @click="supprimer(doc)"
                                    class="rounded-lg p-1.5 text-slate-400 transition hover:bg-red-50 hover:text-red-500"
                                    title="Supprimer"
                                >
                                    <Trash2 class="h-4 w-4" />
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr v-if="!documents.length">
                        <td colspan="4" class="px-6 py-8 text-center text-slate-400">Aucun document.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
