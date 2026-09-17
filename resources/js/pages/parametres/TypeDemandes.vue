<script setup>
import { computed, onMounted, ref } from 'vue';
import api from '@/lib/api';
import AppLayout from '@/layouts/AppLayout.vue';
import { Plus, Trash2, ChevronRight } from '@lucide/vue';

const types = ref([]);
const chargement = ref(true);
const typeSelectionneId = ref(null);
const erreur = ref(null);
const enCours = ref(false);

const nouveauType = ref({ nom: '', code: '', categorie: '', description: '' });
const afficherFormulaireType = ref(false);

const nouveauChamp = ref({
    label: '',
    nom_technique: '',
    type_champ: 'texte',
    obligatoire: false,
    options: '',
});

const typesChamp = [
    { value: 'texte', label: 'Texte court' },
    { value: 'textarea', label: 'Texte long' },
    { value: 'nombre', label: 'Nombre' },
    { value: 'date', label: 'Date' },
    { value: 'heure', label: 'Heure' },
    { value: 'liste', label: 'Liste déroulante' },
    { value: 'booleen', label: 'Case à cocher' },
    { value: 'fichier', label: 'Fichier' },
];

const typeSelectionne = computed(() =>
    types.value.find((type) => type.id === typeSelectionneId.value) ?? null,
);

function slugifier(texte) {
    return (texte ?? '')
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/(^-|-$)/g, '');
}

function remplirCodeDepuisNom() {
    if (!nouveauType.value.code) {
        nouveauType.value.code = slugifier(nouveauType.value.nom);
    }
}

function remplirNomTechniqueDepuisLabel() {
    if (!nouveauChamp.value.nom_technique) {
        nouveauChamp.value.nom_technique = slugifier(nouveauChamp.value.label).replace(/-/g, '_');
    }
}

async function charger() {
    chargement.value = true;
    const { data } = await api.get('/type-demandes');
    types.value = data.data;
    chargement.value = false;
}

onMounted(charger);

async function creerType() {
    erreur.value = null;
    enCours.value = true;

    try {
        const { data } = await api.post('/type-demandes', nouveauType.value);
        nouveauType.value = { nom: '', code: '', categorie: '', description: '' };
        afficherFormulaireType.value = false;
        await charger();
        typeSelectionneId.value = data.data.id;
    } catch (e) {
        erreur.value = Object.values(e.response?.data?.errors ?? {})[0]?.[0] ?? 'Erreur lors de la création.';
    } finally {
        enCours.value = false;
    }
}

async function basculerActifType(type) {
    await api.put(`/type-demandes/${type.id}`, { actif: !type.actif });
    await charger();
}

async function creerChamp() {
    erreur.value = null;
    enCours.value = true;

    try {
        const payload = {
            type_demande_id: typeSelectionne.value.id,
            label: nouveauChamp.value.label,
            nom_technique: nouveauChamp.value.nom_technique,
            type_champ: nouveauChamp.value.type_champ,
            obligatoire: nouveauChamp.value.obligatoire,
            options: nouveauChamp.value.type_champ === 'liste'
                ? nouveauChamp.value.options.split(',').map((o) => o.trim()).filter(Boolean)
                : null,
            ordre: (typeSelectionne.value.champs?.length ?? 0) + 1,
        };

        await api.post('/champ-demandes', payload);
        nouveauChamp.value = { label: '', nom_technique: '', type_champ: 'texte', obligatoire: false, options: '' };
        await charger();
    } catch (e) {
        erreur.value = Object.values(e.response?.data?.errors ?? {})[0]?.[0] ?? "Erreur lors de l'ajout du champ.";
    } finally {
        enCours.value = false;
    }
}

async function supprimerChamp(champ) {
    enCours.value = true;
    try {
        await api.delete(`/champ-demandes/${champ.id}`);
        await charger();
    } finally {
        enCours.value = false;
    }
}
</script>

<template>
    <AppLayout>
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-navy">Types de demandes</h1>
            <button
                @click="afficherFormulaireType = !afficherFormulaireType"
                class="flex items-center gap-1.5 rounded-lg bg-accent px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-accent/90"
            >
                <Plus class="h-4 w-4" />
                Nouveau type
            </button>
        </div>

        <!-- Formulaire de création d'un type -->
        <form
            v-if="afficherFormulaireType"
            @submit.prevent="creerType"
            class="mb-6 grid gap-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:grid-cols-2"
        >
            <label class="block">
                <span class="mb-1.5 block text-sm font-medium text-slate-700">Nom</span>
                <input
                    v-model="nouveauType.nom"
                    required
                    @blur="remplirCodeDepuisNom"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-navy outline-none focus:border-accent focus:ring-2 focus:ring-accent/30"
                />
            </label>
            <label class="block">
                <span class="mb-1.5 block text-sm font-medium text-slate-700">Code</span>
                <input
                    v-model="nouveauType.code"
                    required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-navy outline-none focus:border-accent focus:ring-2 focus:ring-accent/30"
                />
            </label>
            <label class="block">
                <span class="mb-1.5 block text-sm font-medium text-slate-700">Catégorie</span>
                <input
                    v-model="nouveauType.categorie"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-navy outline-none focus:border-accent focus:ring-2 focus:ring-accent/30"
                />
            </label>
            <label class="block">
                <span class="mb-1.5 block text-sm font-medium text-slate-700">Description</span>
                <input
                    v-model="nouveauType.description"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-navy outline-none focus:border-accent focus:ring-2 focus:ring-accent/30"
                />
            </label>
            <div class="sm:col-span-2">
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

        <div class="grid gap-6 lg:grid-cols-[minmax(0,320px)_1fr]">
            <!-- Liste des types -->
            <div class="space-y-2">
                <p v-if="chargement" class="text-sm text-slate-400">Chargement…</p>

                <button
                    v-for="type in types"
                    :key="type.id"
                    @click="typeSelectionneId = type.id"
                    class="flex w-full items-center justify-between rounded-xl border px-4 py-3 text-left text-sm transition"
                    :class="typeSelectionneId === type.id
                        ? 'border-accent bg-accent/5 text-navy'
                        : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'"
                >
                    <span>
                        <span class="font-medium">{{ type.nom }}</span>
                        <span v-if="!type.actif" class="ml-2 rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-400">inactif</span>
                    </span>
                    <ChevronRight class="h-4 w-4 shrink-0 text-slate-400" />
                </button>
            </div>

            <!-- Détail du type sélectionné -->
            <div v-if="typeSelectionne" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-navy">{{ typeSelectionne.nom }}</h2>
                        <p class="text-sm text-slate-400">{{ typeSelectionne.categorie || 'Sans catégorie' }}</p>
                    </div>
                    <button
                        @click="basculerActifType(typeSelectionne)"
                        class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm text-navy transition hover:bg-slate-50"
                    >
                        {{ typeSelectionne.actif ? 'Désactiver' : 'Activer' }}
                    </button>
                </div>

                <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-slate-500">Champs du formulaire</h3>

                <ul class="mb-6 space-y-2">
                    <li
                        v-for="champ in typeSelectionne.champs"
                        :key="champ.id"
                        class="flex items-center justify-between rounded-lg border border-slate-100 px-4 py-2.5 text-sm"
                    >
                        <div>
                            <span class="font-medium text-navy">{{ champ.label }}</span>
                            <span class="ml-2 text-xs text-slate-400">
                                {{ typesChamp.find((t) => t.value === champ.type_champ)?.label }}
                                <span v-if="champ.obligatoire" class="text-accent">· obligatoire</span>
                            </span>
                        </div>
                        <button
                            :disabled="enCours"
                            @click="supprimerChamp(champ)"
                            class="rounded-lg p-1.5 text-slate-400 transition hover:bg-red-50 hover:text-red-500"
                            title="Supprimer ce champ"
                        >
                            <Trash2 class="h-4 w-4" />
                        </button>
                    </li>

                    <li v-if="!typeSelectionne.champs?.length" class="text-sm text-slate-400">
                        Aucun champ pour ce type.
                    </li>
                </ul>

                <!-- Ajout d'un champ -->
                <form @submit.prevent="creerChamp" class="grid gap-4 rounded-xl bg-slate-50 p-4 sm:grid-cols-2">
                    <label class="block">
                        <span class="mb-1.5 block text-xs font-medium text-slate-600">Label</span>
                        <input
                            v-model="nouveauChamp.label"
                            required
                            @blur="remplirNomTechniqueDepuisLabel"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-navy outline-none focus:border-accent focus:ring-2 focus:ring-accent/30"
                        />
                    </label>
                    <label class="block">
                        <span class="mb-1.5 block text-xs font-medium text-slate-600">Nom technique</span>
                        <input
                            v-model="nouveauChamp.nom_technique"
                            required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-navy outline-none focus:border-accent focus:ring-2 focus:ring-accent/30"
                        />
                    </label>
                    <label class="block">
                        <span class="mb-1.5 block text-xs font-medium text-slate-600">Type</span>
                        <select
                            v-model="nouveauChamp.type_champ"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-navy outline-none focus:border-accent focus:ring-2 focus:ring-accent/30"
                        >
                            <option v-for="t in typesChamp" :key="t.value" :value="t.value">{{ t.label }}</option>
                        </select>
                    </label>
                    <label v-if="nouveauChamp.type_champ === 'liste'" class="block">
                        <span class="mb-1.5 block text-xs font-medium text-slate-600">Options (séparées par des virgules)</span>
                        <input
                            v-model="nouveauChamp.options"
                            placeholder="Option A, Option B, Option C"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-navy outline-none focus:border-accent focus:ring-2 focus:ring-accent/30"
                        />
                    </label>
                    <label class="flex items-center gap-2 sm:col-span-2">
                        <input v-model="nouveauChamp.obligatoire" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-accent" />
                        <span class="text-sm text-slate-600">Champ obligatoire</span>
                    </label>
                    <div class="sm:col-span-2">
                        <button
                            type="submit"
                            :disabled="enCours"
                            class="rounded-lg bg-accent px-4 py-2 text-sm font-medium text-white transition hover:bg-accent/90 disabled:opacity-50"
                        >
                            Ajouter le champ
                        </button>
                    </div>
                </form>
            </div>

            <div v-else-if="!chargement" class="flex items-center justify-center rounded-2xl border border-dashed border-slate-200 p-10 text-sm text-slate-400">
                Sélectionne un type de demande pour gérer ses champs.
            </div>
        </div>
    </AppLayout>
</template>
