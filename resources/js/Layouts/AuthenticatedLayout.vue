<script setup>
import { ref, computed } from 'vue'; // Ajout de computed
import { watch } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import {usePage } from '@inertiajs/vue3';
// Composant Toast PrimeVue — affiche les notifications dans le DOM
import Toast from 'primevue/toast';
// Service toast PrimeVue pour afficher les messages
import { useToast } from 'primevue/usetoast';
import { Link} from '@inertiajs/vue3'; // Ajout de usePage
 
 
// État pour le menu hamburger (mobile)
const showingNavigationDropdown = ref(false);
 
// Récupérer le service toast PrimeVue
const toast = useToast();
 
// Récupérer l'objet page d'Inertia qui contient les props partagées
 
 
/**
 * Fonction pour afficher les toasts basés sur les messages flash.
 * Elle est appelée au montage et à chaque changement de page.props.
 *
 * Les messages flash sont définis côté serveur via :
 * - redirect()->with('success', 'Message...')
 * - redirect()->with('error', 'Message...')
 * - redirect()->with('info', 'Message...')
 * - redirect()->with('warning', 'Message...')
 */
const displayFlashToasts = () => {
    const flash = page.props.flash;
   
    // Ne rien faire si flash est vide
    if (!flash) return;
 
    // Afficher le toast de succès (vert) - ex: création, modification, suppression
    if (flash.success) {
        toast.add({
            severity: 'success',
            summary: 'Succès',
            detail: flash.success,
            life: 4000,
        });
    }
 
    // Afficher le toast d'erreur (rouge) - ex: exception serveur
    if (flash.error) {
        toast.add({
            severity: 'error',
            summary: 'Erreur',
            detail: flash.error,
            life: 5000,
        });
    }
 
    // Afficher le toast d'information (bleu) - ex: message neutre
    if (flash.info) {
        toast.add({
            severity: 'info',
            summary: 'Information',
            detail: flash.info,
            life: 4000,
        });
    }
 
    // Afficher le toast d'avertissement (orange) - ex: action partielle
    if (flash.warning) {
        toast.add({
            severity: 'warn',
            summary: 'Avertissement',
            detail: flash.warning,
            life: 4000,
        });
    }
};
 
/**
 * Au montage du layout, afficher les toasts s'il y en a.
 * Cela capture les messages flash du premier chargement de la page.
 */
onMounted(() => {
    displayFlashToasts();
});
 
/**
 * Surveiller les changements dans page.props.
 * À chaque navigation Inertia (redirect, lien, formulaire), les props changent
 * et on affiche automatiquement les toasts correspondants.
 *
 * deep: true permet de détecter les mutations profondes dans les objets imbriqués.
 */
watch(
    () => page.props,
    () => {
        displayFlashToasts();
    },
    { deep: true }
);
const page = usePage();
 
// Détermine dynamiquement la route du dashboard selon le rôle
const dashboardRoute = computed(() => {
    const user = page.props.auth.user;
    if (!user || !user.role) return 'login';
   
    // Mapping basé sur ton fichier web.php
    const roleRoutes = {
        'admin': 'admin.dashboard',
        'cp': 'cp.dashboard',
        'sup': 'sup.dashboard',
        'tc': 'tc.dashboard'
    };
 
    return roleRoutes[user.role.name] || 'login';
});
 
// Surveiller les messages flash
watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) {
            toast.add({ severity: 'success', summary: 'Succès', detail: flash.success, life: 4000 });
        }
        if (flash?.error) {
            toast.add({ severity: 'error', summary: 'Erreur', detail: flash.error, life: 4000 });
        }
    },
    { deep: true, immediate: true }
);
</script>
 
<template>
    <div>
        <Toast position="top-center" />
        <div class="min-h-screen bg-gray-100">
            <nav class="border-b border-gray-100 bg-white">
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <!-- Changement : route(dashboardRoute) -->
                                <Link :href="route(dashboardRoute)">
                                    <ApplicationLogo class="block h-9 w-auto fill-current text-[#FF7A1A]" />
                                </Link>
                            </div>
 
                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <!-- Changement : route(dashboardRoute) et current(dashboardRoute) -->
                                <NavLink
                                    :href="route(dashboardRoute)"
                                    :active="route().current(dashboardRoute)"
                                >
                                    Dashboard
                                </NavLink>
                            </div>
<div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
    <NavLink
        :href="route('dashboard')"
        :active="route().current('dashboard')"
    >
        Dashboard
    </NavLink>
    <NavLink
        :href="route('planning-models.index')"
        :active="route().current('planning-models.*')"
    >
        Modèles Planning
    </NavLink>
    <NavLink
        :href="route('planning-assignments.index')"
        :active="route().current('planning-assignments.*')"
    >
        Affectations
    </NavLink>
</div>
                        </div>
 
                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
                                            >
                                                {{ $page.props.auth.user?.name }}
                                                <svg class="-me-0.5 ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>
 
                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')"> Profile </DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button"> Log Out </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>
 
                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
 
                <!-- Responsive Navigation Menu -->
                <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden">
                    <div class="space-y-1 pb-3 pt-2">
                        <!-- Changement : route(dashboardRoute) -->
                        <ResponsiveNavLink
                            :href="route(dashboardRoute)"
                            :active="route().current(dashboardRoute)"
                        >
                            Dashboard
                        </ResponsiveNavLink>
                    </div>
 
                    <div class="border-t border-gray-200 pb-1 pt-4">
                        <div class="px-4">
                            <div class="text-base font-medium text-gray-800">{{ $page.props.auth.user?.name }}</div>
                            <div class="text-sm font-medium text-gray-500">{{ $page.props.auth.user?.email }}</div>
                        </div>
                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')"> Profile </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button"> Log Out </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>
 
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>
 
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
 