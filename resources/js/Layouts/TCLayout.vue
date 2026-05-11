<script setup>
import { watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
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
    
    <!-- Sidebar TC (Style WIP Orange) -->
    <aside class="w-64 bg-white text-slate-600 flex-shrink-0 hidden md:flex flex-col border-r border-slate-200">
      <div class="h-16 flex items-center px-6 border-b border-slate-100">
        <span class="text-slate-900 font-black tracking-tighter text-xl">MY<span class="text-[#FF7A1A]">SPACE</span></span>
      </div>
      
      <nav class="flex-1 p-6 space-y-2">
        <Link href="/tc/dashboard" 
              :class="[isActive('/tc/dashboard') ? 'bg-orange-50 text-[#FF7A1A]' : 'hover:bg-slate-50 text-slate-500']"
              class="flex items-center p-3 rounded-xl transition-all font-bold group">
          <i class="pi pi-home mr-3 text-lg" :class="isActive('/tc/dashboard') ? 'text-[#FF7A1A]' : 'text-slate-400'"></i>
          Mon Tableau de bord
        </Link>

        <Link
            :href="route('planning-assignments.index')"
            :class="[isActive('planning-assignments.index') ? 'bg-orange-50 text-[#FF7A1A]' : 'hover:bg-slate-50 text-slate-500']"
            class="flex items-center p-3 rounded-xl transition-all font-bold group">
 <i class="pi pi-table mr-3"></i>
           Mon plannig
        </Link>

        <Link :href="route('reporting.index')"
              :class="[isActive('reporting.index') ? 'bg-orange-50 text-[#FF7A1A]' : 'hover:bg-slate-50 text-slate-500']"
              class="flex items-center p-3 rounded-xl transition-all font-bold group">
          <i class="pi pi-chart-bar mr-3 text-lg"></i>
          Mon Reporting
        </Link>

        <Link href="/timesheets"
              :class="[isActive('/timesheets') ? 'bg-orange-50 text-[#FF7A1A]' : 'hover:bg-slate-50 text-slate-500']"
              class="flex items-center p-3 rounded-xl transition-all font-bold group">
<i class="pi pi-clock mr-3 text-lg"></i>
          Mes Timesheets
</Link>
      </nav>

      <div class="p-6">
         <div class="bg-[#FF7A1A] rounded-2xl p-4 text-white shadow-lg shadow-orange-900/20">
            <p class="text-[10px] uppercase font-black text-orange-100 mb-1">Campagne actuelle</p>
            <p class="text-xs font-bold truncate">Service Client - Orange</p>
         </div>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
      <header class="h-16 bg-white flex items-center justify-between px-8 border-b border-slate-100">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Session Active</span>
        </div>
        
        <div class="flex items-center gap-4">
          <Button type="button" icon="pi pi-bell" @click="(e) => op.toggle(e)" 
                  v-badge.danger="$page.props.auth.notifications.length || null" text plain 
                  class="!text-slate-400" />
          
          <div class="h-8 w-[1px] bg-slate-100"></div>
          
          <div class="flex items-center gap-3">
             <div class="text-right hidden sm:block">
                <p class="text-xs font-bold text-slate-800">{{ $page.props.auth.user?.email }}</p>
                <p class="text-[10px] text-[#FF7A1A] font-medium">Téléconseiller</p>
             </div>
             <div class="w-10 h-10 rounded-full bg-orange-100 border-2 border-white shadow-sm flex items-center justify-center font-bold text-[#FF7A1A]">
                {{ $page.props.auth.user?.name?.charAt(0) || '?' }}
             </div>
          </div>

        </div>

            <Link :href="route('logout')"  method="post" as="button" class="p-2 hover:bg-rose-50 rounded-lg group transition-colors">
            <i class="pi pi-power-off text-slate-400 group-hover:text-rose-500">DECONNEXION</i>
          </Link>
      </header>

      <main class="p-8 flex-1 bg-[url('/img/grid.svg')] bg-center">
         <div class="max-w-5xl mx-auto">
            <slot />
         </div>
      </main>
    </div>
  </div>
</template>