<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import CPLayout from '@/Layouts/CPLayout.vue';
import SUPLayout from '@/Layouts/SUPLayout.vue';
import TCLayout from '@/Layouts/TCLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    campaign: Object
});

const page = usePage();
const userRole = computed(() => page.props.auth.user.role.name);

// Déterminer le layout selon le rôle
const currentLayout = computed(() => {
    switch(userRole.value) {
        case 'admin': return AdminLayout;
        case 'cp': return CPLayout;
        case 'sup': return SUPLayout;
        case 'tc': return TCLayout;
        default: return AuthenticatedLayout;
    }
});
</script>

<template>
    <Head :title="`Détails - ${campaign.name}`" />

    <component :is="currentLayout">
        <div class="p-8">
            <div class="flex justify-between items-center py-2 max-w-4xl mx-auto mb-6">
                <div class="flex items-center gap-4">
                    <Link :href="route('campaigns.index')" class="text-orange-400 hover:text-orange-600 transition-colors bg-white p-2 rounded-xl shadow-sm border border-orange-100">
                        <i class="pi pi-arrow-left text-lg"></i>
                    </Link>
                    <h2 class="text-xl font-black text-slate-800 tracking-tighter uppercase">Détails de la campagne</h2>
                </div>
                <div class="flex gap-2">
                    <span :class="['px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border', 
                        campaign.status === 'active' ? 'bg-orange-100 text-orange-600 border-orange-200' : 'bg-slate-100 text-slate-500 border-slate-200']">
                        {{ campaign.status }}
                    </span>
                </div>
            </div>

            <div class="max-w-4xl mx-auto">
                <div class="bg-white rounded-3xl border border-orange-100 shadow-xl shadow-orange-200/20 overflow-hidden transition-all">
                    <!-- Bannière de titre -->
                    <div class="bg-orange-950 p-10 text-white relative">
                        <div class="absolute top-0 right-0 p-8 opacity-10">
                            <i class="pi pi-flag text-8xl"></i>
                        </div>
                        <h1 class="text-4xl font-black tracking-tighter uppercase mb-4 relative z-10">{{ campaign.name }}</h1>
                        <div class="flex flex-wrap gap-6 text-orange-200 text-[10px] font-black uppercase tracking-[0.2em] relative z-10">
                            <div class="flex items-center gap-2 bg-orange-900/50 px-3 py-1.5 rounded-lg border border-orange-800">
                                <i class="pi pi-calendar text-orange-400"></i>
                                Du {{ campaign.start_date }} au {{ campaign.end_date }}
                            </div>
                            <div class="flex items-center gap-2 bg-orange-900/50 px-3 py-1.5 rounded-lg border border-orange-800">
                                <i class="pi pi-tag text-orange-400"></i>
                                ID: #00{{ campaign.id }}
                            </div>
                        </div>
                    </div>

                    <!-- Contenu -->
                    <div class="p-10">
                        <div class="mb-12">
                            <div class="flex items-center gap-2 mb-4">
                                <div class="w-1.5 h-4 bg-orange-500 rounded-full"></div>
                                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Description du projet</h4>
                            </div>
                            <p class="text-slate-600 leading-relaxed text-lg font-medium italic bg-orange-50/30 p-6 rounded-2xl border border-orange-100/50">
                                {{ campaign.description || 'Aucune description détaillée fournie pour cette campagne.' }}
                            </p>
                        </div>

                        <hr class="border-orange-50 mb-10" />

                        <!-- Statistiques ou infos complémentaires -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="p-6 bg-white rounded-2xl border border-orange-100 shadow-sm hover:border-orange-300 transition-colors group">
                                <span class="block text-[9px] font-black text-slate-400 uppercase mb-2 tracking-widest group-hover:text-orange-500">Date de création</span>
                                <span class="text-slate-800 font-bold text-sm">{{ new Date(campaign.created_at).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' }) }}</span>
                            </div>
                            <div class="p-6 bg-white rounded-2xl border border-orange-100 shadow-sm hover:border-orange-300 transition-colors group">
                                <span class="block text-[9px] font-black text-slate-400 uppercase mb-2 tracking-widest group-hover:text-orange-500">Dernière mise à jour</span>
                                <span class="text-slate-800 font-bold text-sm">{{ new Date(campaign.updated_at).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' }) }}</span>
                            </div>
                            <div class="p-6 bg-orange-50 rounded-2xl border border-orange-200 shadow-sm hover:bg-orange-100 transition-colors group">
                                <span class="block text-[9px] font-black text-orange-400 uppercase mb-2 tracking-widest group-hover:text-orange-600">Priorité</span>
                                <span class="text-orange-600 font-black uppercase text-sm tracking-tighter">Haute Performance</span>
                            </div>
                        </div>
                        
                        <!-- Liste des assignations (si CP ou Admin) -->
                        <div v-if="campaign.assignments && campaign.assignments.length > 0" class="mt-12">
                            <div class="flex items-center gap-2 mb-6">
                                <div class="w-1.5 h-4 bg-orange-500 rounded-full"></div>
                                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Équipe assignée</h4>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div v-for="as in campaign.assignments" :key="as.id" class="flex items-center justify-between p-4 bg-white border border-orange-100 rounded-2xl hover:shadow-md transition-all">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 font-bold text-xs">
                                            {{ as.employee.first_name.charAt(0) }}
                                        </div>
                                        <div>
                                            <p class="text-xs font-black text-slate-800 uppercase">{{ as.employee.first_name }} {{ as.employee.last_name }}</p>
                                            <p class="text-[9px] text-orange-500 font-bold uppercase">{{ as.position_id === 1 ? 'CP' : as.position_id === 2 ? 'SUP' : 'TC' }}</p>
                                        </div>
                                    </div>
                                    <div v-if="as.manager" class="text-right">
                                        <p class="text-[8px] text-slate-400 uppercase font-bold">Manager</p>
                                        <p class="text-[10px] text-slate-600 font-bold">{{ as.manager.first_name }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </component>
</template>