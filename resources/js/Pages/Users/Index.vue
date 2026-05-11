
<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import { ref } from "vue";
import Dialog from "primevue/dialog";
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from "primevue/useconfirm";

const confirm = useConfirm();

defineOptions({ layout: AdminLayout });

const props = defineProps({
    users: Object,
    roles: Array,
});

// ── Dialog d'édition ──────────────────────────────────────
const showEditDialog = ref(false);
const selectedUser = ref(null);

const editForm = useForm({
    email: "",
    role_id: "",
    password: "",
    password_confirmation: "",
});

const openEdit = (user) => {
    selectedUser.value = user;
    editForm.email = user.email;
    editForm.role_id = user.role?.id ?? "";
    editForm.password = "";
    editForm.password_confirmation = "";
    showEditDialog.value = true;
};

const submitEdit = () => {
    editForm.put(route("users.update", selectedUser.value.id), {
        onSuccess: () => {
            showEditDialog.value = false;
            editForm.reset("password", "password_confirmation");
        },
    });
};

const deactivateUser = (user) => {
    confirm.require({
        message: `Êtes-vous sûr de vouloir désactiver le compte de ${user.email} ?`,
        header: 'Confirmation de désactivation',
        icon: 'pi pi-exclamation-triangle',
        rejectLabel: 'Annuler',
        acceptLabel: 'Désactiver',
        rejectClass: 'p-button-secondary p-button-outlined',
        acceptClass: 'p-button-danger',
        accept: () => {
            router.patch(route("users.toggle-status", user.id));
        }
    });
};
// ─────────────────────────────────────────────────────────

// Fonction pour formater la date
const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    return new Date(dateString).toLocaleDateString("fr-FR", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
    });
};

// Fonction pour obtenir la couleur du badge de rôle
const getRoleColor = (roleName) => {
    const roles = {
        admin: "bg-orange-600 text-white border-orange-700",
        cp: "bg-[#FF7A1A] text-white border-orange-700",
        sup: "bg-orange-400 text-white border-orange-500",
        tc: "bg-orange-200 text-orange-800 border-orange-300",
    };
    return (
        roles[roleName?.toLowerCase()] ||
        "bg-slate-100 text-slate-700 border-slate-200"
    );
};
</script>

<template>
    <Head title="Gestion des Utilisateurs" />

    <div class="space-y-8">
        <!-- Header de la page -->
        <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-4"
        >
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight">
                    Utilisateurs
                </h1>
                <p class="text-slate-500 mt-1">
                    Gérez les comptes d'accès et les permissions de la
                    plateforme.
                </p>
            </div>
            <div class="flex gap-3">
                <Link
                    href="/users/deactivated"
                    class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-200 transition-all flex items-center gap-2"
                >
                    <i class="pi pi-user-minus"></i>
                    Comptes désactivés
                </Link>
                <Link
                    href="/admin/no_users"
                    class="px-4 py-2 bg-[#FF7A1A] text-white rounded-xl font-bold text-sm hover:bg-[#e66a12] shadow-lg shadow-orange-200 transition-all flex items-center gap-2"
                >
                    <i class="pi pi-user-plus"></i>
                    Créer un compte
                </Link>
            </div>
        </div>

        <!-- Table des Utilisateurs -->
        <div
            class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden"
        >
            <div
                class="p-6 border-b border-slate-50 flex justify-between items-center"
            >
                <h3 class="font-bold text-slate-800 text-lg">
                    Liste des comptes
                </h3>
                <span
                    class="px-3 py-1 bg-slate-100 text-slate-500 rounded-full text-xs font-bold"
                >
                    {{ users.total }} utilisateurs au total
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr
                            class="bg-slate-50/50 text-slate-400 text-[10px] uppercase tracking-widest font-black"
                        >
                            <th class="px-6 py-4">Utilisateur</th>
                            <th class="px-6 py-4">Rôle</th>
                            <th class="px-6 py-4">Email</th>
                            <th class="px-6 py-4">Créé le</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <tr
                            v-for="user in users.data"
                            :key="user.id"
                            class="hover:bg-slate-50/50 transition-colors group"
                        >
                            <td class="px-6 py-4 flex items-center gap-3">
                                <div
                                    class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center font-bold text-xs text-slate-600 border border-slate-200 group-hover:bg-orange-50 group-hover:text-[#FF7A1A] group-hover:border-orange-100 transition-all"
                                >
                                    {{
                                        user.email.substring(0, 2).toUpperCase()
                                    }}
                                </div>
                                <div>
                                    <span
                                        class="text-sm font-bold text-slate-700 block"
                                        >{{ user.email.split("@")[0] }}</span
                                    >
                                    <span
                                        class="text-[11px] text-slate-400 font-medium"
                                        >ID: #{{ user.id }}</span
                                    >
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <span
                                    :class="[
                                        'px-2.5 py-1 rounded-lg text-[10px] font-black uppercase border',
                                        getRoleColor(user.role?.name),
                                    ]"
                                >
                                    {{ user.role?.name || "Aucun" }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <span
                                    class="text-sm text-slate-600 font-medium"
                                    >{{ user.email }}</span
                                >
                            </td>

                            <td class="px-6 py-4">
                                <span class="text-sm text-slate-500">{{
                                    formatDate(user.created_at)
                                }}</span>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <button
                                        @click="openEdit(user)"
                                        class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all"
                                        title="Modifier"
                                    >
                                        <i class="pi pi-pencil text-sm"></i>
                                    </button>
                                    <button
                                        @click="deactivateUser(user)"
                                        class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all"
                                        title="Désactiver"
                                    >
                                        <i class="pi pi-user-minus text-sm"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="users.data.length === 0">
                            <td
                                colspan="5"
                                class="px-6 py-12 text-center text-slate-400 italic"
                            >
                                Aucun utilisateur trouvé.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                v-if="users.links.length > 3"
                class="p-6 border-t border-slate-50 bg-slate-50/30 flex items-center justify-between"
            >
                <div
                    class="text-xs font-bold text-slate-400 uppercase tracking-widest"
                >
                    Affichage de {{ users.from }} à {{ users.to }} sur
                    {{ users.total }}
                </div>
                <div class="flex gap-1">
                    <template v-for="(link, k) in users.links" :key="k">
                        <div
                            v-if="link.url === null"
                            class="px-3 py-1.5 text-slate-300 text-xs font-bold"
                            v-html="link.label"
                        />
                        <Link
                            v-else
                            :href="link.url"
                                                        :class="
                                link.active
                                    ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100'
                                    : 'text-slate-500 hover:bg-white border border-transparent hover:border-slate-200'
                            "
                            v-html="link.label"
                        />
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Dialog Modification Utilisateur ─────────────────── -->
    <Dialog
        v-model:visible="showEditDialog"
        modal
        header="Modifier le compte"
        :style="{ width: '480px' }"
        :breakpoints="{ '960px': '75vw', '641px': '90vw' }"
        class="p-fluid"
    >
        <!-- En-tête utilisateur -->
        <div
            v-if="selectedUser"
            class="flex items-center gap-4 pb-6 mb-6 border-b border-slate-100"
        >
            <div
                class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center font-black text-indigo-600 text-sm"
            >
                {{ selectedUser.email.substring(0, 2).toUpperCase() }}
            </div>
            <div>
                <p class="font-black text-slate-800 text-base">
                    {{ selectedUser.email.split("@")[0] }}
                </p>
                <p
                    class="text-[11px] text-slate-400 font-medium uppercase tracking-wider"
                >
                    ID #{{ selectedUser.id }}
                </p>
            </div>
        </div>

        <form @submit.prevent="submitEdit" class="space-y-5">

            <!-- Email -->
            <div class="space-y-1.5">
                <label
                    class="text-[10px] font-black text-slate-500 uppercase tracking-widest"
                    >Adresse e-mail</label
                >
                <div class="relative">
                    <i
                        class="pi pi-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"
                    ></i>
                    <input
                        v-model="editForm.email"
                        type="email"
                        autocomplete="off"
                        placeholder="email@exemple.com"
                        class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl text-sm text-slate-700 font-medium bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all placeholder:text-slate-300"
                    />
                </div>
                <p
                    v-if="editForm.errors.email"
                    class="text-rose-500 text-[11px] font-bold flex items-center gap-1"
                >
                    <i class="pi pi-exclamation-circle text-[10px]"></i>
                    {{ editForm.errors.email }}
                </p>
            </div>

            <!-- Rôle -->
            <div class="space-y-1.5">
                <label
                    class="text-[10px] font-black text-slate-500 uppercase tracking-widest"
                    >Accréditation (rôle)</label
                >
                <div class="grid grid-cols-3 gap-2">
                    <button
                        v-for="role in roles"
                        :key="role.id"
                        type="button"
                        @click="editForm.role_id = role.id"
                        :class="[
                            editForm.role_id === role.id
                                ? 'bg-indigo-600 text-white border-indigo-600 shadow-lg shadow-indigo-100'
                                : 'bg-white text-slate-600 border-slate-200 hover:border-indigo-300 hover:text-indigo-600',
                        ]"
                        class="flex flex-col items-center justify-center py-3 px-2 rounded-xl border-2 transition-all duration-200 font-black text-xs uppercase tracking-wider"
                    >
                        <i
                            :class="{
                                'pi pi-flag': role.name === 'cp',
                                'pi pi-users': role.name === 'sup',
                                'pi pi-headphones': role.name === 'tc',
                                'pi pi-shield': role.name === 'admin',
                            }"
                            class="text-base mb-1"
                        ></i>
                        {{ role.name }}
                    </button>
                </div>
                <p
                    v-if="editForm.errors.role_id"
                    class="text-rose-500 text-[11px] font-bold flex items-center gap-1"
                >
                    <i class="pi pi-exclamation-circle text-[10px]"></i>
                    {{ editForm.errors.role_id }}
                </p>
            </div>

            <!-- Nouveau mot de passe (optionnel) -->
            <div class="space-y-1.5">
                <label
                    class="text-[10px] font-black text-slate-500 uppercase tracking-widest"
                >
                    Nouveau mot de passe
                    <span class="normal-case font-medium text-slate-400 ml-1"
                        >(laisser vide pour ne pas changer)</span
                    >
                </label>
                <div class="relative">
                    <i
                        class="pi pi-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"
                    ></i>
                    <input
                        v-model="editForm.password"
                        type="password"
                        autocomplete="new-password"
                        placeholder="••••••••"
                        class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl text-sm font-medium bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all placeholder:text-slate-300"
                    />
                </div>
                <p
                    v-if="editForm.errors.password"
                    class="text-rose-500 text-[11px] font-bold flex items-center gap-1"
                >
                    <i class="pi pi-exclamation-circle text-[10px]"></i>
                    {{ editForm.errors.password }}
                </p>
            </div>

            <!-- Confirmation du nouveau mot de passe -->
            <div class="space-y-1.5">
                <label
                    class="text-[10px] font-black text-slate-500 uppercase tracking-widest"
                >
                    Confirmer le mot de passe
                </label>
                <div class="relative">
                    <i
                        class="pi pi-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"
                    ></i>
                    <input
                        v-model="editForm.password_confirmation"
                        type="password"
                        autocomplete="new-password"
                        placeholder="••••••••"
                        :class="[
                            'w-full pl-10 pr-4 py-3 border rounded-xl text-sm font-medium bg-white focus:outline-none focus:ring-2 focus:border-transparent transition-all placeholder:text-slate-300',
                            editForm.password &&
                            editForm.password_confirmation &&
                            editForm.password !== editForm.password_confirmation
                                ? 'border-rose-400 focus:ring-rose-400'
                                : editForm.password &&
                                    editForm.password_confirmation &&
                                    editForm.password ===
                                        editForm.password_confirmation
                                  ? 'border-emerald-400 focus:ring-emerald-400'
                                  : 'border-slate-200 focus:ring-indigo-500',
                        ]"
                    />
                </div>
                <!-- Feedback visuel côté client -->
                <p
                    v-if="
                        editForm.password &&
                        editForm.password_confirmation &&
                        editForm.password !== editForm.password_confirmation
                    "
                    class="text-rose-500 text-[11px] font-bold flex items-center gap-1"
                >
                    <i class="pi pi-times-circle text-[10px]"></i>
                    Les mots de passe ne correspondent pas
                </p>
                <p
                    v-if="
                        editForm.password &&
                        editForm.password_confirmation &&
                        editForm.password === editForm.password_confirmation
                    "
                    class="text-emerald-600 text-[11px] font-bold flex items-center gap-1"
                >
                    <i class="pi pi-check-circle text-[10px]"></i>
                    Les mots de passe correspondent
                </p>
                <!-- Erreur serveur (fallback) -->
                <p
                    v-if="editForm.errors.password_confirmation"
                    class="text-rose-500 text-[11px] font-bold flex items-center gap-1"
                >
                    <i class="pi pi-exclamation-circle text-[10px]"></i>
                    {{ editForm.errors.password_confirmation }}
                </p>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 pt-2">
                <button
                    type="button"
                    @click="showEditDialog = false"
                    class="flex-1 py-3 rounded-xl border border-slate-200 text-slate-600 text-sm font-bold hover:bg-slate-50 transition-all"
                >
                    Annuler
                </button>
                <button
                    type="submit"
                    :disabled="editForm.processing"
                    class="flex-1 py-3 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all disabled:opacity-50 flex items-center justify-center gap-2"
                >
                    <i
                        v-if="editForm.processing"
                        class="pi pi-spin pi-spinner text-sm"
                    ></i>
                    <i v-else class="pi pi-check text-sm"></i>
                    {{
                        editForm.processing
                            ? "Enregistrement..."
                            : "Enregistrer"
                    }}
                </button>
            </div>
        </form>
    </Dialog>
    <!-- ─────────────────────────────────────────────────────── -->
    <ConfirmDialog />
</template>

<style>
/* Dialog styles cohérents avec le thème WIP LITE */
.p-dialog {
    @apply rounded-2xl border-none shadow-2xl;
}
.p-dialog .p-dialog-header {
    @apply px-6 pt-6 pb-0 bg-white rounded-t-2xl;
}
.p-dialog .p-dialog-header-title {
    @apply font-black text-slate-800 text-lg tracking-tight;
}
.p-dialog .p-dialog-content {
    @apply p-6 rounded-b-2xl;
}
.p-dialog .p-dialog-header-icons .p-dialog-header-icon {
    @apply w-8 h-8 rounded-xl bg-slate-50 text-slate-400 hover:bg-rose-50 hover:text-rose-500 transition-all;
}
</style>
