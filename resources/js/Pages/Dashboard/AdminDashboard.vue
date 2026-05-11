<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import StatCard from "@/Components/StatCard.vue";
import Button from "primevue/button"; // Optionnel pour les boutons pro

defineOptions({ layout: AdminLayout });

// On définit les props envoyées par le contrôleur
const props = defineProps({
    stats: Object,
    recent_assignments: Array,
    recent_history: Array,
});

const getActionLabel = (type) => {
    const types = {
        'assign': 'Affectation',
        'release': 'Libération',
        'reassign': 'Réaffectation'
    };
    return types[type] || type;
};
</script>

<template>
    <Head title="Tableau de Bord" />

    <div class="space-y-10">
        <!-- Header de la page -->
        <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-4"
        >
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight">
                    Bonjour, Administrateur
                </h1>
                <p class="text-sm font-medium text-slate-400">
                    Espace d'administration centralisé.
                </p>
            </div>
            <div class="flex gap-3">
                <Link
                    :href="route('notifications.index')"
                    class="px-4 py-2 bg-[#FF7A1A] text-white rounded-xl font-bold text-sm hover:bg-[#e66a12] transition-all shadow-sm flex items-center gap-2"
                >
                    <i class="pi pi-bell text-sm"></i>
                    Notifications
                </Link>
                <button
                    class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-xl font-bold text-sm hover:bg-slate-50 transition-all shadow-sm"
                >
                    Exporter (.csv)
                </button>
                <Link
                    :href="route('campaigns.index')"
                    class="px-4 py-2 bg-[#FF7A1A] text-white rounded-xl font-black text-sm hover:bg-slate-900 shadow-lg shadow-orange-100 transition-all uppercase tracking-widest flex items-center gap-2"
                >
                    <i class="pi pi-plus text-[10px]"></i>
                    Nouvelle Campagne
                </Link>
                <Link
                    :href="route('reporting.index')"
                    class="px-4 py-2 bg-slate-800 text-white rounded-xl font-black text-sm hover:bg-slate-900 transition-all uppercase tracking-widest flex items-center gap-2"
                >
                    <i class="pi pi-chart-bar text-sm"></i>
                    Reporting
                </Link>
            </div>
        </div>

        <!-- Section Statistiques -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <StatCard
                title="Employés"
                :value="stats.employees_count"
                icon="pi-users"
                iconBg="bg-orange-50"
                iconColor="text-orange-600"
            />
            <StatCard
                title="Campagnes actives"
                :value="stats.campaigns_count"
                icon="pi-flag"
                iconBg="bg-orange-100"
                iconColor="text-orange-600"
            />
            <StatCard
                title="Affectations"
                :value="stats.pending_count"
                icon="pi-link"
                iconBg="bg-slate-100"
                iconColor="text-slate-600"
            />
            <StatCard
                title="Alerte Vivier"
                :value="stats.alerts_count"
                icon="pi-exclamation-circle"
                iconBg="bg-red-50"
                iconColor="text-red-600"
            />
        </div>

        <!-- Section Tableaux -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            <!-- Liste des affectations (Prend 2 colonnes sur 3) -->
            <div
                class="xl:col-span-2 bg-white rounded-3xl border border-orange-100 shadow-xl shadow-orange-200/10 overflow-hidden"
            >
                <div
                    class="p-8 border-b border-orange-50 flex justify-between items-center"
                >
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-6 bg-[#FF7A1A] rounded-full"></div>
                        <h3 class="font-black text-slate-800 text-lg uppercase tracking-tighter">
                            Dernières affectations
                        </h3>
                    </div>
                    <Link
                        :href="route('assignments.index')"
                        class="text-[10px] font-black text-orange-600 hover:text-slate-900 transition-colors uppercase tracking-widest"
                        >VOIR TOUT</Link
                    >
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr
                                class="bg-orange-50/30 text-slate-400 text-[10px] uppercase tracking-[0.2em] font-black"
                            >
                                <th class="px-8 py-4">Employé</th>
                                <th class="px-8 py-4">Rôle</th>
                                <th class="px-8 py-4">Campagne</th>
                                <th class="px-8 py-4 text-right">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-orange-50">
                            <!-- On boucle sur la liste des affectations envoyées par le contrôleur -->
                            <tr
                                v-for="assign in recent_assignments"
                                :key="assign.id"
                                class="hover:bg-orange-50/20 transition-colors group"
                            >
                                <td class="px-8 py-5 flex items-center gap-4">
                                    <!-- On génère les initiales dynamiquement -->
                                    <div
                                        class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center font-black text-xs text-orange-600 shadow-sm"
                                    >
                                        {{
                                            assign.employee_name
                                                .split(" ")
                                                .map((n) => n[0])
                                                .join("")
                                        }}
                                    </div>
                                    <!-- Le nom de l'employé devient dynamique -->
                                    <span
                                        class="text-sm font-black text-slate-700 uppercase tracking-tight"
                                        >{{ assign.employee_name }}</span
                                    >
                                </td>

                                <!-- Le rôle dynamique -->
                                <td
                                    class="px-8 py-5 text-xs text-slate-500 font-bold uppercase"
                                >
                                    {{ assign.role }}
                                </td>

                                <!-- La campagne dynamique -->
                                <td class="px-8 py-5">
                                    <span
                                        class="px-3 py-1 bg-orange-50 text-orange-600 border border-orange-100 rounded-lg text-[10px] font-black uppercase tracking-tight"
                                    >
                                        {{ assign.campaign_name }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[10px] font-black uppercase"
                                    >
                                        <span
                                            class="w-1.5 h-1.5 rounded-full bg-emerald-500"
                                        ></span>
                                        {{ assign.status }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Widget Latéral (Prend 1 colonne) -->
            <div class="space-y-6">
                <div
                    class="bg-white rounded-3xl border border-orange-100 p-8 shadow-xl shadow-orange-200/10"
                >
                    <h4 class="font-black text-slate-800 mb-8 text-sm flex items-center gap-2 uppercase tracking-tighter">
                        <i class="pi pi-history text-orange-500"></i>
                        Historique des mouvements
                    </h4>

                    <div class="space-y-6">
                        <template v-if="recent_history.length > 0">
                            <div
                                v-for="log in recent_history"
                                :key="log.id"
                                class="flex gap-4 items-start group"
                            >
                                <div
                                    :class="[
                                        log.action === 'assign' ? 'bg-emerald-500' : 
                                        log.action === 'release' ? 'bg-red-500' : 'bg-orange-500'
                                    ]"
                                    class="w-2 h-2 rounded-full mt-1.5 shrink-0 shadow-sm"
                                ></div>

                                <div class="flex-1">
                                    <div class="flex justify-between items-start mb-1">
                                        <p class="text-xs text-slate-700 font-black uppercase tracking-tight">
                                            {{ log.employee_name }}
                                        </p>
                                        <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest bg-slate-50 px-1.5 py-0.5 rounded">
                                            {{ log.date }}
                                        </span>
                                    </div>
                                    <p class="text-[10px] text-slate-500 font-bold leading-snug group-hover:text-orange-600 transition-colors uppercase">
                                        {{ getActionLabel(log.action) }} - {{ log.campaign }}
                                    </p>
                                    <p class="text-[9px] text-slate-400 mt-1 flex items-center font-medium">
                                        Par {{ log.author }}
                                    </p>
                                </div>
                            </div>
                        </template>

                        <div v-else class="py-10 text-center border-2 border-dashed border-slate-50 rounded-2xl">
                            <i class="pi pi-inbox text-slate-200 text-3xl mb-2"></i>
                            <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest">
                                Aucun mouvement récent.
                            </p>
                        </div>
                    </div>

                    <div class="mt-10 pt-6 border-t border-orange-50">
                        <Link
                            :href="route('admin.history')"
                            class="text-[10px] font-black text-orange-600 hover:text-slate-900 transition-colors uppercase tracking-[0.2em]"
                        >
                            Historique complet →
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
