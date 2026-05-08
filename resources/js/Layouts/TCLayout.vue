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
        <!-- Sidebar TC -->
        <aside
            class="w-72 bg-white text-slate-600 flex-shrink-0 hidden lg:flex flex-col shadow-xl border-r border-slate-200"
        >
            <div class="h-24 flex items-center px-8 border-b border-orange-100">
                <div class="flex items-center gap-3">
                    <ApplicationLogo size="sm" class="shadow-none rounded-lg" />
                    <span class="text-slate-900 font-black tracking-tighter text-2xl uppercase">
                        My<span class="text-orange-500">Space</span>
                    </span>
                </div>
            </div>

            <nav class="flex-1 p-6 space-y-3 custom-scrollbar overflow-y-auto">
                <p
                    class="text-[10px] uppercase tracking-[0.2em] text-slate-400 font-black mb-6 px-4"
                >
                    Mon Espace Personnel
                </p>

                <!-- Dashboard -->
                <Link
                    :href="route('tc.dashboard')"
                    :class="[
                        isRouteActive('tc.dashboard')
                            ? 'bg-orange-50 text-orange-600'
                            : 'hover:bg-slate-50 text-slate-500',
                    ]"
                    class="flex items-center p-4 rounded-2xl transition-all duration-300 group font-bold"
                >
                    <i
                        class="pi pi-home mr-4 text-xl"
                        :class="
                            isRouteActive('tc.dashboard')
                                ? 'text-orange-500'
                                : 'text-slate-400 group-hover:text-orange-500'
                        "
                    ></i>
                    Dashboard
                </Link>

                <!-- Assignations -->
                <Link
                    :href="route('assignments.index')"
                    :class="[
                        isRouteActive('assignments.*')
                            ? 'bg-orange-50 text-orange-600'
                            : 'hover:bg-slate-50 text-slate-500',
                    ]"
                    class="flex items-center p-4 rounded-2xl transition-all duration-300 group font-bold"
                >
                    <i
                        class="pi pi-link mr-4 text-xl"
                        :class="
                            isRouteActive('assignments.*')
                                ? 'text-orange-500'
                                : 'text-slate-400 group-hover:text-orange-500'
                        "
                    ></i>
                    Mes Assignations
                </Link>

                <!-- Campagnes -->
                <Link
                    :href="route('campaigns.index')"
                    :class="[
                        isRouteActive('campaigns.*')
                            ? 'bg-orange-50 text-orange-600'
                            : 'hover:bg-slate-50 text-slate-500',
                    ]"
                    class="flex items-center p-4 rounded-2xl transition-all duration-300 group font-bold"
                >
                    <i
                        class="pi pi-flag mr-4 text-xl"
                        :class="
                            isRouteActive('campaigns.*')
                                ? 'text-orange-500'
                                : 'text-slate-400 group-hover:text-orange-500'
                        "
                    ></i>
                    Ma Campagne
                </Link>
            </nav>

            <!-- User Status Card -->
            <div class="p-6">
                <div class="bg-orange-950 rounded-3xl p-6 text-white shadow-2xl shadow-orange-900/40 relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-20 h-20 bg-orange-500/10 rounded-full blur-2xl group-hover:bg-orange-500/20 transition-all"></div>
                    <p class="text-[10px] uppercase font-black text-orange-500 mb-2 tracking-[0.2em]">Votre Status</p>
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        <p class="text-sm font-black truncate uppercase tracking-tight">Téléconseiller Actif</p>
                    </div>
                    <Link :href="route('logout')" method="post" as="button" class="mt-6 w-full py-3 bg-white/5 hover:bg-white/10 rounded-xl text-xs font-black uppercase tracking-widest transition-all border border-white/10">
                        Déconnexion
                    </Link>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col h-screen overflow-hidden">
            <header class="h-20 bg-white border-b border-slate-100 flex items-center justify-between px-8 z-10 shadow-sm">
                <div class="lg:hidden flex items-center gap-3">
                    <ApplicationLogo size="xs" class="shadow-none rounded-lg" />
                    <span class="text-slate-900 font-black tracking-tighter text-xl uppercase">
                        My<span class="text-orange-500">Space</span>
                    </span>
                </div>
                
                <div class="hidden lg:flex items-center gap-4">
                    <div class="flex flex-col">
                        <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Connecté en tant que</span>
                        <span class="text-sm font-bold text-slate-800">{{ $page.props.auth.user?.email || 'tc@wiplite.com' }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-3 bg-slate-50 px-4 py-2 rounded-xl border border-slate-100">
                        <i class="pi pi-clock text-orange-500"></i>
                        <span class="text-xs font-bold text-slate-600 uppercase tracking-tight">{{ new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }) }}</span>
                    </div>
                    <button class="p-2 text-slate-400 hover:text-orange-500 transition-colors">
                        <i class="pi pi-cog text-xl"></i>
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
    background: rgba(15, 23, 42, 0.05);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(15, 23, 42, 0.1);
}
</style>