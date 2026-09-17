<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import api from '@/lib/api';
import { useAuthStore } from '@/stores/auth';
import AppLayout from '@/layouts/AppLayout.vue';
import { Paperclip, Download, Trash2, Upload } from '@lucide/vue';

const route = useRoute();
const auth = useAuthStore();

const demande = ref(null);
const chargement = ref(true);
const erreur = ref(null);
const enCours = ref(false);
const televersementEnCours = ref(false);
const commentaireComplement = ref('');
const commentaireDecision = ref('');

async function charger() {
    chargement.value = true;
    const { data } = await api.get(`/demandes/${route.params.id}`);
    demande.value = data.data;
    chargement.value = false;
}

onMounted(charger);

const estAgent = computed(() => auth.role === 'agent');
const estValidateur = computed(() => auth.role === 'validateur');
const estAdmin = computed(() => auth.role === 'administrateur');

function peutSupprimerDocument(doc) {
    return doc.uploaded_by?.id === auth.user?.id || estAdmin.value;
}

async function executer(action) {
    erreur.value = null;
    enCours.value = true;

    try {
        await action();
        commentaireComplement.value = '';
        commentaireDecision.value = '';
        await charger();
    } catch (e) {
        erreur.value = e.response?.data?.message
            ?? Object.values(e.response?.data?.errors ?? {})[0]?.[0]
            ?? 'Une erreur est survenue.';
    } finally {
        enCours.value = false;
    }
}

const affecter = () => executer(() =>
    api.post(`/demandes/${demande.value.id}/affectations`, { agent_id: auth.user.id }),
);

const transmettre = () => executer(() =>
    api.post(`/demandes/${demande.value.id}/transmettre-validation`),
);

const demanderComplement = () => executer(() =>
    api.post(`/demandes/${demande.value.id}/demander-complement`, {
        commentaire: commentaireComplement.value,
    }),
);

function decider(decision) {
    if (decision === 'rejete' && !commentaireDecision.value) {
        erreur.value = 'Un commentaire est requis pour un rejet.';
        return;
    }

    return executer(() =>
        api.post(`/demandes/${demande.value.id}/validations`, {
            decision,
            commentaire: commentaireDecision.value || undefined,
        }),
    );
}

const archiver = () => executer(() =>
    api.post(`/demandes/${demande.value.id}/archiver`),
);

async function ajouterDocument(event) {
    const fichier = event.target.files[0];
    if (!fichier) {
        return;
    }

    erreur.value = null;
    televersementEnCours.value = true;

    try {
        const formData = new FormData();
        formData.append('fichier', fichier);

        await api.post(`/demandes/${demande.value.id}/documents`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });

        await charger();
    } catch (e) {
        erreur.value = e.response?.data?.message
            ?? Object.values(e.response?.data?.errors ?? {})[0]?.[0]
            ?? "Erreur lors de l'ajout du document.";
    } finally {
        televersementEnCours.value = false;
        event.target.value = '';
    }
}

async function telechargerDocument(doc) {
    const response = await api.get(`/documents/${doc.id}/download`, { responseType: 'blob' });
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const lien = window.document.createElement('a');
    lien.href = url;
    lien.download = doc.nom_original;
    lien.click();
    window.URL.revokeObjectURL(url);
}

const supprimerDocument = (doc) => executer(() => api.delete(`/documents/${doc.id}`));
</script>

<template>
    <AppLayout>
        <div v-if="chargement" class="text-slate-500">Chargement…</div>

        <div v-else-if="demande" class="animate-fade-in-up space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h1 class="text-2xl font-bold text-navy">{{ demande.reference }}</h1>
                    <span class="rounded-full bg-accent/10 px-3 py-1 text-sm font-medium text-accent">
                        {{ demande.statut }}
                    </span>
                </div>

                <dl class="mt-6 grid gap-4 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="text-slate-400">Type</dt>
                        <dd class="text-navy">{{ demande.type_demande?.nom }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400">Priorité</dt>
                        <dd class="text-navy">{{ demande.priorite }}</dd>
                    </div>
                    <div v-if="demande.commentaire" class="sm:col-span-2">
                        <dt class="text-slate-400">Commentaire</dt>
                        <dd class="text-navy">{{ demande.commentaire }}</dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
                <h2 class="mb-4 text-lg font-semibold text-navy">Champs</h2>
                <ul class="space-y-2 text-sm text-slate-700">
                    <li v-for="valeur in demande.valeurs" :key="valeur.champ_demande_id">
                        <span class="text-slate-400">{{ valeur.label }} :</span> {{ valeur.valeur }}
                    </li>
                </ul>
            </div>

            <!-- Documents -->
            <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-navy">Documents</h2>

                    <label class="flex cursor-pointer items-center gap-1.5 rounded-lg border border-slate-300 px-3 py-1.5 text-sm font-medium text-navy transition hover:bg-slate-50">
                        <Upload class="h-4 w-4" />
                        {{ televersementEnCours ? 'Envoi…' : 'Ajouter' }}
                        <input type="file" class="hidden" :disabled="televersementEnCours" @change="ajouterDocument" />
                    </label>
                </div>

                <ul class="space-y-2">
                    <li
                        v-for="doc in demande.documents"
                        :key="doc.id"
                        class="flex items-center justify-between rounded-lg border border-slate-100 px-4 py-2.5 text-sm"
                    >
                        <div class="flex min-w-0 items-center gap-2 text-slate-700">
                            <Paperclip class="h-4 w-4 shrink-0 text-slate-400" />
                            <span class="truncate">{{ doc.nom_original }}</span>
                            <span class="shrink-0 text-xs text-slate-400">— {{ doc.uploaded_by?.name }}</span>
                        </div>

                        <div class="flex shrink-0 items-center gap-1">
                            <button
                                :disabled="enCours"
                                @click="telechargerDocument(doc)"
                                class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-accent"
                                title="Télécharger"
                            >
                                <Download class="h-4 w-4" />
                            </button>
                            <button
                                v-if="peutSupprimerDocument(doc)"
                                :disabled="enCours"
                                @click="supprimerDocument(doc)"
                                class="rounded-lg p-1.5 text-slate-400 transition hover:bg-red-50 hover:text-red-500"
                                title="Supprimer"
                            >
                                <Trash2 class="h-4 w-4" />
                            </button>
                        </div>
                    </li>

                    <li v-if="!demande.documents?.length" class="text-sm text-slate-400">
                        Aucun document joint.
                    </li>
                </ul>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
                <h2 class="mb-4 text-lg font-semibold text-navy">Historique</h2>

                <p class="mb-2 text-xs uppercase text-slate-400">Affectations</p>
                <ul class="mb-4 space-y-1 text-sm text-slate-700">
                    <li v-for="affectation in demande.affectations" :key="affectation.id">
                        {{ affectation.agent?.name }} ({{ affectation.statut }})
                    </li>
                    <li v-if="!demande.affectations?.length" class="text-slate-400">Aucune affectation.</li>
                </ul>

                <p class="mb-2 text-xs uppercase text-slate-400">Décisions</p>
                <ul class="space-y-1 text-sm text-slate-700">
                    <li v-for="validation in demande.validations" :key="validation.id">
                        {{ validation.validateur?.name }} : {{ validation.decision }}
                        <span v-if="validation.commentaire" class="text-slate-400"> — {{ validation.commentaire }}</span>
                    </li>
                    <li v-if="!demande.validations?.length" class="text-slate-400">Aucune décision.</li>
                </ul>
            </div>

            <p v-if="erreur" class="rounded-lg border border-red-200 bg-red-50 px-4 py-2.5 text-sm text-red-600">
                {{ erreur }}
            </p>

            <!-- Actions agent : affectation puis transmission -->
            <div v-if="estAgent" class="flex flex-wrap gap-3 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <button
                    v-if="demande.statut === 'soumise'"
                    :disabled="enCours"
                    @click="affecter"
                    class="rounded-lg bg-accent px-4 py-2 font-medium text-white shadow-sm transition hover:bg-accent/90 disabled:opacity-50"
                >
                    M'affecter cette demande
                </button>

                <button
                    v-if="demande.statut === 'en_verification'"
                    :disabled="enCours"
                    @click="transmettre"
                    class="rounded-lg bg-accent px-4 py-2 font-medium text-white shadow-sm transition hover:bg-accent/90 disabled:opacity-50"
                >
                    Transmettre en validation
                </button>
            </div>

            <!-- Demande de complément -->
            <div
                v-if="(estAgent || estValidateur) && ['en_verification', 'en_validation'].includes(demande.statut)"
                class="space-y-3 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
            >
                <label class="block">
                    <span class="mb-1.5 block text-sm font-medium text-slate-700">Commentaire (complément demandé)</span>
                    <textarea
                        v-model="commentaireComplement"
                        rows="2"
                        class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-navy outline-none transition focus:border-accent focus:ring-2 focus:ring-accent/30"
                    ></textarea>
                </label>
                <button
                    :disabled="enCours || !commentaireComplement"
                    @click="demanderComplement"
                    class="rounded-lg border border-slate-300 px-4 py-2 font-medium text-navy transition hover:bg-slate-50 disabled:opacity-50"
                >
                    Demander un complément
                </button>
            </div>

            <!-- Décision du validateur -->
            <div v-if="estValidateur && demande.statut === 'en_validation'" class="space-y-3 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-navy">Décision</h2>
                <label class="block">
                    <span class="mb-1.5 block text-sm font-medium text-slate-700">
                        Commentaire (optionnel pour approuver, obligatoire pour rejeter)
                    </span>
                    <textarea
                        v-model="commentaireDecision"
                        rows="2"
                        class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-navy outline-none transition focus:border-accent focus:ring-2 focus:ring-accent/30"
                    ></textarea>
                </label>
                <div class="flex gap-3">
                    <button
                        :disabled="enCours"
                        @click="decider('approuve')"
                        class="rounded-lg bg-emerald-500 px-4 py-2 font-medium text-white shadow-sm transition hover:bg-emerald-600 disabled:opacity-50"
                    >
                        Approuver
                    </button>
                    <button
                        :disabled="enCours"
                        @click="decider('rejete')"
                        class="rounded-lg bg-red-500 px-4 py-2 font-medium text-white shadow-sm transition hover:bg-red-600 disabled:opacity-50"
                    >
                        Rejeter
                    </button>
                </div>
            </div>

            <!-- Archivage -->
            <div
                v-if="(estValidateur || estAdmin) && ['approuvee', 'rejetee'].includes(demande.statut)"
                class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
            >
                <button
                    :disabled="enCours"
                    @click="archiver"
                    class="rounded-lg border border-slate-300 px-4 py-2 font-medium text-navy transition hover:bg-slate-50 disabled:opacity-50"
                >
                    Archiver
                </button>
            </div>
        </div>
    </AppLayout>
</template>
