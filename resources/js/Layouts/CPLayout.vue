<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { Link, usePage, router } from "@inertiajs/vue3";
import DropdownLink from "@/Components/DropdownLink.vue";
import Popover from "primevue/popover";
import OverlayBadge from "primevue/overlaybadge";
import Button from "primevue/button";
import Toast from "primevue/toast";
import { useToast } from "primevue/usetoast";

const toast = useToast();
const page = usePage();
const op = ref();

// Surveiller les messages flash de Laravel (Inertia)
watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) {
            toast.add({
                severity: "success",
                summary: "Succès",
                detail: flash.success,
                life: 4000,
            });
        }
        if (flash?.error) {
            toast.add({
                severity: "error",
                summary: "Erreur",
                detail: flash.error,
                life: 9000,
            });
        }
    },
    { deep: true, immediate: true }
);

/* ---------------- notifications ---------------- */
const notifications = computed(
    () => page.props.auth?.notifications || []
);

/* ---------------- toast ---------------- */
onMounted(() => {
    toast.add({
        severity: "success",
        summary: "Bienvenue 👋",
        detail: "Heureux de vous revoir sur votre espace Chef de Plateau !",
        life: 4000,
    });
});

/* ---------------- helpers ---------------- */
const isRouteActive = (name) => route().current(name);
</script>

<template>
<div class="min-h-screen bg-slate-50 flex text-slate-900 font-sans antialiased">
    <Toast position="top-right" />

    <!-- SIDEBAR -->
    <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col hidden md:flex border-r border-slate-800">

        <!-- LOGO -->
        <div class="h-16 flex items-center px-6 border-b border-slate-800">
            <span class="text-white font-black text-xl italic">
                CP<span class="text-[#FF7A1A]">PANEL</span>
            </span>
        </div>

        <!-- NAV -->
        <nav class="flex-1 p-4 space-y-1">

            <p class="text-[10px] uppercase text-slate-500 font-bold mb-4 px-3 tracking-widest">
                Pilotage Opérationnel
            </p>

            <!-- Dashboard CP -->
            <Link
                :href="route('cp.dashboard')"
                :class="[isRouteActive('cp.dashboard') ? 'bg-[#FF7A1A] text-white shadow-lg shadow-orange-900/20' : 'hover:bg-slate-800']"
                class="flex items-center p-3 rounded-xl transition group"
            >
                <i class="pi pi-home mr-3 text-[#FF7A1A] group-hover:text-white"></i>
                Dashboard
            </Link>

            <!-- Campagnes -->
            <Link
                :href="route('campaigns.index')"
                :class="[isRouteActive('campaigns.index') ? 'bg-[#FF7A1A] text-white shadow-lg shadow-orange-900/20' : 'hover:bg-slate-800']"
                class="flex items-center p-3 rounded-xl transition group"
            >
                <i class="pi pi-flag mr-3 text-[#FF7A1A] group-hover:text-white"></i>
                Campagnes
            </Link>

            <!-- Mes Équipes (Assignations) -->
            <Link
                :href="route('assignments.index')"
                :class="[isRouteActive('assignments.index') ? 'bg-[#FF7A1A] text-white shadow-lg shadow-orange-900/20' : 'hover:bg-slate-800']"
                class="flex items-center p-3 rounded-xl transition group"
            >
                <i class="pi pi-users mr-3 text-[#FF7A1A] group-hover:text-white"></i>
                Mes Équipes
            </Link>

            <div class="h-[1px] bg-slate-800 my-4 mx-3"></div>

            <p class="text-[10px] uppercase text-slate-500 font-bold mb-4 px-3 tracking-widest">
                Gestion Plannings
            </p>

            <!-- Validations -->
            <Link
                :href="route('cp.planning-validation')"
                :class="[isRouteActive('cp.planning-validation') ? 'bg-[#FF7A1A] text-white shadow-lg shadow-orange-900/20' : 'hover:bg-slate-800']"
                class="flex items-center p-3 rounded-xl transition group"
            >
                <i class="pi pi-check-square mr-3 text-[#FF7A1A] group-hover:text-white"></i>
                Validations
            </Link>

            <!-- Modèles Planning -->
            <Link
                :href="route('planning-models.index')"
                :class="[isRouteActive('planning-models.index') ? 'bg-[#FF7A1A] text-white shadow-lg shadow-orange-900/20' : 'hover:bg-slate-800']"
                class="flex items-center p-3 rounded-xl transition group"
            >
                <i class="pi pi-table mr-3 text-[#FF7A1A] group-hover:text-white"></i>
                Modèles Planning
            </Link>

            <!-- Affectations Plannings -->
            <Link
                :href="route('planning-assignments.index')"
                :class="[isRouteActive('planning-assignments.index') ? 'bg-[#FF7A1A] text-white shadow-lg shadow-orange-900/20' : 'hover:bg-slate-800']"
                class="flex items-center p-3 rounded-xl transition group"
            >
                <i class="pi pi-calendar mr-3 text-[#FF7A1A] group-hover:text-white"></i>
                Affectations
            </Link>

            <!-- Reporting -->
            <Link
                :href="route('reporting.index')"
                :class="[isRouteActive('reporting.index') ? 'bg-[#FF7A1A] text-white shadow-lg shadow-orange-900/20' : 'hover:bg-slate-800']"
                class="flex items-center p-3 rounded-xl transition group"
            >
                <i class="pi pi-chart-bar mr-3 text-[#FF7A1A] group-hover:text-white"></i>
                Reporting
            </Link>

            <!-- Timesheets -->
            <Link
                :href="route('timesheets.index')"
                :class="[isRouteActive('timesheets.index') ? 'bg-[#FF7A1A] text-white shadow-lg shadow-orange-900/20' : 'hover:bg-slate-800']"
                class="flex items-center p-3 rounded-xl transition group"
            >
                <i class="pi pi-clock mr-3 text-[#FF7A1A] group-hover:text-white"></i>
                Timesheets
            </Link>

        </nav>

        <!-- USER INFO -->
        <div class="p-4 border-t border-slate-800">
            <div class="bg-slate-800/50 p-3 rounded-xl flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-orange-500 flex items-center justify-center text-white font-bold text-xs">
                    {{ page.props.auth.user?.name?.charAt(0) || '?' }}
                </div>
                <div class="overflow-hidden">
                    <p class="text-xs font-bold text-white truncate">{{ page.props.auth.user?.name }}</p>
                    <p class="text-[10px] text-slate-500 truncate">Chef de Plateau</p>
                </div>
            </div>
        </div>

    </aside>

    <!-- MAIN -->
    <div class="flex-1 flex flex-col">

        <!-- TOPBAR -->
        <header class="h-16 bg-white flex items-center justify-between px-8 border-b border-slate-200 sticky top-0 z-10">
            <h2 class="font-bold text-slate-800 tracking-tight">Espace Opérationnel</h2>

            <div class="flex items-center gap-4">
                <OverlayBadge :value="notifications?.length || null" severity="danger" :style="{ display: notifications?.length ? 'inline-flex' : 'none' }">
                    <Button type="button" icon="pi pi-bell" @click="(e) => op?.toggle(e)" text plain class="!text-slate-400" />
                </OverlayBadge>

                <div class="h-8 w-[1px] bg-slate-200"></div>

                <Link :href="route('logout')" method="post" as="button" class="text-xs font-bold text-rose-500 hover:text-rose-700 transition-colors">
                    DÉCONNEXION
                </Link>
            </div>
        </header>

        <!-- CONTENT -->
        <main class="p-8 flex-1">
            <slot />
        </main>

        <!-- NOTIFICATIONS POPOVER -->
        <Popover ref="op" class="w-[380px] shadow-2xl border border-slate-100">
            <div class="p-4 border-b font-bold text-slate-800">
                Notifications
            </div>

            <div v-if="notifications.length > 0" class="max-h-96 overflow-y-auto">
                <div
                    v-for="notif in notifications"
                    :key="notif.id"
                    class="p-4 border-b hover:bg-slate-50 flex gap-3 cursor-pointer transition-colors"
                >
                    <i class="pi pi-calendar text-orange-500 mt-1"></i>
                    <div>
                        <p class="text-sm text-slate-700">{{ notif.data?.message }}</p>
                        <p class="text-[10px] text-slate-400 mt-1">
                            {{ new Date(notif.created_at).toLocaleString('fr-FR') }}
                        </p>
                    </div>
                </div>
            </div>
            <div v-else class="p-8 text-center text-slate-400 italic">
                Aucune notification pour le moment
            </div>
        </Popover>

    </div>
</div>
</template>
