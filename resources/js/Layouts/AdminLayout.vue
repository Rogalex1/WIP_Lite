<script setup>
import { Link, usePage, router } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
import Toast from "primevue/toast";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";

const page = usePage();

const isRouteActive = (routeName) => {
    return route().current(routeName);
};

const activateUsers = () => {
    router.post(route("no_users"));
};
</script>

<template>
    <div class="min-h-screen bg-slate-50 flex font-sans antialiased">
        <Toast />
        <!-- Sidebar -->
        <aside
            class="w-72 bg-slate-900 text-slate-300 flex-shrink-0 hidden lg:flex flex-col shadow-2xl border-r border-slate-800"
        >
            <div class="h-24 flex items-center px-8 border-b border-slate-800/50">
                <div class="flex items-center gap-3">
                    <ApplicationLogo size="sm" class="shadow-none rounded-lg" />
                    <span class="text-white font-black tracking-tighter text-2xl">
                        WIP<span class="text-[#FF7A1A]">LITE</span>
                    </span>
                </div>
            </div>

            <nav class="flex-1 p-6 space-y-2 custom-scrollbar overflow-y-auto">
                <p
                    class="text-[10px] uppercase tracking-[0.2em] text-slate-500 font-black mb-6 px-4"
                >
                    Administration
                </p>

                <!-- Dashboard -->
                <Link
                    :href="route('admin.dashboard')"
                    :class="[
                        isRouteActive('admin.dashboard')
                            ? 'bg-[#FF7A1A] text-white shadow-lg shadow-orange-900/20'
                            : 'hover:bg-slate-800 hover:text-white',
                    ]"
                    class="flex items-center p-4 rounded-2xl transition-all duration-300 group"
                >
                    <i
                        class="pi pi-th-large mr-4 text-xl"
                        :class="
                            isRouteActive('admin.dashboard')
                                ? 'text-white'
                                : 'text-slate-500 group-hover:text-[#FF7A1A]'
                        "
                    ></i>
                    <span class="font-bold tracking-tight">Dashboard</span>
                </Link>

                <!-- Employés -->
                <Link
                    :href="route('employees.index')"
                    :class="[
                        isRouteActive('employees.*')
                            ? 'bg-[#FF7A1A] text-white shadow-lg shadow-orange-900/20'
                            : 'hover:bg-slate-800 hover:text-white',
                    ]"
                    class="flex items-center p-4 rounded-2xl transition-all duration-300 group"
                >
                    <i
                        class="pi pi-users mr-4 text-xl"
                        :class="
                            isRouteActive('employees.*')
                                ? 'text-white'
                                : 'text-slate-500 group-hover:text-[#FF7A1A]'
                        "
                    ></i>
                    <span class="font-bold tracking-tight">Collaborateurs</span>
                </Link>

                <!-- Campagnes -->
                <Link
                    :href="route('campaigns.index')"
                    :class="[
                        isRouteActive('campaigns.*')
                            ? 'bg-[#FF7A1A] text-white shadow-lg shadow-orange-900/20'
                            : 'hover:bg-slate-800 hover:text-white',
                    ]"
                    class="flex items-center p-4 rounded-2xl transition-all duration-300 group"
                >
                    <i
                        class="pi pi-flag mr-4 text-xl"
                        :class="
                            isRouteActive('campaigns.*')
                                ? 'text-white'
                                : 'text-slate-500 group-hover:text-[#FF7A1A]'
                        "
                    ></i>
                    <span class="font-bold tracking-tight">Campagnes</span>
                </Link>

                <!-- Assignations -->
                <Link
                    :href="route('assignments.index')"
                    :class="[
                        isRouteActive('assignments.index')
                            ? 'bg-[#FF7A1A] text-white shadow-lg shadow-orange-900/20'
                            : 'hover:bg-slate-800 hover:text-white',
                    ]"
                    class="flex items-center p-4 rounded-2xl transition-all duration-300 group"
                >
                    <i
                        class="pi pi-link mr-4 text-xl"
                        :class="
                            isRouteActive('assignments.index')
                                ? 'text-white'
                                : 'text-slate-500 group-hover:text-[#FF7A1A]'
                        "
                    ></i>
                    <span class="font-bold tracking-tight">Affectations</span>
                </Link>

                <div class="pt-6 mt-6 border-t border-slate-800/50">
                    <p class="text-[10px] uppercase tracking-[0.2em] text-slate-500 font-black mb-4 px-4">Système</p>
                    <button
                        @click="activateUsers"
                        class="w-full flex items-center p-4 rounded-2xl transition-all duration-300 group hover:bg-slate-800 hover:text-white text-left"
                    >
                        <i
                            class="pi pi-user-plus mr-4 text-xl text-slate-500 group-hover:text-[#FF7A1A]"
                        ></i>
                        <span class="font-bold tracking-tight">Activation</span>
                    </button>
                </div>
            </nav>

            <!-- User Profile Section -->
            <div class="p-6 border-t border-slate-800/50 bg-slate-900/50">
                <div class="bg-slate-800/40 p-4 rounded-2xl flex items-center gap-4 border border-slate-700/30">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#FF9E4F] to-[#FF7A1A] flex items-center justify-center text-white font-black text-sm shadow-lg">
                        {{ $page.props.auth.user?.name?.substring(0, 2).toUpperCase() }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-sm font-black text-white truncate tracking-tight">
                            {{ $page.props.auth.user?.name || 'Admin' }}
                        </p>
                        <p class="text-[10px] text-slate-500 truncate font-bold uppercase tracking-tighter">
                            Administrateur
                        </p>
                    </div>
                    <Link :href="route('logout')" method="post" as="button" class="ml-auto text-slate-500 hover:text-white transition-colors">
                        <i class="pi pi-power-off"></i>
                    </Link>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col h-screen overflow-hidden">
            <!-- Top Header -->
            <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-8 z-10 shadow-sm">
                <div class="lg:hidden flex items-center gap-3">
                    <ApplicationLogo size="xs" class="shadow-none rounded-lg" />
                    <span class="text-slate-900 font-black tracking-tighter text-xl">
                        WIP<span class="text-[#FF7A1A]">LITE</span>
                    </span>
                </div>
                
                <div class="hidden lg:flex items-center gap-2 text-slate-400">
                    <i class="pi pi-search mr-2"></i>
                    <span class="text-sm font-medium">Recherche globale...</span>
                </div>

                <div class="flex items-center gap-6">
                    <button class="relative p-2 text-slate-400 hover:text-[#FF7A1A] transition-colors">
                        <i class="pi pi-bell text-xl"></i>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-[#FF7A1A] rounded-full border-2 border-white"></span>
                    </button>
                    <div class="h-8 w-[1px] bg-slate-200 mx-2"></div>
                    <span class="text-xs font-black text-slate-400 uppercase tracking-widest">{{ new Date().toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long' }) }}</span>
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

/* Transitions globales */
.page-enter-active, .page-leave-active {
    transition: opacity 0.3s ease, transform 0.3s ease;
}
.page-enter-from, .page-leave-to {
    opacity: 0;
    transform: translateY(10px);
}
</style>
