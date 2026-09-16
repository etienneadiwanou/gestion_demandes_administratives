<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const email = ref('');
const password = ref('');
const erreur = ref(null);
const chargement = ref(false);

const auth = useAuthStore();
const router = useRouter();

async function seConnecter() {
    erreur.value = null;
    chargement.value = true;

    try {
        await auth.login(email.value, password.value);
        router.push({ name: 'demandes.index' });
    } catch (e) {
        erreur.value = e.response?.data?.message ?? 'Identifiants invalides.';
    } finally {
        chargement.value = false;
    }
}
</script>

<template>
    <div class="login">
        <h1>Connexion</h1>

        <form @submit.prevent="seConnecter">
            <label>
                Email
                <input v-model="email" type="email" required />
            </label>

            <label>
                Mot de passe
                <input v-model="password" type="password" required />
            </label>

            <p v-if="erreur" class="erreur">{{ erreur }}</p>

            <button type="submit" :disabled="chargement">
                {{ chargement ? 'Connexion…' : 'Se connecter' }}
            </button>
        </form>
    </div>
</template>
