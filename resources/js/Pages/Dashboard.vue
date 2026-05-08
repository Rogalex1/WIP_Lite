<script setup>
import { onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import Toast from 'primevue/toast';
import Button from 'primevue/button';
import Card from 'primevue/card';

const toast = useToast();

// Définition des items pour la grille de navigation
const menuItems = [
    {
        title: 'Employés',
        description: 'Gérer les effectifs',
        route: 'employees.index',
        icon: 'pi pi-users',
        color: 'text-blue-600',
        bgColor: 'bg-blue-50'
    },
    {
        title: 'Affectations',
        description: 'Piloter le plateau',
        route: 'assignments.index',
        icon: 'pi pi-sitemap',
        color: 'text-orange-600',
        bgColor: 'bg-orange-50'
    },
    {
        title: 'Campagnes',
        description: 'Suivi des projets',
        route: 'campaigns.index',
        icon: 'pi pi-briefcase',
        color: 'text-purple-600',
        bgColor: 'bg-purple-50'
    }
];

onMounted(() => {
    toast.add({
        severity: 'success',
        summary: 'Bienvenue 👋',
        detail: 'Heureux de vous revoir sur votre tableau de bord !',
        life: 4000
    });
});
</script>

<template>
    <Head title="Dashboard" />

        <Toast />



    <AuthenticatedLayout>
        <template #header>
            <Toast />
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Tableau de bord
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-8">
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <Link v-for="item in menuItems" :key="item.route" 
                          :href="route().has(item.route) ? route(item.route) : '#'" 
                          class="no-underline">
                        <Card class="hover:shadow-lg transition-all duration-300 border-none h-full cursor-pointer">
                            <template #content>
                                <div class="flex items-center gap-4">
                                    <div :class="['p-4 rounded-xl', item.bgColor]">
                                        <i :class="[item.icon, item.color, 'text-2xl']"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-800">{{ item.title }}</h3>
                                        <p class="text-sm text-gray-500">{{ item.description }}</p>
                                    </div>
                                </div>
                            </template>
                        </Card>
                    </Link>
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg border border-gray-100">
                    <div class="p-6 text-gray-900 font-medium">
                        Dernières activités du système
                        <p class="text-sm text-gray-400 mt-1">Consultez les mises à jour récentes de vos projets.</p>
                    </div>
                </div>

                <Card>
                    <template #title>
                        <div class="flex items-center text-lg font-bold">
                            <i class="pi pi-home mr-2 text-blue-500"></i>
                            Bienvenue sur Wip Lite
                        </div>
                    </template>
                    <template #content>
                        <p class="text-gray-600 mb-8">
                            Gérez facilement vos employés, campagnes et affectations depuis cette interface centralisée.
                        </p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl p-6 text-white shadow-md">
                                <i class="pi pi-users text-3xl mb-4 opacity-50"></i>
                                <h3 class="text-lg font-bold mb-2">Employés</h3>
                                <p class="text-blue-100 text-xs mb-4">Module de gestion des profils actif.</p>
                                <Link :href="route('employees.index')">
                                    <Button label="Ouvrir" class="p-button-sm p-button-rounded bg-white text-blue-600 border-none w-full" />
                                </Link>
                            </div>

                            <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-2xl p-6 text-white shadow-md">
                                <i class="pi pi-sitemap text-3xl mb-4 opacity-50"></i>
                                <h3 class="text-lg font-bold mb-2">Plateau</h3>
                                <p class="text-emerald-100 text-xs mb-4">Structure hiérarchique en direct.</p>
                                <Link :href="route('assignments.index')">
                                    <Button label="Piloter" class="p-button-sm p-button-rounded bg-white text-emerald-600 border-none w-full" />
                                </Link>
                            </div>

                            <div class="bg-gradient-to-br from-slate-700 to-slate-900 rounded-2xl p-6 text-white shadow-md">
                                <i class="pi pi-cog text-3xl mb-4 opacity-50"></i>
                                <h3 class="text-lg font-bold mb-2">Paramètres</h3>
                                <p class="text-slate-300 text-xs mb-4">Configuration des campagnes.</p>
                                <Button label="Bientôt" disabled class="p-button-sm p-button-rounded bg-slate-600 text-white border-none w-full" />
                            </div>
                        </div>
                    </template>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.p-card:hover {
    transform: translateY(-4px);
}
.p-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>