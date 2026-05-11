<script setup>
import CPLayout from '@/Layouts/CPLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import StatCard from '@/Components/StatCard.vue';

defineOptions({ layout: CPLayout });

defineProps({
    stats: Object,
    my_teams: Array
});
</script>

<template>
  <Head title="Dashboard CP" />

  <div class="space-y-8">
    <div class="flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-black text-slate-800 tracking-tight">Tableau de bord Opérationnel</h1>
        <p class="text-slate-500">Gestion de vos équipes et suivi des affectations.</p>
      </div>
      <Link :href="route('campaigns.index')" class="px-6 py-3 bg-[#FF7A1A] text-white rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-orange-100 hover:bg-slate-900 transition-all">
        Suivi des campagnes
      </Link>
      <Link :href="route('reporting.index')" class="px-6 py-3 bg-slate-800 text-white rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg hover:bg-slate-900 transition-all">
        Reporting Production
      </Link>
    </div>

    <!-- Stats CP -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <StatCard title="Mes Équipes" :value="stats.teams_count" icon="pi-users" iconBg="bg-orange-50" iconColor="text-orange-600" />
      <StatCard title="Campagnes" :value="stats.campaigns_count" icon="pi-flag" iconBg="bg-orange-100" iconColor="text-orange-600" />
      <StatCard title="Objectif Atteint" value="85%" icon="pi-chart-bar" iconBg="bg-slate-50" iconColor="text-slate-600" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Liste de ses superviseurs / agents -->
        <div class="bg-white rounded-3xl border border-orange-100 shadow-xl shadow-orange-200/10 p-8">
            <h3 class="font-black text-slate-800 mb-6 uppercase tracking-tighter text-lg">Mes Superviseurs Actifs</h3>
            <div class="space-y-4">
                <div v-for="sup in my_teams" :key="sup.id" class="flex items-center justify-between p-4 bg-orange-50/20 rounded-2xl hover:bg-orange-50 transition-all border border-transparent hover:border-orange-100 group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-white border border-orange-100 shadow-sm flex items-center justify-center font-black text-orange-600 group-hover:bg-orange-500 group-hover:text-white transition-all">
                            {{ sup.initials }}
                        </div>
                        <div>
                            <p class="text-sm font-black text-slate-800 uppercase tracking-tight">{{ sup.name }}</p>
                            <Link :href="route('campaigns.show', sup.campaign_id)" class="text-[10px] text-orange-400 font-bold uppercase tracking-widest mt-0.5 hover:text-orange-600 transition-colors underline decoration-orange-200">
                                Campagne : {{ sup.campaign }}
                            </Link>
                        </div>
                    </div>
                    <span class="text-[9px] font-black text-orange-600 bg-orange-100 px-3 py-1.5 rounded-lg border border-orange-200 tracking-widest">EN LIGNE</span>
                </div>
                
                <div v-if="my_teams.length === 0" class="text-center py-10">
                    <i class="pi pi-users text-slate-100 text-4xl mb-3"></i>
                    <p class="text-sm text-slate-400 font-medium">Aucun superviseur actif</p>
                </div>
            </div>
        </div>

        <!-- Widget de rappel -->
        <div class="bg-orange-950 rounded-3xl p-10 text-white relative overflow-hidden shadow-2xl shadow-orange-900/40 group">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-orange-500/10 rounded-full blur-3xl group-hover:bg-orange-500/20 transition-all"></div>
            <i class="pi pi-calendar absolute -right-6 -bottom-6 text-9xl text-white/5"></i>
            <h4 class="text-2xl font-black mb-3 tracking-tighter uppercase">Rappel Hebdomadaire</h4>
            <p class="text-orange-200/70 mb-8 text-sm font-medium">N'oubliez pas de valider les affectations de la semaine prochaine avant vendredi 17h.</p>
            <button class="bg-orange-500 hover:bg-white hover:text-orange-600 text-white px-8 py-3 rounded-xl font-black text-[10px] uppercase shadow-lg shadow-orange-900/20 transition-all tracking-[0.2em]">
                Consulter les plannings
            </button>
        </div>
    </div>
  </div>
</template>
