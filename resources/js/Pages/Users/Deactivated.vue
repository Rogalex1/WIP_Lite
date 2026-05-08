<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from "primevue/useconfirm";

const confirm = useConfirm();

defineOptions({ layout: AdminLayout });

const props = defineProps({
    users: Object,
});

// Fonction pour formater la date
const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    });
};

// Fonction pour obtenir la couleur du badge de rôle
const getRoleColor = (roleName) => {
    const roles = {
        'admin': 'bg-rose-100 text-rose-700 border-rose-200',
        'cp':    'bg-indigo-100 text-indigo-700 border-indigo-200',
        'sup':   'bg-blue-100 text-blue-700 border-blue-200',
        'tc':    'bg-emerald-100 text-emerald-700 border-emerald-200',
    };
    return roles[roleName?.toLowerCase()] || 'bg-slate-100 text-slate-700 border-slate-200';
};

const activateUser = (user) => {
    confirm.require({
        message: `Voulez-vous réactiver le compte de ${user.email} ?`,
        header: 'Confirmation de réactivation',
        icon: 'pi pi-user-plus',
        rejectLabel: 'Annuler',
        acceptLabel: 'Réactiver',
        rejectClass: 'p-button-secondary p-button-outlined',
        acceptClass: 'p-button-success',
        accept: () => {
            router.patch(route('users.toggle-status', user.id));
        }
    });
};
</script>

<template>
    <Head title="Comptes Désactivés" />

    <div class="space-y-8">
        <!-- Header de la page -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <Link 
                    href="/users"
                    class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:border-indigo-100 hover:bg-indigo-50 transition-all shadow-sm"
                >
                    <i class="pi pi-arrow-left text-sm"></i>
                </Link>
                <div>
                    <h1 class="text-3xl font-black text-slate-800 tracking-tight">Comptes désactivés</h1>
                    <p class="text-slate-500 mt-1">Liste des utilisateurs n'ayant plus accès à la plateforme.</p>
                </div>
            </div>
        </div>

        <!-- Table des Utilisateurs -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
                <h3 class="font-bold text-slate-800 text-lg">Archives</h3>
                <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-bold border border-amber-200">
                    {{ users.total }} comptes inactifs
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 text-slate-400 text-[10px] uppercase tracking-widest font-black">
                            <th class="px-6 py-4">Utilisateur</th>
                            <th class="px-6 py-4">Rôle</th>
                            <th class="px-6 py-4">Email</th>
                            <th class="px-6 py-4">Créé le</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <tr v-for="user in users.data" :key="user.id" class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-6 py-4 flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center font-bold text-xs text-slate-600 border border-slate-200 grayscale">
                                    {{ user.email.substring(0, 2).toUpperCase() }}
                                </div>
                                <div>
                                    <span class="text-sm font-bold text-slate-400 block line-through">{{ user.email.split('@')[0] }}</span>
                                    <span class="text-[11px] text-slate-400 font-medium">ID: #{{ user.id }}</span>
                                </div>
                            </td>
                            
                            <td class="px-6 py-4">
                                <span :class="['px-2.5 py-1 rounded-lg text-[10px] font-black uppercase border opacity-60', getRoleColor(user.role?.name)]">
                                    {{ user.role?.name || 'Aucun' }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <span class="text-sm text-slate-400 font-medium">{{ user.email }}</span>
                            </td>

                            <td class="px-6 py-4">
                                <span class="text-sm text-slate-400">{{ formatDate(user.created_at) }}</span>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <button 
                                    @click="activateUser(user)"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-50 text-emerald-600 rounded-lg font-bold text-[10px] uppercase tracking-wider hover:bg-emerald-600 hover:text-white transition-all border border-emerald-100"
                                    title="Réactiver le compte"
                                >
                                    <i class="pi pi-user-plus"></i>
                                    Réactiver
                                </button>
                            </td>
                        </tr>
                        <tr v-if="users.data.length === 0">
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">
                                Aucun compte désactivé.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="users.links.length > 3" class="p-6 border-t border-slate-50 bg-slate-50/30 flex items-center justify-between">
                <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                    Affichage de {{ users.from }} à {{ users.to }} sur {{ users.total }}
                </div>
                <div class="flex gap-1">
                    <template v-for="(link, k) in users.links" :key="k">
                        <div v-if="link.url === null" class="px-3 py-1.5 text-slate-300 text-xs font-bold" v-html="link.label" />
                        <Link 
                            v-else 
                            :href="link.url" 
                            class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all"
                            :class="link.active ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-slate-500 hover:bg-white border border-transparent hover:border-slate-200'"
                            v-html="link.label" 
                        />
                    </template>
                </div>
            </div>
        </div>
    </div>
    <ConfirmDialog />
</template>
