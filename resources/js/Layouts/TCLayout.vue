<template>
    <div class="min-h-screen flex flex-col bg-sky-50">
        <header class="bg-sky-700 text-white p-4 shadow-md">
            <h1 class="text-center font-semibold">Ma Liste de Travail</h1>
        </header>

        <main class="flex-1 p-4 sm:p-8">
            <slot />
        </main>

        <footer class="bg-white border-t p-4 flex justify-around">
            <Link href="/tc/dashboard" class="text-sky-700 flex flex-col items-center">
                <i class="pi pi-home"></i><span class="text-xs">Accueil</span>
            </Link>
            <Link href="/tc/tasks" class="text-gray-500 flex flex-col items-center">
                <i class="pi pi-check-square"></i><span class="text-xs">Tâches</span>
            </Link>
          <Link href="/tc/planning" class="text-gray-500 flex flex-col items-center">
    <i class="pi pi-calendar"></i><span class="text-xs">Mon Planning</span>
</Link>
        </footer>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import OverlayPanel from 'primevue/overlaypanel';
import Button from 'primevue/button';

const op = ref();
const page = usePage();
const isActive = (route) => page.url.startsWith(route);
</script>

<template>
  <div class="min-h-screen bg-slate-50 flex font-sans antialiased">
    
    <!-- Sidebar TC (Style Ambre/Chaleureux) -->
    <aside class="w-64 bg-white text-slate-600 flex-shrink-0 hidden md:flex flex-col border-r border-slate-200">
      <div class="h-16 flex items-center px-6 border-b border-slate-100">
        <span class="text-slate-900 font-black tracking-tighter text-xl">MY<span class="text-amber-500">SPACE</span></span>
      </div>
      
      <nav class="flex-1 p-6 space-y-2">
        <Link href="/tc/dashboard" 
              :class="[isActive('/tc/dashboard') ? 'bg-amber-50 text-amber-700' : 'hover:bg-slate-50 text-slate-500']"
              class="flex items-center p-3 rounded-xl transition-all font-bold group">
          <i class="pi pi-home mr-3 text-lg" :class="isActive('/tc/dashboard') ? 'text-amber-500' : 'text-slate-400'"></i>
          Mon Tableau de bord
        </Link>

        <Link href="/tc/my-planning" 
              :class="[isActive('/tc/my-planning') ? 'bg-amber-50 text-amber-700' : 'hover:bg-slate-50 text-slate-500']"
              class="flex items-center p-3 rounded-xl transition-all font-bold group">
          <i class="pi pi-calendar mr-3 text-lg" :class="isActive('/tc/my-planning') ? 'text-amber-500' : 'text-slate-400'"></i>
          Mon Planning
        </Link>
      </nav>

      <div class="p-6">
         <div class="bg-slate-900 rounded-2xl p-4 text-white shadow-lg">
            <p class="text-[10px] uppercase font-black text-slate-500 mb-1">Campagne actuelle</p>
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
                <p class="text-[10px] text-amber-600 font-medium">Téléconseiller</p>
             </div>
             <div class="w-10 h-10 rounded-full bg-amber-100 border-2 border-white shadow-sm flex items-center justify-center font-bold text-amber-700">
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