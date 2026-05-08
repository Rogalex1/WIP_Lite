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
        <!-- Sidebar SUP -->
        <aside
            class="w-72 bg-slate-900 text-slate-300 flex-shrink-0 hidden lg:flex flex-col shadow-2xl border-r border-slate-800"
        >
            <div class="h-24 flex items-center px-8 border-b border-slate-800/50">
                <div class="flex items-center gap-3">
                    <ApplicationLogo size="sm" class="shadow-none rounded-lg" />
                    <span class="text-white font-black tracking-tighter text-2xl uppercase">
                        SUP<span class="text-orange-500">Core</span>
                    </span>
                </div>
            </div>

            <nav class="flex-1 p-6 space-y-2 custom-scrollbar overflow-y-auto">
                <p
                    class="text-[10px] uppercase tracking-[0.2em] text-slate-500 font-black mb-6 px-4"
                >
                    Gestion Terrain
                </p>

                <!-- Dashboard -->
                <Link
                    :href="route('sup.dashboard')"
                    :class="[
                        isRouteActive('sup.dashboard')
                            ? 'bg-orange-600 text-white shadow-lg shadow-orange-900/20'
                            : 'hover:bg-slate-800 hover:text-white',
                    ]"
                    class="flex items-center p-4 rounded-2xl transition-all duration-300 group"
                >
                    <i
                        class="pi pi-home mr-4 text-xl"
                        :class="
                            isRouteActive('sup.dashboard')
                                ? 'text-white'
                                : 'text-orange-400 group-hover:text-white'
                        "
                    ></i>
                    <span class="font-bold tracking-tight">Dashboard</span>
                </Link>

                <!-- Mon Équipe -->
                <Link
                    :href="route('assignments.index')"
                    :class="[
                        isRouteActive('assignments.*')
                            ? 'bg-orange-600 text-white shadow-lg shadow-orange-900/20'
                            : 'hover:bg-slate-800 hover:text-white',
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
                    <span class="font-bold tracking-tight">Mon Équipe</span>
                </Link>

                <!-- Campagnes -->
                <Link
                    :href="route('campaigns.index')"
                    :class="[
                        isRouteActive('campaigns.*')
                            ? 'bg-orange-600 text-white shadow-lg shadow-orange-900/20'
                            : 'hover:bg-slate-800 hover:text-white',
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
            </nav>

            <!-- User Profile -->
            <div class="p-6 border-t border-slate-800/50 bg-slate-900/50">
                <div class="bg-slate-800/30 p-4 rounded-2xl flex items-center gap-4 border border-slate-700/30">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white font-black text-sm shadow-lg">
                        {{ $page.props.auth.user?.name?.substring(0, 2).toUpperCase() || 'SU' }}
                    </div>
                    <div class="overflow-hidden flex-1">
                        <p class="text-sm font-black text-white truncate tracking-tight">
                            {{ $page.props.auth.user?.name || 'Superviseur' }}
                        </p>
                        <span class="text-[9px] bg-orange-900/50 text-orange-400 px-2 py-0.5 rounded font-black uppercase tracking-tighter border border-orange-800/30">Superviseur</span>
                    </div>
                    <Link :href="route('logout')" method="post" as="button" class="text-slate-500 hover:text-white transition-colors">
                        <i class="pi pi-power-off"></i>
                    </Link>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col h-screen overflow-hidden">
            <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-8 z-10 shadow-sm">
                <div class="lg:hidden flex items-center gap-3">
                    <ApplicationLogo size="xs" class="shadow-none rounded-lg" />
                    <span class="text-slate-900 font-black tracking-tighter text-xl uppercase">
                        SUP<span class="text-orange-500">Core</span>
                    </span>
                </div>
                
                <div class="hidden lg:flex items-center gap-2 text-slate-400 font-medium">
                    <i class="pi pi-shield mr-2 text-orange-500"></i>
                    <span class="text-sm uppercase tracking-widest font-black">Supervision Directe</span>
                </div>

                <div class="flex items-center gap-6">
                    <div class="flex flex-col items-end">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Performance</span>
                        <span class="text-xs font-bold text-orange-600">En cours d'analyse...</span>
                    </div>
                    <div class="h-8 w-[1px] bg-slate-200 mx-2"></div>
                    <button class="relative p-2 text-slate-400 hover:text-orange-500 transition-colors">
                        <i class="pi pi-bell text-xl"></i>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-orange-500 rounded-full border-2 border-white"></span>
                    </button>
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
    background: rgba(15, 23, 42, 0.1);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(15, 23, 42, 0.2);
}
</style>