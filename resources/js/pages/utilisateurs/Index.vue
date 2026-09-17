<script setup>
import { onMounted, ref } from 'vue';
import api from '@/lib/api';
import { useAuthStore } from '@/stores/auth';
import AppLayout from '@/layouts/AppLayout.vue';
import { Plus, UserX, UserCheck as UserCheckIcon } from '@lucide/vue';

const auth = useAuthStore();

const utilisateurs = ref([]);
const roles = ref([]);
const departements = ref([]);
const chargement = ref(true);
const erreur = ref(null);
const enCours = ref(false);
const afficherFormulaire = ref(false);

const nouveauUtilisateur = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role_id: '',
    department_id: '',
});

async function charger() {
    chargement.value = true;
    const [reponseUtilisateurs, reponseRoles, reponseDepartements] = await Promise.all([
        api.get('/users'),
        api.get('/roles'),
        api.get('/departments'),
    ]);
    utilisateurs.value = reponseUtilisateurs.data.data;
    roles.value = reponseRoles.data.data;
    departements.value = reponseDepartements.data.data;
    chargement.value = false;
}

onMounted(charger);

async function creerUtilisateur() {
    erreur.value = null;
    enCours.value = true;

    try {
        await api.post('/users', {
            ...nouveauUtilisateur.value,
            role_id: nouveauUtilisateur.value.role_id || null,
            department_id: nouveauUtilisateur.value.department_id || null,
        });
        nouveauUtilisateur.value = {
            name: '', email: '', password: '', password_confirmation: '', role_id: '', department_id: '',
        };
        afficherFormulaire.value = false;
        await charger();
    } catch (e) {
        erreur.value = Object.values(e.response?.data?.errors ?? {})[0]?.[0] ?? 'Erreur lors de la création.';
    } finally {
        enCours.value = false;
    }
}

async function changerRole(utilisateur, roleId) {
    await api.put(`/users/${utilisateur.id}`, { role_id: roleId || null });
    await charger();
}

async function changerDepartement(utilisateur, departmentId) {
    await api.put(`/users/${utilisateur.id}`, { department_id: departmentId || null });
    await charger();
}

async function basculerActif(utilisateur) {
    enCours.value = true;
    try {
        if (utilisateur.actif) {
            await api.delete(`/users/${utilisateur.id}`); // désactive côté backend
        } else {
            await api.put(`/users/${utilisateur.id}`, { actif: true });
        }
        await charger();
    } catch (e) {
        erreur.value = e.response?.data?.message ?? 'Action impossible.';
    } finally {
        enCours.value = false;
    }
}
</script>

<template>
    <AppLayout>
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-navy">Utilisateurs</h1>
            <button
                @click="afficherFormulaire = !afficherFormulaire"
                class="flex items-center gap-1.5 rounded-lg bg-accent px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-accent/90"
            >
                <Plus class="h-4 w-4" />
                Nouvel utilisateur
            </button>
        </div>

        <form
            v-if="afficherFormulaire"
            @submit.prevent="creerUtilisateur"
            class="mb-6 grid gap-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:grid-cols-2"
        >
            <label class="block">
                <span class="mb-1.5 block text-sm font-medium text-slate-700">Nom complet</span>
                <input
                    v-model="nouveauUtilisateur.name"
                    required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-navy outline-none focus:border-accent focus:ring-2 focus:ring-accent/30"
                />
            </label>
            <label class="block">
                <span class="mb-1.5 block text-sm font-medium text-slate-700">Email</span>
                <input
                    v-model="nouveauUtilisateur.email"
                    type="email"
                    required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-navy outline-none focus:border-accent focus:ring-2 focus:ring-accent/30"
                />
            </label>
            <label class="block">
                <span class="mb-1.5 block text-sm font-medium text-slate-700">Mot de passe</span>
                <input
                    v-model="nouveauUtilisateur.password"
                    type="password"
                    required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-navy outline-none focus:border-accent focus:ring-2 focus:ring-accent/30"
                />
            </label>
            <label class="block">
                <span class="mb-1.5 block text-sm font-medium text-slate-700">Confirmation</span>
                <input
                    v-model="nouveauUtilisateur.password_confirmation"
                    type="password"
                    required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-navy outline-none focus:border-accent focus:ring-2 focus:ring-accent/30"
                />
            </label>
            <label class="block">
                <span class="mb-1.5 block text-sm font-medium text-slate-700">Rôle</span>
                <select
                    v-model="nouveauUtilisateur.role_id"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-navy outline-none focus:border-accent focus:ring-2 focus:ring-accent/30"
                >
                    <option value="">Aucun</option>
                    <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.nom }}</option>
                </select>
            </label>
            <label class="block">
                <span class="mb-1.5 block text-sm font-medium text-slate-700">Département</span>
                <select
                    v-model="nouveauUtilisateur.department_id"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-navy outline-none focus:border-accent focus:ring-2 focus:ring-accent/30"
                >
                    <option value="">Aucun</option>
                    <option v-for="departement in departements" :key="departement.id" :value="departement.id">
                        {{ departement.nom }}
                    </option>
                </select>
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

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <p v-if="chargement" class="p-6 text-slate-500">Chargement…</p>

            <table v-else class="w-full text-left text-sm text-slate-700">
                <thead class="border-b border-slate-200 text-xs uppercase text-slate-400">
                    <tr>
                        <th class="px-6 py-3">Nom</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Rôle</th>
                        <th class="px-6 py-3">Département</th>
                        <th class="px-6 py-3">Statut</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="utilisateur in utilisateurs" :key="utilisateur.id" class="border-b border-slate-100">
                        <td class="px-6 py-4 font-medium text-navy">{{ utilisateur.name }}</td>
                        <td class="px-6 py-4">{{ utilisateur.email }}</td>
                        <td class="px-6 py-4">
                            <select
                                :value="utilisateur.role?.id ?? ''"
                                @change="changerRole(utilisateur, $event.target.value)"
                                class="rounded-lg border border-slate-200 bg-white px-2 py-1 text-sm text-navy outline-none focus:border-accent"
                            >
                                <option value="">Aucun</option>
                                <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.nom }}</option>
                            </select>
                        </td>
                        <td class="px-6 py-4">
                            <select
                                :value="utilisateur.department?.id ?? ''"
                                @change="changerDepartement(utilisateur, $event.target.value)"
                                class="rounded-lg border border-slate-200 bg-white px-2 py-1 text-sm text-navy outline-none focus:border-accent"
                            >
                                <option value="">Aucun</option>
                                <option v-for="departement in departements" :key="departement.id" :value="departement.id">
                                    {{ departement.nom }}
                                </option>
                            </select>
                        </td>
                        <td class="px-6 py-4">
                            <span
                                class="rounded-full px-2.5 py-1 text-xs font-medium"
                                :class="utilisateur.actif ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500'"
                            >
                                {{ utilisateur.actif ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button
                                v-if="utilisateur.id !== auth.user?.id"
                                :disabled="enCours"
                                @click="basculerActif(utilisateur)"
                                class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100"
                                :title="utilisateur.actif ? 'Désactiver' : 'Réactiver'"
                            >
                                <component :is="utilisateur.actif ? UserX : UserCheckIcon" class="h-4 w-4" />
                            </button>
                        </td>
                    </tr>

                    <tr v-if="!utilisateurs.length">
                        <td colspan="6" class="px-6 py-8 text-center text-slate-400">Aucun utilisateur.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
