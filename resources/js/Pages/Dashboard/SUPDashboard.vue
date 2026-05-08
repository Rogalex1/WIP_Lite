<script setup>
import SUPLayout from '@/Layouts/SUPLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import StatCard from '@/Components/StatCard.vue';

defineOptions({ layout: SUPLayout });

defineProps({
    stats: Object,
    my_agents: Array
});
</script>

<template>
  <Head title="Tableau de bord SUP" />

  <div class="space-y-8">
    <div class="flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-black text-slate-800 tracking-tight uppercase tracking-tighter">Bonjour, {{ $page.props.auth.user.name }}</h1>
        <p class="text-slate-500">Supervision en temps réel de votre équipe.</p>
      </div>
      <Link :href="route('campaigns.index')" class="px-6 py-3 bg-[#FF7A1A] text-white rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-orange-100 hover:bg-slate-900 transition-all">
        Ma Campagne
      </Link>
    </div>

    <!-- Stats SUP -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <StatCard title="Agents Actifs" :value="stats.present_count" icon="pi-user-plus" iconBg="bg-orange-50" iconColor="text-orange-600" />
      <StatCard title="Total Team" :value="stats.total_team" icon="pi-users" iconBg="bg-slate-100" iconColor="text-slate-600" />
      <StatCard title="Production" value="100%" icon="pi-chart-line" iconBg="bg-orange-100" iconColor="text-orange-600" />
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Liste de ses agents -->
        <div class="xl:col-span-2 bg-white rounded-3xl border border-orange-100 shadow-xl shadow-orange-200/10 overflow-hidden">
            <div class="p-6 border-b border-orange-50 flex justify-between items-center bg-orange-50/20">
                <div class="flex items-center gap-3">
                    <div class="w-1.5 h-6 bg-[#FF7A1A] rounded-full"></div>
                    <h3 class="font-black text-slate-800 uppercase tracking-tighter">Ma Team en Production</h3>
                </div>
                <span class="text-[9px] font-black text-orange-400 italic uppercase tracking-[0.2em]">Live Status</span>
            </div>
            
            <div class="divide-y divide-orange-50">
                <div v-for="agent in my_agents" :key="agent.id" class="p-6 hover:bg-orange-50/30 transition-all flex items-center justify-between group">
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center font-black text-xs text-orange-600 shadow-sm group-hover:bg-orange-500 group-hover:text-white transition-all">
                                {{ agent.name.charAt(0).toUpperCase() }}
                            </div>
                            <div class="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-green-500 border-2 border-white rounded-full shadow-sm animate-pulse"></div>
                        </div>
                        <div>
                            <p class="text-sm font-black text-slate-800 uppercase tracking-tight">{{ agent.name }}</p>
                            <Link :href="route('campaigns.show', agent.campaign_id)" class="text-[10px] text-orange-400 font-bold uppercase tracking-widest mt-0.5 hover:text-orange-600 transition-colors underline decoration-orange-100">
                                {{ agent.last_action }}
                            </Link>
                        </div>
                    </div>
                    <button class="p-3 bg-white hover:bg-orange-500 border border-orange-100 rounded-xl transition-all text-orange-400 hover:text-white shadow-sm">
                        <i class="pi pi-chevron-right text-xs"></i>
                    </button>
                </div>

                <div v-if="my_agents.length === 0" class="text-center py-20 bg-white">
                    <i class="pi pi-users text-orange-50 text-6xl mb-4"></i>
                    <p class="text-slate-400 font-black uppercase tracking-widest text-xs">Aucun agent assigné</p>
                </div>
            </div>
        </div>

        <!-- Widget Planning du jour -->
        <div class="bg-orange-950 rounded-3xl p-8 text-white shadow-2xl shadow-orange-900/40 flex flex-col justify-between relative overflow-hidden group">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-orange-500/10 rounded-full blur-3xl group-hover:bg-orange-500/20 transition-all"></div>
            <div>
                <div class="w-12 h-12 bg-orange-500/20 rounded-xl flex items-center justify-center mb-8 border border-orange-500/30">
                    <i class="pi pi-calendar-plus text-orange-400 text-xl"></i>
                </div>
                <h4 class="text-xl font-black mb-3 tracking-tighter uppercase">Planning du jour</h4>
                <p class="text-orange-200/70 text-sm leading-relaxed mb-10 font-medium">
                    3 agents attendent la validation de leur pause déjeuner pour aujourd'hui.
                </p>
            </div>
            <button class="w-full py-4 bg-orange-500 hover:bg-white hover:text-orange-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all shadow-lg shadow-orange-900/20">
                Gérer les pauses
            </button>
        </div>
    </div>
  </div>
</template>