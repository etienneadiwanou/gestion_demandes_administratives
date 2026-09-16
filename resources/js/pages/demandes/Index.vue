<script setup>
import { onMounted, ref } from 'vue';
import { RouterLink } from 'vue-router';
import api from '@/lib/api';

const demandes = ref([]);
const chargement = ref(true);

onMounted(async () => {
    const { data } = await api.get('/demandes');
    demandes.value = data.data;
    chargement.value = false;
});
</script>

<template>
    <div>
        <header>
            <h1>Mes demandes</h1>
            <RouterLink :to="{ name: 'demandes.create' }">Nouvelle demande</RouterLink>
        </header>

        <p v-if="chargement">Chargement…</p>

        <table v-else>
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Type</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="demande in demandes" :key="demande.id">
                    <td>{{ demande.reference }}</td>
                    <td>{{ demande.type_demande?.nom }}</td>
                    <td>{{ demande.statut }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
