<script setup>
import AppLayout from '@/layouts/AppLayout.vue';

defineProps({
    titre: { type: String, default: 'Bientôt disponible' },
});
</script>

<template>
    <AppLayout>
        <div class="animate-fade-in-up rounded-2xl border border-slate-200 bg-white p-10 text-center shadow-sm">
            <h1 class="text-xl font-semibold text-navy">{{ titre }}</h1>
            <p class="mt-2 text-slate-500">Cet écran arrive bientôt.</p>
        </div>
    </AppLayout>
</template>
