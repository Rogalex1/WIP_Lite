<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import StatCard from '@/Components/StatCard.vue';
import Button from 'primevue/button'; // Optionnel pour les boutons pro

defineOptions({ layout: AdminLayout });

// On définit les props envoyées par le contrôleur
const props = defineProps({
    stats: Object,
    recent_assignments: Array
});
</script>

<template>
  <Head title="Tableau de Bord" />

  <div class="space-y-10">
    <!-- Header de la page -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight">Bonjour, Administrateur </h1>
        <p class="text-slate-500 mt-1">Voici ce qui se passe sur votre plateforme aujourd'hui.</p>
      </div>
      <div class="flex gap-3">
         <button class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-xl font-bold text-sm hover:bg-slate-50 transition-all">
            Exporter (.csv)
         </button>
         <button class="px-4 py-2 bg-indigo-600 text-white rounded-xl font-bold text-sm hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all">
            + Nouvelle Campagne
         </button>
      </div>
    </div>

    <!-- Section Statistiques -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <StatCard title="Employés" :value="stats.employees_count" icon="pi-users" iconBg="bg-indigo-50" iconColor="text-indigo-600" />
      <StatCard title="Campagnes actives" :value="stats.campaigns_count" icon="pi-flag" iconBg="bg-blue-50" iconColor="text-blue-600" />
      <StatCard title="En attente" :value="stats.pending_count" icon="pi-clock" iconBg="bg-amber-50" iconColor="text-amber-600" />
      <StatCard title="Alertes RH":value="stats.alerts_count" icon="pi-exclamation-circle" iconBg="bg-rose-50" iconColor="text-rose-600" />
    </div>

    <!-- Section Tableaux -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
      
      <!-- Liste des affectations (Prend 2 colonnes sur 3) -->
      <div class="xl:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-50 flex justify-between items-center">
          <h3 class="font-bold text-slate-800 text-lg">Dernières affectations</h3>
          <Link href="/admin/assignments" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors">VOIR TOUT</Link>
        </div>
        
        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead>
              <tr class="bg-slate-50/50 text-slate-400 text-[10px] uppercase tracking-widest font-black">
                <th class="px-6 py-4">Employé</th>
                <th class="px-6 py-4">Rôle</th>
                <th class="px-6 py-4">Campagne</th>
                <th class="px-6 py-4 text-right">Statut</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
      <!-- On boucle sur la liste des affectations envoyées par le contrôleur -->
<tr v-for="assign in recent_assignments" :key="assign.id" class="hover:bg-slate-50/50 transition-colors group">
  <td class="px-6 py-4 flex items-center gap-3">
    <!-- On génère les initiales dynamiquement -->
    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center font-bold text-[10px] text-indigo-700">
      {{ assign.employee_name.split(' ').map(n => n[0]).join('') }}
    </div>
    <!-- Le nom de l'employé devient dynamique -->
    <span class="text-sm font-bold text-slate-700">{{ assign.employee_name }}</span>
  </td>
  
  <!-- Le rôle dynamique -->
  <td class="px-6 py-4 text-sm text-slate-500 font-medium">
    {{ assign.role }}
  </td>
  
  <!-- La campagne dynamique -->
  <td class="px-6 py-4">
    <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-lg text-[11px] font-bold">
      {{ assign.campaign_name }}
    </span>
  </td>
  
  <td class="px-6 py-4 text-right">
    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[10px] font-black uppercase">
       <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
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
         <div class="bg-slate-900 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden group">
            <i class="pi pi-bolt absolute -right-4 -bottom-4 text-8xl text-white/5 rotate-12 group-hover:rotate-0 transition-transform duration-500"></i>
            <h4 class="font-bold text-lg mb-2">Besoin d'aide ?</h4>
            <p class="text-slate-400 text-sm mb-4 leading-relaxed">Consultez la documentation pour gérer les exports de plannings.</p>
            <button class="w-full py-2 bg-indigo-500 hover:bg-indigo-400 text-white rounded-xl text-sm font-bold transition-colors">
               Ouvrir le guide
            </button>
         </div>

         <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
            <h4 class="font-bold text-slate-800 mb-4">Activité récente</h4>
            <div class="space-y-4">
<div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
    <h4 class="font-bold text-slate-800 mb-4 text-sm flex items-center gap-2">
        <i class="pi pi-history text-indigo-500"></i>
        Activité récente
    </h4>
    
    <div class="space-y-6">
        <!-- On boucle sur les notifications réelles -->
        <div v-if="$page.props.auth.notifications.length > 0" 
             v-for="notif in $page.props.auth.notifications.slice(0, 4)" 
             :key="notif.id" 
             class="flex gap-3 items-start group">
            
            <!-- Point indicateur dynamique (Indigo si non lu) -->
            <div :class="[notif.read_at ? 'bg-slate-200' : 'bg-indigo-500']" 
                 class="w-2 h-2 rounded-full mt-1.5 shrink-0 shadow-sm"></div>
            
            <div class="flex-1">
                <p class="text-xs text-slate-700 font-medium leading-snug group-hover:text-indigo-600 transition-colors">
                    {{ notif.data.message }}
                </p>
                <!-- Formatage de la date (ex: "Il y a 2 heures" ou Date locale) -->
                <p class="text-[10px] text-slate-400 mt-1 flex items-center">
                    <i class="pi pi-clock mr-1 text-[9px]"></i>
                    {{ new Date(notif.created_at).toLocaleDateString() }}
                </p>
            </div>
        </div>

        <!-- Cas où il n'y a aucune activité -->
        <div v-else class="py-4 text-center">
            <p class="text-[11px] text-slate-400 italic">Aucun mouvement récent.</p>
        </div>
    </div>

    <!-- Petit bouton pour voir l'historique complet -->
    <div class="mt-6 pt-4 border-t border-slate-50">
        <Link href="/notifications" class="text-[10px] font-black text-indigo-500 hover:text-indigo-700 uppercase tracking-tighter">
            Consulter l'historique complet →
        </Link>
    </div>
</div>
            </div>
         </div>
      </div>

    </div>
  </div>
</template>

<style scoped></style>
