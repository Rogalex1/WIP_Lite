<script setup>
import TClayout from '@/Layouts/TCLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import StatCard from '@/Components/StatCard.vue';

defineOptions({ layout: TClayout });

const props = defineProps({
    my_stats: {
        type: Object,
        default: () => ({ hours_done: 0, quality_score: 0, off_days: 0 })
    },
    today_schedule: {
        type: Object,
        default: () => ({})
    },
    current_campaign: Object
});
</script>

<template>
  <Head title="Mon Espace" />

  <div class="space-y-8">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
      <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight">Ravi de vous voir, {{ $page.props.auth.user?.name ? $page.props.auth.user.name.split(' ')[0] : 'Collaborateur' }} !</h1>
        <p class="text-slate-500">Passez une excellente journée de production.</p>
      </div>
      <div v-if="current_campaign" class="bg-orange-50 px-6 py-3 rounded-2xl border border-orange-100 shadow-sm flex items-center gap-4">
          <div class="p-2 bg-white rounded-lg text-orange-500 shadow-sm">
            <i class="pi pi-flag-fill"></i>
          </div>
          <div>
            <p class="text-[10px] font-black text-orange-400 uppercase tracking-widest leading-none mb-1">Campagne Actuelle</p>
            <Link :href="route('campaigns.show', current_campaign.id)" class="text-sm font-black text-slate-800 hover:text-orange-600 transition-colors uppercase tracking-tight">{{ current_campaign.name }}</Link>
          </div>
      </div>
    </div>

    <!-- Stats TC -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <StatCard title="Heures ce mois" :value="my_stats.hours_done" icon="pi-clock" iconBg="bg-orange-50" iconColor="text-orange-600" />
      <StatCard title="Score Qualité" :value="my_stats.quality_score + '%'" icon="pi-star" iconBg="bg-orange-100" iconColor="text-orange-600" />
      <StatCard title="Congés restants" :value="my_stats.off_days" icon="pi-sun" iconBg="bg-slate-50" iconColor="text-slate-600" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Planning du Jour -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-orange-100 shadow-xl shadow-orange-200/20 overflow-hidden">
            <div class="p-6 bg-orange-950 text-white">
                <h3 class="font-black uppercase tracking-widest text-xs flex items-center gap-2">
                    <i class="pi pi-stopwatch text-orange-500"></i>
                    Planning de production
                </h3>
            </div>
            <div class="p-8">
                <div class="relative border-l-2 border-orange-50 ml-4 space-y-12">
                    <!-- Début de journée -->
                    <div class="relative pl-8">
                        <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-white border-4 border-orange-500"></div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Matinée</p>
                        <p class="text-lg font-black text-slate-800 uppercase tracking-tighter">{{ today_schedule.morning_start }} — {{ today_schedule.morning_end }}</p>
                        <p class="text-xs text-orange-500 font-bold uppercase">Production Appels Entrants</p>
                    </div>

                    <!-- Pause Déjeuner -->
                    <div class="relative pl-8 bg-orange-50/50 py-5 rounded-r-2xl border-l-4 border-orange-500">
                        <div class="absolute -left-[11px] top-6 w-4 h-4 rounded-full bg-white border-4 border-orange-200"></div>
                        <p class="text-[10px] font-black text-orange-600 uppercase tracking-[0.2em]">Pause Déjeuner</p>
                        <p class="text-xl font-black text-orange-900 tracking-tighter">{{ today_schedule.lunch_break }}</p>
                    </div>

                    <!-- Après-midi -->
                    <div class="relative pl-8">
                        <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-white border-4 border-slate-300"></div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Après-midi</p>
                        <p class="text-lg font-black text-slate-800 uppercase tracking-tighter">{{ today_schedule.afternoon_start }} — {{ today_schedule.afternoon_end }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Widget Aide / Support -->
        <div class="space-y-6">
            <div class="bg-orange-500 rounded-3xl p-8 text-white shadow-xl shadow-orange-200 relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:bg-white/20 transition-all"></div>
                <h4 class="font-black uppercase tracking-tighter text-xl mb-3">Besoin d'aide ?</h4>
                <p class="text-xs text-orange-100 mb-8 leading-relaxed font-medium">Une modification sur votre planning ? Signaler une absence ou un retard ?</p>
                <button class="w-full py-4 bg-orange-950 hover:bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] transition-all shadow-lg">Contacter mon SUP</button>
            </div>
        </div>
    </div>
  </div>
</template>