<script setup>
import { watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';

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

const isActive = (route) => {
    return window.location.pathname.startsWith(route);
};
</script>



<template>
  <div class="min-h-screen bg-slate-50 flex font-sans antialiased">
    
    <!-- Sidebar SUP (Style Orange WIP) -->
    <aside class="w-64 bg-slate-800 text-slate-300 flex-shrink-0 hidden md:flex flex-col border-r border-slate-700">
      <div class="h-16 flex items-center px-6 border-b border-slate-700">
        <span class="text-white font-black tracking-tighter text-xl">SUP<span class="text-[#FF7A1A]">CORE</span></span>
      </div>
      
      <nav class="flex-1 p-4 space-y-1">
        <p class="text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-4 px-3">Gestion Terrain</p>
        
        <Link href="/sup/dashboard" 
              :class="[isActive('/sup/dashboard') ? 'bg-[#FF7A1A] text-white shadow-lg shadow-orange-900/20' : 'hover:bg-slate-700 hover:text-white']"
              class="flex items-center p-3 rounded-xl transition-all group">
          <i class="pi pi-home mr-3 text-lg" :class="isActive('/sup/dashboard') ? 'text-white' : 'text-[#FF7A1A]'"></i>
          <span class="font-medium">Dashboard</span>
        </Link>

        <Link href="/sup/my-agents" 
              :class="[isActive('/sup/my-agents') ? 'bg-[#FF7A1A] text-white' : 'hover:bg-slate-700 hover:text-white']"
              class="flex items-center p-3 rounded-xl transition-all group">
          <i class="pi pi-users mr-3 text-lg" :class="isActive('/sup/my-agents') ? 'text-white' : 'text-[#FF7A1A]'"></i>
          <span class="font-medium">Mon Équipe</span>
        </Link>

        <Link :href="route('planning-assignments.index')"
              :class="[isActive('/sup/weekly-planning') ? 'bg-[#FF7A1A] text-white' : 'hover:bg-slate-700 hover:text-white']"
              class="flex items-center p-3 rounded-xl transition-all group">
          <i class="pi pi-calendar mr-3 text-lg" :class="isActive('/sup/weekly-planning') ? 'text-white' : 'text-[#FF7A1A]'"></i>
          <span class="font-medium">Planning Hebdo</span>
        </Link>

        <Link href="/reporting" 
              :class="[isActive('/reporting') ? 'bg-[#FF7A1A] text-white' : 'hover:bg-slate-700 hover:text-white']"
              class="flex items-center p-3 rounded-xl transition-all group">
          <i class="pi pi-chart-bar mr-3 text-lg" :class="isActive('/reporting') ? 'text-white' : 'text-[#FF7A1A]'"></i>
          <span class="font-medium">Reporting</span>
        </Link>

        <Link href="/timesheets" 
              :class="[isActive('/timesheets') ? 'bg-[#FF7A1A] text-white' : 'hover:bg-slate-700 hover:text-white']"
              class="flex items-center p-3 rounded-xl transition-all group">
<i class="pi pi-clock mr-3 text-lg" :class="isActive('/timesheets') ? 'text-white' : 'text-[#FF7A1A]'"></i>
<span class="font-medium">Timesheets</span>
</Link>
      </nav>

      <div class="p-4 border-t border-slate-700">
         <div class="flex items-center gap-3 px-2">
            <div class="w-8 h-8 rounded-full bg-[#FF7A1A] flex items-center justify-center text-white font-bold text-xs uppercase">
               {{ $page.props.auth.user?.name?.substring(0, 2) || 'SUP' }}
            </div>
            <div class="overflow-hidden">
               <p class="text-xs font-bold text-white truncate">{{ $page.props.auth.user.email }}</p>
               <span class="text-[9px] bg-orange-900 text-orange-200 px-1.5 py-0.5 rounded font-bold uppercase tracking-tighter">Superviseur</span>
            </div>
         </div>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
      <header class="h-16 bg-white/80 backdrop-blur-md sticky top-0 z-10 flex items-center justify-between px-8 border-b border-slate-200">
        <h2 class="font-bold text-slate-800 tracking-tight italic">Espace Superviseur</h2>
        
        <div class="flex items-center gap-4">
          <Button type="button" icon="pi pi-bell" @click="(e) => op.toggle(e)" 
                  v-badge.danger="$page.props.auth.notifications.length || null" text plain 
                  class="!text-slate-400 hover:!bg-slate-100" />
          
          <div class="h-8 w-[1px] bg-slate-200"></div>
          
          <Link :href="route('logout')"  method="post" as="button" class="p-2 hover:bg-rose-50 rounded-lg group transition-colors">
            <i class="pi pi-power-off text-slate-400 group-hover:text-rose-500">DECONNEXION</i>
          </Link>
        </div>
      </header>

      <main class="p-8 flex-1">
         <div class="max-w-6xl mx-auto">
            <slot />
         </div>
      </main>
    </div>
  </div>
</template>