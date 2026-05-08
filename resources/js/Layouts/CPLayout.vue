<script setup>
import { ref, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import OverlayPanel from 'primevue/overlaypanel';
import Button from 'primevue/button';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';

const op = ref();
const page = usePage();
const toast = useToast();

// Surveiller les messages flash
watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) {
            toast.add({ severity: 'success', summary: 'Succès', detail: flash.success, life: 4000 });
        }
        if (flash?.error) {
            toast.add({ severity: 'error', summary: 'Erreur', detail: flash.error, life: 4000 });
        }
    },
    { deep: true, immediate: true }
);

const isActive = (route) => page.url.startsWith(route);
</script>

<template>
  <Toast position="top-center" />
  <div class="min-h-screen bg-slate-50 flex font-sans antialiased">
    
    <!-- Sidebar CP (Style Vert Émeraude pour différencier) -->
    <aside class="w-64 bg-emerald-950 text-emerald-100 flex-shrink-0 hidden md:flex flex-col">
      <div class="h-16 flex items-center px-6 border-b border-emerald-900/50">
        <span class="text-white font-black tracking-tighter text-xl italic">CP<span class="text-emerald-400">PANEL</span></span>
      </div>
      
      <nav class="flex-1 p-4 space-y-1">
        <p class="text-[10px] uppercase tracking-widest text-emerald-500 font-bold mb-4 px-3">Pilotage</p>
        
        <Link href="/cp/dashboard" 
              :class="[isActive('/cp/dashboard') ? 'bg-emerald-900 text-white' : 'hover:bg-emerald-900/50 hover:text-white']"
              class="flex items-center p-3 rounded-xl transition-all group">
          <i class="pi pi-th-large mr-3" :class="isActive('/cp/dashboard') ? 'text-emerald-400' : 'text-emerald-600'"></i>
          <span class="font-medium">Vue d'ensemble</span>
        </Link>

        <Link href="/cp/my-campaigns" 
              :class="[isActive('/cp/my-campaigns') ? 'bg-emerald-900 text-white' : 'hover:bg-emerald-900/50 hover:text-white']"
              class="flex items-center p-3 rounded-xl transition-all group">
          <i class="pi pi-briefcase mr-3" :class="isActive('/cp/my-campaigns') ? 'text-emerald-400' : 'text-emerald-600'"></i>
          <span class="font-medium">Mes Campagnes</span>
        </Link>

        <Link href="/cp/planning-validation" 
              :class="[isActive('/cp/planning-validation') ? 'bg-emerald-900 text-white' : 'hover:bg-emerald-900/50 hover:text-white']"
              class="flex items-center p-3 rounded-xl transition-all group">
          <i class="pi pi-check-square mr-3" :class="isActive('/cp/planning-validation') ? 'text-emerald-400' : 'text-emerald-600'"></i>
          <span class="font-medium">Validations</span>
        </Link>
      <Link href="/planning-models"
      :class="[isActive('/planning-models') ? 'bg-emerald-900 text-white' : 'hover:bg-emerald-900/50 hover:text-white']"
      class="flex items-center p-3 rounded-xl transition-all group">
  <i class="pi pi-table mr-3" :class="isActive('/planning-models') ? 'text-emerald-400' : 'text-emerald-600'"></i>
  <span class="font-medium">Modèles Planning</span>
</Link>

<Link href="/planning-assignments"
      :class="[isActive('/planning-assignments') ? 'bg-emerald-900 text-white' : 'hover:bg-emerald-900/50 hover:text-white']"
      class="flex items-center p-3 rounded-xl transition-all group">
  <i class="pi pi-users mr-3" :class="isActive('/planning-assignments') ? 'text-emerald-400' : 'text-emerald-600'"></i>
  <span class="font-medium">Affectations</span>
</Link>
      </nav>

      <div class="p-4 border-t border-emerald-900/50">
         <div class="bg-emerald-900/40 p-3 rounded-xl flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-500 flex items-center justify-center text-white font-bold text-xs uppercase">
               {{ $page.props.auth.user.name }}
            </div>
            <div class="overflow-hidden">
               <p class="text-xs font-bold text-white truncate">{{ $page.props.auth.user.name }}</p>
               <p class="text-[10px] text-emerald-400 truncate italic">Chef de Plateau</p>
            </div>
         </div>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
      <header class="h-16 bg-white flex items-center justify-between px-8 border-b border-slate-200">
        <h2 class="font-bold text-slate-800">Espace Opérationnel</h2>
        
        <div class="flex items-center gap-3">
          <Button type="button" icon="pi pi-bell" @click="(e) => op.toggle(e)" 
                  v-badge.danger="$page.props.auth.notifications.length || null" text plain />
          <div class="h-8 w-[1px] bg-slate-200"></div>
          <Link :href="route('logout')" method="post" as="button" class="text-xs font-bold text-rose-500 hover:text-rose-700">DÉCONNEXION</Link>
        </div>
      </header>

      <main class="p-8 flex-1">
         <slot />
      </main>
    </div>
  </div>
</template>
