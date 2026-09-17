<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { Mail, Lock, Eye, EyeOff, User, Check } from '@lucide/vue';

const email = ref('');
const password = ref('');
const afficherMotDePasse = ref(false);
const erreur = ref(null);
const chargement = ref(false);

const auth = useAuthStore();
const router = useRouter();

async function seConnecter() {
    erreur.value = null;
    chargement.value = true;

    try {
        await auth.login(email.value, password.value);
        router.push({ name: 'dashboard' });
    } catch (e) {
        erreur.value = e.response?.data?.message
            ?? Object.values(e.response?.data?.errors ?? {})[0]?.[0]
            ?? 'Identifiants invalides.';
    } finally {
        chargement.value = false;
    }
}
</script>

<template>
    <div class="grid min-h-screen lg:grid-cols-2">
        <!-- Bloc identité -->
        <div class="relative hidden flex-col items-center justify-center overflow-hidden bg-navy px-12 text-center lg:flex">
            <div class="absolute -left-24 -top-24 h-80 w-80 rounded-full border border-white/10"></div>
            <div class="absolute -bottom-32 -right-16 h-96 w-96 rounded-full border border-white/10"></div>
            <div class="absolute left-10 top-1/3 h-56 w-56 rounded-full border border-white/5"></div>

            <div class="relative z-10 flex flex-col items-center">
                <div class="mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-white/10">
                    <User class="h-7 w-7 text-white" />
                </div>

                <h2 class="text-2xl font-bold leading-snug text-white">
                    Espace d'administration<br />AdminFlow
                </h2>
                <p class="mt-3 max-w-xs text-sm text-white/70">
                    Connectez-vous pour gérer les demandes et le workflow administratif.
                </p>

                <div class="mt-10 flex flex-wrap justify-center gap-x-6 gap-y-2 text-xs text-white/70">
                    <span class="flex items-center gap-1.5">
                        <Check class="h-3.5 w-3.5 rounded-full bg-white/10 p-0.5" />
                        Accès sécurisé
                    </span>
                    <span class="flex items-center gap-1.5">
                        <Check class="h-3.5 w-3.5 rounded-full bg-white/10 p-0.5" />
                        Données protégées
                    </span>
                    <span class="flex items-center gap-1.5">
                        <Check class="h-3.5 w-3.5 rounded-full bg-white/10 p-0.5" />
                        Historique tracé
                    </span>
                </div>
            </div>
        </div>

        <!-- Bloc formulaire -->
        <div class="flex items-center justify-center bg-slate-100 p-6">
            <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-xl sm:p-10">
                <div class="mb-8 text-center">
                    <h1 class="flex items-center justify-center gap-2 text-2xl font-bold text-navy">
                        Connexion <User class="h-5 w-5" />
                    </h1>
                    <p class="mt-2 text-sm text-slate-500">
                        Bienvenue ! Veuillez vous connecter pour continuer.
                    </p>
                </div>

                <form class="space-y-5" @submit.prevent="seConnecter">
                    <label class="block">
                        <span class="mb-1.5 block text-sm font-semibold text-slate-700">Adresse email</span>
                        <div class="flex items-center gap-2 rounded-xl bg-slate-100 px-4 py-3 focus-within:ring-2 focus-within:ring-accent">
                            <Mail class="h-4 w-4 shrink-0 text-slate-400" />
                            <input
                                v-model="email"
                                type="email"
                                required
                                placeholder="vous@exemple.com"
                                class="w-full bg-transparent text-sm text-navy outline-none placeholder-slate-400"
                            />
                        </div>
                    </label>

                    <label class="block">
                        <span class="mb-1.5 block text-sm font-semibold text-slate-700">Mot de passe</span>
                        <div class="flex items-center gap-2 rounded-xl bg-slate-100 px-4 py-3 focus-within:ring-2 focus-within:ring-accent">
                            <Lock class="h-4 w-4 shrink-0 text-slate-400" />
                            <input
                                v-model="password"
                                :type="afficherMotDePasse ? 'text' : 'password'"
                                required
                                placeholder="••••••••"
                                class="w-full bg-transparent text-sm text-navy outline-none placeholder-slate-400"
                            />
                            <button
                                type="button"
                                @click="afficherMotDePasse = !afficherMotDePasse"
                                class="shrink-0 text-slate-400 hover:text-slate-600"
                            >
                                <component :is="afficherMotDePasse ? EyeOff : Eye" class="h-4 w-4" />
                            </button>
                        </div>
                    </label>

                    <p v-if="erreur" class="rounded-lg border border-red-200 bg-red-50 px-4 py-2.5 text-sm text-red-600">
                        {{ erreur }}
                    </p>

                    <button
                        type="submit"
                        :disabled="chargement"
                        class="w-full rounded-full bg-gradient-to-r from-navy to-accent py-3 font-semibold text-white shadow-lg transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        {{ chargement ? 'Connexion…' : 'Se connecter' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
