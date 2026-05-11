<script setup>
import { ref, computed, onMounted } from "vue";
import { Link, usePage, router } from "@inertiajs/vue3";

import { watch } from "vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import OverlayPanel from "primevue/overlaypanel";
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
        detail: "Heureux de vous revoir sur votre tableau de bord !",
        life: 4000,
    });
});

/* ---------------- helpers ---------------- */
const isRouteActive = (name) => route().current(name);

/* ---------------- actions ---------------- */
const markAsRead = (id) => {
    console.log("Marqué comme lu :", id);
};

const activateUsers = () => {
    router.post(route("no_users"));
};
</script>

<template>
<Toast position="top-right" />

<div class="min-h-screen bg-slate-50 flex text-slate-900">

<!-- SIDEBAR -->
<aside class="w-64 bg-slate-900 text-slate-300 flex flex-col hidden md:flex border-r border-slate-800">

    <!-- LOGO -->
    <div class="h-16 flex items-center px-6 border-b border-slate-800">
        <span class="text-white font-black text-xl">
            WIP<span class="text-[#FF7A1A]">LITE</span>
        </span>
    </div>

    <!-- NAV -->
    <nav class="flex-1 p-4 space-y-1">

        <p class="text-[10px] uppercase text-slate-500 font-bold mb-4 px-3">
            Menu Principal
        </p>

        <!-- Dashboard -->
        <Link
            :href="route('admin.dashboard')"
            :class="[isRouteActive('admin.dashboard') ? 'bg-[#FF7A1A] text-white shadow-lg shadow-orange-900/20' : 'hover:bg-slate-800']"
            class="flex items-center p-3 rounded-xl transition"
        >
            <i class="pi pi-home mr-3"></i>
            Dashboard
        </Link>

        <!-- Employés -->
        <Link
            :href="route('employees.index')"
            :class="[isRouteActive('employees.index') ? 'bg-[#FF7A1A] text-white shadow-lg shadow-orange-900/20' : 'hover:bg-slate-800']"
            class="flex items-center p-3 rounded-xl transition"
        >
            <i class="pi pi-users mr-3"></i>
            Employés
        </Link>

        <!-- Planning models -->
        <Link
            :href="route('planning-models.index')"
            :class="[isRouteActive('planning-models.index') ? 'bg-[#FF7A1A] text-white shadow-lg shadow-orange-900/20' : 'hover:bg-slate-800']"
            class="flex items-center p-3 rounded-xl transition"
        >
            <i class="pi pi-table mr-3"></i>
            Modèles Planning
        </Link>

        <!-- ✅ Planning Assignments (RESTORED) -->
        <Link
            :href="route('planning-assignments.index')"
            :class="[isRouteActive('planning-assignments.index') ? 'bg-[#FF7A1A] text-white shadow-lg shadow-orange-900/20' : 'hover:bg-slate-800']"
            class="flex items-center p-3 rounded-xl transition"
        >
            <i class="pi pi-users mr-3"></i>
            Affectations plannings
        </Link>

        <Link
            :href="route('timesheets.index')"
            :class="[isRouteActive('timesheets.index') ? 'bg-[#FF7A1A] text-white shadow-lg shadow-orange-900/20' : 'hover:bg-slate-800']"
            class="flex items-center p-3 rounded-xl transition"
>
<i class="pi pi-link mr-3"></i>
            timesheets
</Link>

        <!-- Activation comptes -->
        <Link
            :href="route('users.index')"
            class="flex items-center p-3 rounded-xl hover:bg-slate-800 transition"
        >
            <i class="pi pi-user-plus mr-3"></i>
            Gestion Utilisateurs
        </Link>

        <!-- Reporting -->
        <Link
            :href="route('reporting.index')"
            :class="[isRouteActive('reporting.index') ? 'bg-[#FF7A1A] text-white shadow-lg shadow-orange-900/20' : 'hover:bg-slate-800']"
            class="flex items-center p-3 rounded-xl transition"
        >
            <i class="pi pi-chart-bar mr-3"></i>
            Reporting Global
        </Link>

        <!-- Campaigns -->
        <Link
            :href="route('campaigns.index')"
            :class="[isRouteActive('campaigns.index') ? 'bg-[#FF7A1A] text-white shadow-lg shadow-orange-900/20' : 'hover:bg-slate-800']"
            class="flex items-center p-3 rounded-xl transition"
        >
            <i class="pi pi-flag mr-3"></i>
            Campagnes
        </Link>

        <!-- ✅ Assignations campagnes (RESTORED) -->
        <Link
            :href="route('assignments.index')"
            :class="[isRouteActive('assignments.index') ? 'bg-[#FF7A1A] text-white shadow-lg shadow-orange-900/20' : 'hover:bg-slate-800']"
            class="flex items-center p-3 rounded-xl transition"
        >
            <i class="pi pi-link mr-3"></i>
            Assignations campagnes
        </Link>

    </nav>

    <!-- USER -->
    <div class="p-4 border-t border-slate-800">
        <p class="text-xs font-bold text-white truncate">
            {{ page.props.auth.user.name }}
        </p>
        <p class="text-[10px] text-slate-500 truncate">
            {{ page.props.auth.user.email }}
        </p>
    </div>

</aside>

<!-- MAIN -->
<div class="flex-1 flex flex-col">

<!-- TOPBAR -->
<header class="h-16 bg-white/80 backdrop-blur flex items-center justify-between px-8 border-b">

    <h2 class="font-bold">Administration</h2>

    <div class="flex items-center gap-3">

        <Button
            icon="pi pi-bell"
            text
            plain
            class="!w-10 !h-10 !rounded-full"
            v-badge.danger="notifications.length || null"
            @click="(e) => op.toggle(e)"
        />

        <DropdownLink
            :href="route('logout')"
            method="post"
            as="button"
            class="text-sm font-semibold hover:text-red-600"
        >
            Déconnexion
        </DropdownLink>

    </div>
</header>

<!-- NOTIFICATIONS -->
<OverlayPanel ref="op" class="w-[380px]">

    <div class="p-4 border-b font-bold">
        Notifications
    </div>

    <div v-if="notifications.length > 0">
        <div
            v-for="notif in notifications"
            :key="notif.id"
            class="p-4 border-b hover:bg-slate-50 flex gap-3 cursor-pointer"
            @click="markAsRead(notif.id)"
        >
            <i class="pi pi-calendar text-blue-500 mt-1"></i>

            <div>
                <p class="text-sm">
                    {{ notif.data.message }}
                </p>

                <p class="text-[10px] text-slate-400">
                    {{ new Date(notif.created_at).toLocaleString('fr-FR') }}
                </p>
            </div>
        </div>
    </div>

    <div v-else class="p-6 text-center text-slate-400">
        Tout est à jour !
    </div>

</OverlayPanel>

<!-- CONTENT -->
<main class="p-8 flex-1">
    <slot />
</main>

</div>
</div>
</template>