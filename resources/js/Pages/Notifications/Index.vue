<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import Card from 'primevue/card';

defineOptions({ layout: AdminLayout });

defineProps({
    allNotifications: Array
});
</script>

<template>
    <Head title="Toutes mes notifications" />

    <div class="max-w-4xl mx-auto py-8">
        <h1 class="text-2xl font-bold mb-6">Historique des notifications</h1>

        <div v-if="allNotifications.length > 0" class="space-y-4">
            <Card v-for="notif in allNotifications" :key="notif.id" 
                  :class="{'bg-white': notif.read_at, 'bg-orange-50 border-l-4 border-[#FF7A1A]': !notif.read_at}">
                <template #content>
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-lg" :class="{'font-bold': !notif.read_at}">
                                {{ notif.data.message }}
                            </p>
                            <span class="text-sm text-gray-500 italic">
                                Reçu le {{ new Date(notif.created_at).toLocaleDateString() }} à {{ new Date(notif.created_at).toLocaleTimeString() }}
                            </span>
                        </div>
                        <div v-if="!notif.read_at">
                            <span class="px-2 py-1 bg-[#FF7A1A] text-white text-xs rounded-full">Nouveau</span>
                        </div>
                    </div>
                </template>
            </Card>
        </div>

        <Card v-else>
            <template #content>
                <p class="text-center text-gray-500">Vous n'avez aucun historique de notification.</p>
            </template>
        </Card>
    </div>
</template>