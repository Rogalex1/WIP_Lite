<script setup>
import TClayout from '@/Layouts/TCLayout.vue';
import { Head } from '@inertiajs/vue3';
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
    }
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
      <div class="bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-sm flex items-center gap-3">
          <i class="pi pi-calendar text-amber-500"></i>
          <span class="text-sm font-bold text-slate-700">{{ new Date().toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long' }) }}</span>
      </div>
    </div>

    <!-- Stats TC -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <StatCard title="Heures ce mois" :value="my_stats.hours_done" icon="pi-clock" iconBg="bg-amber-50" iconColor="text-amber-600" />
      <StatCard title="Score Qualité" :value="my_stats.quality_score + '%'" icon="pi-star" iconBg="bg-blue-50" iconColor="text-blue-600" />
      <StatCard title="Congés restants" :value="my_stats.off_days" icon="pi-sun" iconBg="bg-emerald-50" iconColor="text-emerald-600" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Planning du Jour -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden">
            <div class="p-6 bg-slate-900 text-white">
                <h3 class="font-bold flex items-center gap-2">
                    <i class="pi pi-stopwatch"></i>
                    Planning du jour
                </h3>
            </div>
            <div class="p-8">
                <div class="relative border-l-2 border-slate-100 ml-4 space-y-12">
                    <!-- Début de journée -->
                    <div class="relative pl-8">
                        <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-white border-4 border-amber-500"></div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Matinée</p>
                        <p class="text-lg font-bold text-slate-800">{{ today_schedule.morning_start }} — {{ today_schedule.morning_end }}</p>
                        <p class="text-sm text-slate-500">Production Appels Entrants</p>
                    </div>

                    <!-- Pause Déjeuner -->
                    <div class="relative pl-8 bg-amber-50/50 py-4 rounded-r-2xl border-l-4 border-amber-500">
                        <div class="absolute -left-[11px] top-6 w-4 h-4 rounded-full bg-white border-4 border-amber-200"></div>
                        <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest">Pause Déjeuner</p>
                        <p class="text-lg font-bold text-amber-900">{{ today_schedule.lunch_break }}</p>
                    </div>

                    <!-- Après-midi -->
                    <div class="relative pl-8">
                        <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-white border-4 border-slate-300"></div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Après-midi</p>
                        <p class="text-lg font-bold text-slate-800">{{ today_schedule.afternoon_start }} — {{ today_schedule.afternoon_end }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Widget Aide / Support -->
        <div class="space-y-6">
            <div class="bg-amber-500 rounded-3xl p-6 text-white shadow-lg shadow-amber-200 relative overflow-hidden">
                <i class="pi pi-question-circle absolute -right-4 -top-4 text-7xl text-white/20"></i>
                <h4 class="font-bold mb-2">Une question ?</h4>
                <p class="text-xs text-amber-100 mb-4">Besoin de modifier votre planning ou de signaler une absence ?</p>
                <button class="w-full py-2 bg-white text-amber-600 rounded-xl text-xs font-black uppercase">Contacter mon SUP</button>
            </div>
        </div>
    </div>
  </div>
</template>