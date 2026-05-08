<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
import Toast from "primevue/toast";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";

const page = usePage();

const isRouteActive = (routeName) => {
    return route().current(routeName);
};
</script>

<template>
    <div class="min-h-screen bg-slate-50 flex font-sans antialiased">
        <Toast />
        <!-- Sidebar CP -->
        <aside
            class="w-72 bg-orange-950 text-orange-100 flex-shrink-0 hidden lg:flex flex-col shadow-2xl border-r border-orange-900/30"
        >
            <div class="h-24 flex items-center px-8 border-b border-orange-900/50">
                <div class="flex items-center gap-3">
                    <ApplicationLogo size="sm" class="shadow-none rounded-lg" />
                    <span class="text-white font-black tracking-tighter text-2xl uppercase">
                        CP<span class="text-orange-400">Panel</span>
                    </span>
                </div>
            </div>

            <nav class="flex-1 p-6 space-y-2 custom-scrollbar overflow-y-auto">
                <p
                    class="text-[10px] uppercase tracking-[0.2em] text-orange-500/70 font-black mb-6 px-4"
                >
                    Pilotage Opérationnel
                </p>

                <!-- Dashboard -->
                <Link
                    :href="route('cp.dashboard')"
                    :class="[
                        isRouteActive('cp.dashboard')
                            ? 'bg-orange-600 text-white shadow-lg shadow-orange-950/50'
                            : 'hover:bg-orange-900/50 hover:text-white',
                    ]"
                    class="flex items-center p-4 rounded-2xl transition-all duration-300 group"
                >
                    <i
                        class="pi pi-th-large mr-4 text-xl"
                        :class="
                            isRouteActive('cp.dashboard')
                                ? 'text-white'
                                : 'text-orange-400 group-hover:text-white'
                        "
                    ></i>
                    <span class="font-bold tracking-tight">Dashboard</span>
                </Link>

                <!-- Campagnes -->
                <Link
                    :href="route('campaigns.index')"
                    :class="[
                        isRouteActive('campaigns.*')
                            ? 'bg-orange-600 text-white shadow-lg shadow-orange-950/50'
                            : 'hover:bg-orange-900/50 hover:text-white',
                    ]"
                    class="flex items-center p-4 rounded-2xl transition-all duration-300 group"
                >
                    <i
                        class="pi pi-flag mr-4 text-xl"
                        :class="
                            isRouteActive('campaigns.*')
                                ? 'text-white'
                                : 'text-orange-400 group-hover:text-white'
                        "
                    ></i>
                    <span class="font-bold tracking-tight">Campagnes</span>
                </Link>

                <!-- Affectations -->
                <Link
                    :href="route('assignments.index')"
                    :class="[
                        isRouteActive('assignments.*')
                            ? 'bg-orange-600 text-white shadow-lg shadow-orange-950/50'
                            : 'hover:bg-orange-900/50 hover:text-white',
                    ]"
                    class="flex items-center p-4 rounded-2xl transition-all duration-300 group"
                >
                    <i
                        class="pi pi-users mr-4 text-xl"
                        :class="
                            isRouteActive('assignments.*')
                                ? 'text-white'
                                : 'text-orange-400 group-hover:text-white'
                        "
                    ></i>
                    <span class="font-bold tracking-tight">Mes Équipes</span>
                </Link>
            </nav>

            <!-- User Profile -->
            <div class="p-6 border-t border-orange-900/50 bg-orange-950/50">
                <div class="bg-orange-900/30 p-4 rounded-2xl flex items-center gap-4 border border-orange-800/30">
                    <div class="w-10 h-10 rounded-xl bg-orange-500 flex items-center justify-center text-white font-black text-sm shadow-lg">
                        {{ $page.props.auth.user?.name?.substring(0, 2).toUpperCase() }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-sm font-black text-white truncate tracking-tight">
                            {{ $page.props.auth.user?.name || 'Chef de Plateau' }}
                        </p>
                        <p class="text-[10px] text-orange-400 truncate font-bold uppercase tracking-tighter">
                            Chef de Plateau
                        </p>
                    </div>
                    <Link :href="route('logout')" method="post" as="button" class="ml-auto text-orange-400 hover:text-white transition-colors">
                        <i class="pi pi-power-off"></i>
                    </Link>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col h-screen overflow-hidden">
            <header class="h-20 bg-white border-b border-orange-100 flex items-center justify-between px-8 z-10 shadow-sm">
                <div class="lg:hidden flex items-center gap-3">
                    <ApplicationLogo size="xs" class="shadow-none rounded-lg" />
                    <span class="text-slate-900 font-black tracking-tighter text-xl uppercase">
                        CP<span class="text-orange-500">Panel</span>
                    </span>
                </div>
                
                <div class="hidden lg:flex items-center gap-2 text-slate-400 font-medium">
                    <i class="pi pi-calendar mr-2 text-orange-400"></i>
                    <span class="text-sm capitalize">{{ new Date().toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) }}</span>
                </div>

                <div class="flex items-center gap-6">
                    <button class="relative p-2 text-slate-400 hover:text-orange-500 transition-colors">
                        <i class="pi pi-bell text-xl"></i>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-orange-500 rounded-full border-2 border-white"></span>
                    </button>
                    <div class="h-8 w-[1px] bg-orange-100 mx-2"></div>
                    <div class="flex items-center gap-3 bg-orange-50 px-4 py-2 rounded-xl border border-orange-100">
                        <span class="text-[10px] font-black text-orange-600 uppercase tracking-widest">Statut:</span>
                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                        <span class="text-[10px] font-black text-slate-600 uppercase">En Ligne</span>
                    </div>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto custom-scrollbar bg-slate-50/50 p-8">
                <slot />
            </div>
        </main>
    </div>
</template>

<style>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(251, 146, 60, 0.2);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(251, 146, 60, 0.4);
}
</style>
