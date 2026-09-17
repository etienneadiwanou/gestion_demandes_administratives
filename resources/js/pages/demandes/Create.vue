<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/lib/api';
import AppLayout from '@/layouts/AppLayout.vue';

const router = useRouter();
const types = ref([]);
const typeDemandeId = ref('');
const valeurs = ref({});
const erreurs = ref({});
const enregistrement = ref(false);

const typeSelectionne = computed(() =>
    types.value.find((type) => type.id === typeDemandeId.value) ?? null,
);

// Correspondance type_champ (métier) -> type d'input HTML natif.
const typeInputHtml = {
    texte: 'text',
    nombre: 'number',
    date: 'date',
    heure: 'time',
};

onMounted(async () => {
    const { data } = await api.get('/type-demandes');
    types.value = data.data;
});

function formaterValeur(champ, valeur) {
    if (champ.type_champ === 'booleen') {
        return valeur ? '1' : '0';
    }

    return valeur ?? '';
}

async function creerEtSoumettre() {
    erreurs.value = {};
    enregistrement.value = true;

    try {
        const payload = {
            type_demande_id: typeDemandeId.value,
            valeurs: (typeSelectionne.value?.champs ?? []).map((champ) => ({
                champ_demande_id: champ.id,
                valeur: formaterValeur(champ, valeurs.value[champ.id]),
            })),
        };

        const { data } = await api.post('/demandes', payload);
        await api.post(`/demandes/${data.data.id}/submit`);

        router.push({ name: 'demandes.index' });
    } catch (e) {
        erreurs.value = e.response?.data?.errors ?? {};
    } finally {
        enregistrement.value = false;
    }
}
</script>

<template>
    <AppLayout>
        <h1 class="mb-6 text-2xl font-bold text-navy">Nouvelle demande</h1>

        <div class="animate-fade-in-up max-w-xl rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
            <form class="space-y-5" @submit.prevent="creerEtSoumettre">
                <label class="block">
                    <span class="mb-1.5 block text-sm font-medium text-slate-700">Type de demande</span>
                    <select
                        v-model="typeDemandeId"
                        required
                        class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-navy outline-none transition focus:border-accent focus:ring-2 focus:ring-accent/30"
                    >
                        <option disabled value="">Choisir…</option>
                        <option v-for="type in types" :key="type.id" :value="type.id">
                            {{ type.nom }}
                        </option>
                    </select>
                </label>

                <div v-if="typeSelectionne" class="space-y-5">
                    <label v-for="champ in typeSelectionne.champs" :key="champ.id" class="block">
                        <span class="mb-1.5 block text-sm font-medium text-slate-700">
                            {{ champ.label }} <span v-if="champ.obligatoire" class="text-accent">*</span>
                        </span>

                        <!-- Liste déroulante -->
                        <select
                            v-if="champ.type_champ === 'liste'"
                            v-model="valeurs[champ.id]"
                            :required="champ.obligatoire"
                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-navy outline-none transition focus:border-accent focus:ring-2 focus:ring-accent/30"
                        >
                            <option disabled value="">Choisir…</option>
                            <option v-for="option in champ.options ?? []" :key="option" :value="option">
                                {{ option }}
                            </option>
                        </select>

                        <!-- Texte long -->
                        <textarea
                            v-else-if="champ.type_champ === 'textarea'"
                            v-model="valeurs[champ.id]"
                            :required="champ.obligatoire"
                            rows="3"
                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-navy outline-none transition focus:border-accent focus:ring-2 focus:ring-accent/30"
                        ></textarea>

                        <!-- Case à cocher -->
                        <label v-else-if="champ.type_champ === 'booleen'" class="flex items-center gap-2">
                            <input
                                v-model="valeurs[champ.id]"
                                type="checkbox"
                                class="h-4 w-4 rounded border-slate-300 text-accent focus:ring-accent/30"
                            />
                            <span class="text-sm text-slate-600">Oui</span>
                        </label>

                        <!-- Fichier : à uploader une fois la demande créée, via l'onglet Documents -->
                        <p v-else-if="champ.type_champ === 'fichier'" class="text-sm text-slate-400">
                            À joindre depuis l'onglet Documents une fois la demande créée.
                        </p>

                        <!-- Texte, nombre, date, heure -->
                        <input
                            v-else
                            v-model="valeurs[champ.id]"
                            :type="typeInputHtml[champ.type_champ] ?? 'text'"
                            :required="champ.obligatoire"
                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-navy outline-none transition focus:border-accent focus:ring-2 focus:ring-accent/30"
                        />
                    </label>
                </div>

                <p v-if="erreurs.valeurs" class="rounded-lg border border-red-200 bg-red-50 px-4 py-2.5 text-sm text-red-600">
                    {{ erreurs.valeurs[0] }}
                </p>

                <button
                    type="submit"
                    :disabled="enregistrement"
                    class="w-full transform rounded-lg bg-accent py-3 font-semibold text-white shadow-sm transition hover:scale-[1.02] hover:bg-accent/90 active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:scale-100"
                >
                    {{ enregistrement ? 'Envoi…' : 'Soumettre' }}
                </button>
            </form>
        </div>
    </AppLayout>
</template>
