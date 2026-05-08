<script setup>
import CPLayout from '@/Layouts/CPLayout.vue';
import { Head } from '@inertiajs/vue3';
import StatCard from '@/Components/StatCard.vue';

defineOptions({ layout: CPLayout });

defineProps({
    stats: Object,
    my_teams: Array, // 👈 employés affectés au CP
});
</script>

<template>
  <Head title="Dashboard CP" />

  <div class="space-y-8">

    <!-- HEADER -->
    <div>
      <h1 class="text-2xl font-black text-slate-800 tracking-tight">
        Tableau de bord Opérationnel
      </h1>
      <p class="text-slate-500">
        Gestion de vos équipes et suivi des affectations.
      </p>
    </div>

    <!-- STATS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <StatCard
        title="Mes Employés"
        :value="stats.employees_count"
        icon="pi-users"
        iconBg="bg-emerald-50"
        iconColor="text-emerald-600"
      />

      <StatCard
        title="Affectations actives"
        :value="stats.pending_count"
        icon="pi-sitemap"
        iconBg="bg-blue-50"
        iconColor="text-blue-600"
      />

      <StatCard
        title="Campagnes actives"
        :value="stats.campaigns_count"
        icon="pi-briefcase"
        iconBg="bg-purple-50"
        iconColor="text-purple-600"
      />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

      <!-- TEAM LIST -->
      <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">

        <h3 class="font-bold text-slate-800 mb-4">
          Mes Employés Affectés
        </h3>

        <div class="space-y-4">

          <div
            v-for="emp in my_teams"
            :key="emp.id"
            class="flex items-center justify-between p-3 bg-slate-50 rounded-xl hover:bg-emerald-50 transition-colors border border-transparent hover:border-emerald-100"
          >

            <div class="flex items-center gap-3">

              <!-- Avatar -->
              <div class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center font-bold text-emerald-700">
                {{ emp.first_name?.charAt(0) }}{{ emp.last_name?.charAt(0) }}
              </div>

              <div>
                <p class="text-sm font-bold text-slate-700">
                  {{ emp.first_name }} {{ emp.last_name }}
                </p>

                <p class="text-[10px] text-slate-400">
                  Poste : {{ emp.position?.name ?? 'Non défini' }}
                </p>
              </div>

            </div>

            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-100 px-2 py-1 rounded">
              AFFECTÉ
            </span>

          </div>

        </div>
      </div>

      <!-- WIDGET -->
      <div class="bg-emerald-600 rounded-2xl p-8 text-white relative overflow-hidden">

        <i class="pi pi-send absolute -right-6 -top-6 text-9xl text-white/10"></i>

        <h4 class="text-xl font-bold mb-2">
          Rappel Hebdomadaire
        </h4>

        <p class="text-emerald-100 mb-6 text-sm">
          N'oubliez pas de valider les affectations de la semaine prochaine avant vendredi 17h.
        </p>

        <button class="bg-white text-emerald-600 px-6 py-2 rounded-xl font-black text-xs uppercase shadow-lg">
          Consulter les plannings
        </button>

      </div>

    </div>
  </div>
</template>