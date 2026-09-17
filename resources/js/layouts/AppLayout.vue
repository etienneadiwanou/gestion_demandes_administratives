<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import {
    LayoutDashboard,
    FileText,
    Folder,
    UserCheck,
    Users,
    Building2,
    Settings,
    Bell,
    LogOut,
} from '@lucide/vue';

const auth = useAuthStore();
const router = useRouter();

async function seDeconnecter() {
    await auth.logout();
    router.push({ name: 'login' });
}

const estAdmin = computed(() => auth.role === 'administrateur');
const estAgentOuPlus = computed(() => ['agent', 'validateur', 'administrateur'].includes(auth.role));

const navItems = computed(() => [
    { name: 'dashboard', label: 'Tableau de bord', icon: LayoutDashboard, to: { name: 'dashboard' }, visible: true },
    { name: 'demandes', label: 'Demandes', icon: FileText, to: { name: 'demandes.index' }, visible: true },
    { name: 'documents', label: 'Documents', icon: Folder, to: { name: 'documents.index' }, visible: true },
    { name: 'affectations', label: 'Affectations', icon: UserCheck, to: { name: 'affectations.index' }, visible: estAgentOuPlus.value },
    { name: 'utilisateurs', label: 'Utilisateurs', icon: Users, to: { name: 'utilisateurs.index' }, visible: estAdmin.value },
    { name: 'departements', label: 'Départements', icon: Building2, to: { name: 'departements.index' }, visible: estAdmin.value },
    { name: 'parametres', label: 'Paramètres', icon: Settings, to: { name: 'parametres.index' }, visible: estAdmin.value },
].filter((item) => item.visible));

const initiales = computed(() => {
    const nom = auth.user?.name ?? '';
    return nom
        .split(' ')
        .map((mot) => mot[0])
        .slice(0, 2)
        .join('')
        .toUpperCase();
});
</script>

<template>
    <div class="flex min-h-screen bg-slate-50">
        <!-- Sidebar -->
        <aside class="hidden w-64 flex-col bg-navy lg:flex">
            <div class="flex items-center gap-3 px-6 py-6">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-accent text-sm font-bold text-white">
                    A
                </div>
                <div>
                    <p class="text-sm font-bold leading-none text-white">AdminFlow</p>
                    <p class="mt-1 text-[10px] font-medium uppercase tracking-wider text-white/40">Administration</p>
                </div>
            </div>

            <nav class="flex-1 space-y-1 px-4">
                <RouterLink
                    v-for="item in navItems"
                    :key="item.name"
                    :to="item.to"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-white/60 transition hover:bg-white/5 hover:text-white"
                    active-class="!bg-accent !text-white"
                >
                    <component :is="item.icon" class="h-[18px] w-[18px] shrink-0" />
                    {{ item.label }}
                </RouterLink>
            </nav>

            <div class="flex items-center gap-3 border-t border-white/10 px-6 py-5">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-accent text-xs font-semibold text-white">
                    {{ initiales }}
                </span>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium text-white">{{ auth.user?.name }}</p>
                    <p class="text-[10px] uppercase tracking-wider text-white/40">{{ auth.role ?? 'sans rôle' }}</p>
                </div>
            </div>
        </aside>

        <div class="flex flex-1 flex-col">
            <!-- Topbar -->
            <header class="sticky top-0 z-20 border-b border-slate-200 bg-white">
                <div class="flex items-center justify-between px-6 py-4">
                    <RouterLink :to="{ name: 'dashboard' }" class="text-lg font-bold text-navy lg:hidden">
                        AdminFlow
                    </RouterLink>
                    <div class="hidden lg:block"></div>

                    <div class="flex items-center gap-4">
                        <button class="text-slate-400 transition hover:text-navy" title="Notifications">
                            <Bell class="h-5 w-5" />
                        </button>

                        <button
                            @click="seDeconnecter"
                            class="flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-1.5 text-sm text-slate-600 transition hover:bg-slate-50"
                        >
                            <LogOut class="h-4 w-4" />
                            Se déconnecter
                        </button>
                    </div>
                </div>
            </header>

            <main class="flex-1 px-6 py-8">
                <slot />
            </main>
        </div>
    </div>
</template>
