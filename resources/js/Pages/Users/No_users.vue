<script setup>
import { useForm, Head } from "@inertiajs/vue3";
import { ref } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import Dialog from "primevue/dialog";

defineOptions({ layout: AdminLayout });

const props = defineProps({
    employesSansCompte: Array,
    employe: Object, // L'employé sélectionné pour le pré-remplissage
    roles: Array,
});

// Initialisation du formulaire
const form = useForm({
    employee_id: props.employe?.id || "",
    email: props.employe?.email || "",
    role_id: "",
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post(route("users.store"), {
        onSuccess: () => {
            showDialog.value = false;
            form.reset();
        },
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const showDialog = ref(false);
const selectedEmploye = ref(null);

const selectEmploye = (emp) => {
    selectedEmploye.value = emp;
    form.employee_id = emp.id;
    form.email = emp.email;
    showDialog.value = true;
};

// Fonction pour obtenir la couleur du badge de rôle (si besoin d'afficher les rôles dispo)
const getRoleBadgeClass = (roleName) => {
    const roles = {
        'admin': 'bg-rose-100 text-rose-700',
        'cp': 'bg-indigo-100 text-indigo-700',
        'sup': 'bg-blue-100 text-blue-700',
        'tc': 'bg-emerald-100 text-emerald-700',
    };
    return roles[roleName?.toLowerCase()] || 'bg-slate-100 text-slate-700';
};
</script>

<template>
    <Head title="Activation des comptes" />

    <div class="space-y-8">
        <!-- Header de la page -->
        <div class="flex flex-col md:items-start gap-1">
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Activation des comptes</h1>
            <p class="text-slate-500">Sélectionnez un employé pour lui créer un accès à la plateforme.</p>
        </div>

        <!-- Table des employés sans compte -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
                <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                    <i class="pi pi-users text-indigo-500"></i>
                    Employés en attente d'accès
                </h3>
                <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-bold border border-amber-200">
                    {{ employesSansCompte.length }} en attente
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 text-slate-400 text-[10px] uppercase tracking-widest font-black">
                            <th class="px-6 py-4">Employé</th>
                            <th class="px-6 py-4">Poste</th>
                            <th class="px-6 py-4">Matricule / Email</th>
                            <th class="px-6 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <tr
                            v-for="emp in employesSansCompte"
                            :key="emp.id"
                            class="hover:bg-slate-50/50 transition-colors group"
                        >
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center font-bold text-indigo-600 border border-indigo-100">
                                        {{ emp.first_name[0] }}{{ emp.last_name[0] }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-700">{{ emp.first_name }} {{ emp.last_name }}</div>
                                        <div class="text-[11px] text-slate-400 font-medium uppercase tracking-wider">ID: #{{ emp.id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg text-[10px] font-black uppercase border border-slate-200">
                                    {{ emp.position?.name || 'Non défini' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs font-bold text-slate-500 font-mono">{{ emp.matricule }}</div>
                                <div class="text-xs text-slate-400">{{ emp.email }}</div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button
                                    @click="selectEmploye(emp)"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-xl font-bold text-xs hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all shadow-sm group-hover:shadow-indigo-100"
                                >
                                    <i class="pi pi-key text-[10px]"></i>
                                    Activer l'accès
                                </button>
                            </td>
                        </tr>
                        <tr v-if="employesSansCompte.length === 0">
                            <td colspan="4" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center">
                                        <i class="pi pi-check-circle text-3xl text-slate-200"></i>
                                    </div>
                                    <p class="text-slate-400 italic font-medium">Tous les comptes sont déjà activés.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal de création de compte -->
        <Dialog
            v-model:visible="showDialog"
            modal
            header="Configuration du compte"
            :style="{ width: '460px' }"
            class="p-fluid shadow-2xl"
            :breakpoints="{ '960px': '75vw', '641px': '90vw' }"
        >
            <template v-if="selectedEmploye">
                <!-- Profile Header Noir sur Blanc -->
                <div class="mb-8 flex items-center gap-4 border-b border-slate-100 pb-6">
                    <div class="w-14 h-14 rounded-full bg-white flex items-center justify-center font-bold text-black border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                        {{ selectedEmploye.first_name[0] }}{{ selectedEmploye.last_name[0] }}
                    </div>
                    <div>
                        <h4 class="font-black text-black text-lg tracking-tight uppercase">
                            {{ selectedEmploye.first_name }} {{ selectedEmploye.last_name }}
                        </h4>
                        <p class="text-slate-500 text-[10px] font-black uppercase tracking-[0.2em]">
                            {{ selectedEmploye.position?.name || 'Collaborateur' }}
                        </p>
                    </div>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Email -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-black uppercase tracking-[0.2em] px-1">Email de connexion</label>
                        <div class="relative">
                            <input
                                v-model="form.email"
                                type="email"
                                readonly
                                class="w-full border-2 border-slate-100 rounded-none bg-white px-4 py-3 text-sm font-bold text-slate-400 cursor-not-allowed focus:ring-0 focus:border-slate-100"
                            />
                        </div>
                    </div>

                    <!-- Rôle Selection Noir sur Blanc -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-black uppercase tracking-[0.2em] px-1">Accréditation</label>
                        <div class="grid grid-cols-2 gap-3">
                            <button
                                v-for="role in roles"
                                :key="role.id"
                                type="button"
                                @click="form.role_id = role.id"
                                :class="[
                                    form.role_id === role.id 
                                    ? 'bg-black text-white border-black' 
                                    : 'bg-white text-black border-slate-200 hover:border-black'
                                ]"
                                class="flex flex-col items-center justify-center p-4 rounded-none border-2 transition-all duration-200"
                            >
                                <span class="text-xs font-black uppercase tracking-widest">{{ role.name }}</span>
                                <span class="text-[9px] font-bold mt-1 opacity-50">Accès {{ role.name }}</span>
                            </button>
                        </div>
                        <p v-if="form.errors.role_id" class="text-red-600 text-[10px] font-black uppercase tracking-widest mt-2 px-1">{{ form.errors.role_id }}</p>
                    </div>

                    <!-- Password -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-black uppercase tracking-[0.2em] px-1">Mot de passe provisoire</label>
                        <input
                            v-model="form.password"
                            type="password"
                            placeholder="••••••••"
                            class="w-full border-2 border-slate-200 rounded-none bg-white px-4 py-3 text-sm font-bold text-black placeholder:text-slate-200 focus:ring-0 focus:border-black transition-all"
                        />
                        <p v-if="form.errors.password" class="text-red-600 text-[10px] font-black uppercase tracking-widest mt-2 px-1">{{ form.errors.password }}</p>
                    </div>

                    <!-- Confirmation du mot de passe -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-black uppercase tracking-[0.2em] px-1">Confirmer le mot de passe</label>
                        <input
                            v-model="form.password_confirmation"
                            type="password"
                            placeholder="••••••••"
                            :class="[
                                'w-full border-2 rounded-none bg-white px-4 py-3 text-sm font-bold text-black placeholder:text-slate-200 focus:ring-0 transition-all',
                                form.password && form.password_confirmation && form.password !== form.password_confirmation
                                    ? 'border-red-400 focus:border-red-500'
                                    : form.password && form.password_confirmation && form.password === form.password_confirmation
                                        ? 'border-emerald-400 focus:border-emerald-500'
                                        : 'border-slate-200 focus:border-black'
                            ]"
                        />
                        <!-- Indicateur visuel côté client -->
                        <p
                            v-if="form.password && form.password_confirmation && form.password !== form.password_confirmation"
                            class="text-red-600 text-[10px] font-black uppercase tracking-widest mt-2 px-1 flex items-center gap-1"
                        >
                            <i class="pi pi-times-circle text-[9px]"></i>
                            Les mots de passe ne correspondent pas
                        </p>
                        <p
                            v-else-if="form.password && form.password_confirmation && form.password === form.password_confirmation"
                            class="text-emerald-600 text-[10px] font-black uppercase tracking-widest mt-2 px-1 flex items-center gap-1"
                        >
                            <i class="pi pi-check-circle text-[9px]"></i>
                            Les mots de passe correspondent
                        </p>
                        <!-- Erreur serveur (fallback) -->
                        <p v-if="form.errors.password_confirmation" class="text-red-600 text-[10px] font-black uppercase tracking-widest mt-2 px-1">{{ form.errors.password_confirmation }}</p>
                    </div>

                    <!-- Submit Noir sur Blanc -->
                    <div class="pt-4">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full bg-black text-white py-4 rounded-none font-black text-xs uppercase tracking-[0.3em] hover:bg-slate-800 transition-all disabled:opacity-30 shadow-[6px_6px_0px_0px_rgba(0,0,0,0.1)] active:shadow-none active:translate-x-1 active:translate-y-1"
                        >
                            <span v-if="!form.processing">Confirmer l'accès</span>
                            <span v-else class="flex items-center justify-center gap-2">
                                <i class="pi pi-spin pi-spinner"></i>
                                En cours
                            </span>
                        </button>
                    </div>
                </form>
            </template>
        </Dialog>
    </div>
</template>

<style>
/* Ajustements pour PrimeVue Dialog pour matcher le thème WIP LITE */
.p-dialog {
    @apply rounded-3xl border-none shadow-2xl;
}
.p-dialog .p-dialog-header {
    @apply p-6 bg-white border-b border-slate-50 rounded-t-3xl;
}
.p-dialog .p-dialog-header-title {
    @apply font-black text-slate-800 text-xl tracking-tight;
}
.p-dialog .p-dialog-content {
    @apply p-6 rounded-b-3xl;
}
.p-dialog .p-dialog-header-icons .p-dialog-header-icon {
    @apply w-8 h-8 rounded-xl bg-slate-50 text-slate-400 hover:bg-rose-50 hover:text-rose-500 transition-all;
}
</style>
