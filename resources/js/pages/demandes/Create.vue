<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/lib/api';

const router = useRouter();
const types = ref([]);
const typeDemandeId = ref('');
const valeurs = ref({});
const erreurs = ref({});
const enregistrement = ref(false);

const typeSelectionne = computed(() =>
    types.value.find((type) => type.id === typeDemandeId.value) ?? null,
);

onMounted(async () => {
    const { data } = await api.get('/type-demandes');
    types.value = data.data;
});

async function creerEtSoumettre() {
    erreurs.value = {};
    enregistrement.value = true;

    try {
        const payload = {
            type_demande_id: typeDemandeId.value,
            valeurs: Object.entries(valeurs.value).map(([champDemandeId, valeur]) => ({
                champ_demande_id: Number(champDemandeId),
                valeur,
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
    <div>
        <h1>Nouvelle demande</h1>

        <form @submit.prevent="creerEtSoumettre">
            <label>
                Type de demande
                <select v-model="typeDemandeId" required>
                    <option disabled value="">Choisir…</option>
                    <option v-for="type in types" :key="type.id" :value="type.id">
                        {{ type.nom }}
                    </option>
                </select>
            </label>

            <div v-if="typeSelectionne">
                <label v-for="champ in typeSelectionne.champs" :key="champ.id">
                    {{ champ.label }} <span v-if="champ.obligatoire">*</span>
                    <input v-model="valeurs[champ.id]" :required="champ.obligatoire" />
                </label>
            </div>

            <p v-if="erreurs.valeurs" class="erreur">{{ erreurs.valeurs[0] }}</p>

            <button type="submit" :disabled="enregistrement">
                {{ enregistrement ? 'Envoi…' : 'Soumettre' }}
            </button>
        </form>
    </div>
</template>
