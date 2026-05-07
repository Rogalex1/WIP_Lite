<script setup>
import { ref } from "vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import { Link, usePage } from "@inertiajs/vue3";
import OverlayPanel from "primevue/overlaypanel";
import Button from "primevue/button";
import { computed } from "vue";
import { onMounted } from 'vue';


import { useToast } from 'primevue/usetoast';
import Toast from 'primevue/toast';
const toast = useToast();

onMounted(() => {
    toast.add({
        severity: 'success',
        summary: 'Bienvenue 👋',
        detail: 'Heureux de vous revoir sur votre tableau de bord !',
        life: 4000
    });
});


const op = ref();
const page = usePage();

const markAsRead = (id) => {
    // Logique router.post ici plus tard
    console.log("Marqué comme lu :", id);
};
// On utilise une computed property pour que le badge soit réactif
const notifications = computed(() => page.props.auth.notifications);

// Fonction pour gérer la classe active des liens
const isActive = (route) => page.url.startsWith(route);
</script>

<template>
   <Toast position="top-right" />

    <div
        class="min-h-screen bg-slate-50 flex font-sans antialiased text-slate-900"
    >
        <!-- Sidebar -->
<!-- Sidebar -->
<aside
    class="w-64 bg-slate-900 text-slate-300 flex-shrink-0 hidden md:flex flex-col border-r border-slate-800"
>
    <!-- Logo -->
    <div class="h-16 flex items-center px-6 border-b border-slate-800">
        <span class="text-white font-black tracking-tighter text-xl">
            WIP<span class="text-indigo-500">LITE</span>
        </span>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 p-4 space-y-1">

        <p class="text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-4 px-3">
            Menu Principal
        </p>

        <!-- Dashboard -->
        <Link href="/admin/dashboard"
            :class="[isActive('/admin/dashboard') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800 hover:text-white']"
            class="flex items-center p-3 rounded-xl transition-all duration-200 group">
            <i class="pi pi-home mr-3 text-lg"
                :class="isActive('/admin/dashboard') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-indigo-400'"/>
            <span class="font-medium">Dashboard</span>
        </Link>

        <!-- Employees -->
        <Link href="/admin/employees"
            :class="[isActive('/admin/employees') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800 hover:text-white']"
            class="flex items-center p-3 rounded-xl transition-all duration-200 group">
            <i class="pi pi-users mr-3 text-lg"
                :class="isActive('/admin/employees') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-indigo-400'"/>
            <span class="font-medium">Employés</span>
        </Link>

        <!-- Employees route name -->
        <Link :href="route('employees.index')"
            :class="[route().current('employees.index') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800 hover:text-white']"
            class="flex items-center p-3 rounded-xl transition-all duration-200 group">
            <i class="pi pi-users mr-3 text-lg"
                :class="route().current('employees.index') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-indigo-400'"/>
            <span class="font-medium">Employés (Route)</span>
        </Link>

        <!-- Campaigns -->
        <Link href="/admin/campaigns"
            :class="[isActive('/admin/campaigns') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800 hover:text-white']"
            class="flex items-center p-3 rounded-xl transition-all duration-200 group">
            <i class="pi pi-flag mr-3 text-lg"
                :class="isActive('/admin/campaigns') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-indigo-400'"/>
            <span class="font-medium">Campagnes</span>
        </Link>

        <!-- Planning Models -->
        <Link href="/planning-models"
            :class="[isActive('/planning-models') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800 hover:text-white']"
            class="flex items-center p-3 rounded-xl transition-all duration-200 group">
            <i class="pi pi-table mr-3 text-lg"
                :class="isActive('/planning-models') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-indigo-400'"/>
            <span class="font-medium">Modèles Planning</span>
        </Link>

        <!-- Planning Assignments -->
        <Link href="/planning-assignments"
            :class="[isActive('/planning-assignments') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800 hover:text-white']"
            class="flex items-center p-3 rounded-xl transition-all duration-200 group">
            <i class="pi pi-users mr-3 text-lg"
                :class="isActive('/planning-assignments') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-indigo-400'"/>
            <span class="font-medium">Affectations</span>
        </Link>

        <!-- Activation comptes -->
        <Link href="/admin/no_users"
            class="flex items-center p-3 rounded-xl hover:bg-slate-800 hover:text-white transition-all duration-200 group">
            <i class="pi pi-user-plus mr-3 text-lg text-slate-500 group-hover:text-indigo-400"></i>
            <span class="font-medium">Activation des comptes</span>
        </Link>

        <!-- Reporting -->
        <Link :href="route('reporting.index')"
            :class="[route().current('reporting.index') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 hover:text-white']"
            class="flex items-center p-3 rounded-xl transition-all group">
            <i class="pi pi-chart-bar mr-3"></i>
            <span class="font-medium">Reporting Global</span>
        </Link>

    </nav>

    <!-- User Card -->
    <div class="p-4 border-t border-slate-800">
        <div class="bg-slate-800/50 p-3 rounded-xl flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-indigo-500 flex items-center justify-center text-white font-bold text-xs">
                AD
            </div>
            <div>
                <p class="text-xs font-bold text-white">Administrateur</p>
                <p class="text-[10px] text-slate-500">admin@wiplite.com</p>
            </div>
        </div>
    </div>
</aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Topbar -->
            <header
                class="h-16 bg-white/80 backdrop-blur-md sticky top-0 z-10 flex items-center justify-between px-8 border-b border-slate-200"
            >
                        <div>

            </div>
                <h2 class="font-bold text-slate-800 tracking-tight">
                    Administration
                </h2>

                <DropdownLink :href="route('logout')" method="post" as="button">
                    Log Out
                </DropdownLink>
                <div class="flex items-center gap-3">
                    <!-- Notification Button -->
<!-- Un seul bouton propre qui porte le badge et l'événement -->
    <Button
        type="button"
        icon="pi pi-bell"
        @click="(event) => op.toggle(event)"
        v-badge.danger="notifications.length || null"
        text
        plain
        class="!p-2 !w-10 !h-10 !rounded-full hover:!bg-slate-100 transition-colors"
    />

                    <div class="h-8 w-[1px] bg-slate-200 mx-2"></div>

                    <button
                        class="flex items-center gap-2 hover:bg-slate-50 p-1 rounded-full transition-all"
                    >
                        <i class="pi pi-cog text-slate-400"></i>
                    </button>
                </div>

            </header>


            <!-- OverlayPanel épuré -->
            <OverlayPanel
                ref="op"
                style="width: 380px"
                class="shadow-2xl border-slate-200 rounded-2xl overflow-hidden"
            >
                <div class="flex flex-col">
                    <div
                        class="p-4 border-b border-slate-100 flex justify-between items-center"
                    >
                        <span class="font-bold text-slate-800"
                            >Notifications</span
                        >
                        <span
                            v-if="$page.props.auth.notifications.length"
                            class="text-[10px] bg-indigo-50 text-indigo-600 px-2 py-1 rounded-md font-bold uppercase"
                            >Nouveau</span
                        >
                    </div>

                    <div class="max-h-[400px] overflow-y-auto">
                        <div v-if="$page.props.auth.notifications.length > 0">
                            <div
                                v-for="notif in $page.props.auth.notifications"
                                :key="notif.id"
                                class="p-4 border-b border-slate-50 hover:bg-slate-50 cursor-pointer transition-all flex gap-4"
                                @click="markAsRead(notif.id)"
                            >
                                <div
                                    class="w-10 h-10 rounded-full bg-blue-50 flex-shrink-0 flex items-center justify-center"
                                >
                                    <i class="pi pi-calendar text-blue-500"></i>
                                </div>
                                <div class="flex-1">
                                    <p
                                        class="text-sm text-slate-700 leading-snug mb-1"
                                    >
                                        {{ notif.data.message }}
                                    </p>
<div class="text-[10px] text-slate-400 italic mt-1 flex items-center gap-1">
    <i class="pi pi-clock text-[9px]"></i>
    {{ new Date(notif.created_at).toLocaleString('fr-FR', { 
        day: '2-digit', 
        month: '2-digit', 
        hour: '2-digit', 
        minute: '2-digit' 
    }) }}
</div>
                                </div>
                            </div>
                        </div>
                        <div
                            v-else
                            class="p-12 text-center flex flex-col items-center"
                        >
                            <i
                                class="pi pi-bell-slash text-slate-200 text-4xl mb-3"
                            ></i>
                            <p class="text-slate-400 text-sm">
                                Tout est à jour !
                            </p>
                        </div>
                    </div>

                    <div
                        v-if="$page.props.auth.notifications.length > 0"
                        class="p-3 bg-slate-50 text-center"
                    >
                        <Link
                            href="/notifications"
                            class="text-xs text-indigo-600 font-bold hover:text-indigo-800"
                        >
                            VOIR TOUT L'HISTORIQUE
                        </Link>
                    </div>
                </div>
            </OverlayPanel>

            <!-- Main Page Content -->
            <main class="p-8 flex-1">
                <!-- Conteneur pour limiter la largeur du contenu et le rendre plus lisible -->
                <div class="max-w-7xl mx-auto">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>

<style>
/* Custom PrimeVue Badge Style pour coller à l'esprit épuré */
.p-badge.p-badge-danger {
    background: #ef4444;
    min-width: 1.2rem;
    height: 1.2rem;
    line-height: 1.2rem;
    font-size: 0.65rem;
    font-weight: 800;
}

/* On lisse les transitions de l'OverlayPanel */
.p-overlaypanel {
    border-radius: 16px !important;
}
.p-overlaypanel:before,
.p-overlaypanel:after {
    display: none !important; /* On enlève la petite flèche pour plus de modernité */
}
</style>
