<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Top Navigation SUP -->
        <nav class="bg-amber-600 text-white p-4 flex justify-between">
            <div class="flex gap-8 items-center">
                <span class="font-bold">WIP Lite SUP</span>
                <Link href="/sup/dashboard" class="hover:underline">Contrôle</Link>
                <Link href="/approvals" class="hover:underline">Validations</Link>
               <Link href="/planning-models" class="hover:underline">Modèles Planning</Link>
               <Link href="/planning-assignments" class="hover:underline">Affectations</Link>
            </div>
            <Button icon="pi pi-sign-out" class="p-button-text text-white" />
        </nav>

        <div class="max-w-7xl mx-auto py-8 px-4">
            <slot />
        </div>
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
    
    <!-- Sidebar SUP (Style Bleu Azur) -->
    <aside class="w-64 bg-slate-800 text-slate-300 flex-shrink-0 hidden md:flex flex-col border-r border-slate-700">
      <div class="h-16 flex items-center px-6 border-b border-slate-700">
        <span class="text-white font-black tracking-tighter text-xl">SUP<span class="text-sky-400">CORE</span></span>
      </div>
      
      <nav class="flex-1 p-4 space-y-1">
        <p class="text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-4 px-3">Gestion Terrain</p>
        
        <Link href="/sup/dashboard" 
              :class="[isActive('/sup/dashboard') ? 'bg-sky-600 text-white shadow-lg shadow-sky-900/20' : 'hover:bg-slate-700 hover:text-white']"
              class="flex items-center p-3 rounded-xl transition-all group">
          <i class="pi pi-home mr-3 text-lg" :class="isActive('/sup/dashboard') ? 'text-white' : 'text-sky-400'"></i>
          <span class="font-medium">Dashboard</span>
        </Link>

        <Link href="/sup/my-agents" 
              :class="[isActive('/sup/my-agents') ? 'bg-sky-600 text-white' : 'hover:bg-slate-700 hover:text-white']"
              class="flex items-center p-3 rounded-xl transition-all group">
          <i class="pi pi-users mr-3 text-lg" :class="isActive('/sup/my-agents') ? 'text-white' : 'text-sky-400'"></i>
          <span class="font-medium">Mon Équipe</span>
        </Link>

        <Link href="/sup/weekly-planning" 
              :class="[isActive('/sup/weekly-planning') ? 'bg-sky-600 text-white' : 'hover:bg-slate-700 hover:text-white']"
              class="flex items-center p-3 rounded-xl transition-all group">
          <i class="pi pi-calendar mr-3 text-lg" :class="isActive('/sup/weekly-planning') ? 'text-white' : 'text-sky-400'"></i>
          <span class="font-medium">Planning Hebdo</span>
        </Link>
      </nav>

      <div class="p-4 border-t border-slate-700">
         <div class="flex items-center gap-3 px-2">
            <div class="w-8 h-8 rounded-full bg-sky-500 flex items-center justify-center text-white font-bold text-xs uppercase">
               {{ $page.props.auth.user?.name?.substring(0, 2) || 'SUP' }}
            </div>
            <div class="overflow-hidden">
               <p class="text-xs font-bold text-white truncate">{{ $page.props.auth.user.email }}</p>
               <span class="text-[9px] bg-sky-900 text-sky-300 px-1.5 py-0.5 rounded font-bold uppercase tracking-tighter">Superviseur</span>
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