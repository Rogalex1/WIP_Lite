<script setup>
import { ref, computed } from "vue";
import { useForm, Head, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import CPLayout from "@/Layouts/CPLayout.vue";
import SUPLayout from "@/Layouts/SUPLayout.vue";
import TCLayout from "@/Layouts/TCLayout.vue";
import Dialog from "primevue/dialog";
import InputText from "primevue/inputtext";
import Textarea from "primevue/textarea";
import Select from "primevue/select";
import ConfirmDialog from "primevue/confirmdialog";
import { useConfirm } from "primevue/useconfirm";

const props = defineProps({ 
    campaigns: Array,
    userRole: String,
    isAdmin: Boolean
});

const confirm = useConfirm();

// Déterminer le layout selon le rôle
const currentLayout = computed(() => {
    switch(props.userRole) {
        case 'admin': return AdminLayout;
        case 'cp': return CPLayout;
        case 'sup': return SUPLayout;
        case 'tc': return TCLayout;
        default: return AuthenticatedLayout;
    }
});

const displayDialog = ref(false);
const isEditing = ref(false);

const statuses = [
    { label: "Active", value: "active" },
    { label: "Inactive", value: "inactive" },
    { label: "Terminée", value: "completed" },
];

const form = useForm({
    id: null,
    name: "",
    description: "",
    start_date: "",
    end_date: "",
    status: "active",
});

const openNew = () => {
    isEditing.value = false;
    form.reset();
    form.clearErrors();
    displayDialog.value = true;
};

const editCampaign = (campaign) => {
    isEditing.value = true;
    form.clearErrors();
    form.id = campaign.id;
    form.name = campaign.name;
    form.description = campaign.description;
    form.start_date = campaign.start_date;
    form.end_date = campaign.end_date;
    form.status = campaign.status;
    displayDialog.value = true;
};

const saveCampaign = () => {
    if (isEditing.value) {
        form.put(route("campaigns.update", { campaign: form.id }), {
            onSuccess: () => {
                displayDialog.value = false;
                form.reset();
            },
        });
    } else {
        form.post(route("campaigns.store"), {
            onSuccess: () => {
                displayDialog.value = false;
                form.reset();
            },
        });
    }
};

const deleteCampaign = (id) => {
    confirm.require({
        message: 'Êtes-vous sûr de vouloir supprimer cette campagne ? Cette action est irréversible.',
        header: 'Confirmation de suppression',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Annuler',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Supprimer',
            severity: 'danger'
        },
        accept: () => {
            form.delete(route("campaigns.destroy", id));
        }
    });
};

// Déterminer le titre et le layout selon le rôle
const getTitle = () => {
    switch(props.userRole) {
        case 'cp':
            return 'Mes Campagnes';
        case 'sup':
            return 'Ma Campagne';
        case 'tc':
            return 'Ma Campagne';
        default:
            return 'Gestion des Campagnes';
    }
};
</script>

<template>
    <Head title="Campagnes | WIP_Lite" />
    <ConfirmDialog />
    <component :is="currentLayout">
        <div class="p-8 max-w-6xl mx-auto">
            <div class="flex justify-between items-center mb-10">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-8 bg-[#FF7A1A] rounded-full"></div>
                    <h2 class="text-xl font-black text-slate-800 tracking-tighter uppercase">
                        {{ getTitle() }}
                    </h2>
                </div>
                <!-- Bouton créer visible uniquement pour l'admin -->
                <button 
                    v-if="isAdmin"
                    @click="openNew" 
                    class="btn-primary flex items-center shadow-sm"
                >
                    <i class="pi pi-plus mr-2 text-[9px]"></i> NOUVELLE CAMPAGNE
                </button>
            </div>

            <div class="py-12 bg-orange-50/30 rounded-3xl px-4">
                <!-- Grid system pour un rendu plus aéré -->
                <div class="grid grid-cols-1 gap-4">
                    <div
                        v-for="campaign in campaigns"
                        :key="campaign.id"
                        :class="[
                            'group bg-white p-6 rounded-2xl border border-orange-100 flex items-center justify-between hover:border-[#FF7A1A]/50 hover:shadow-xl hover:shadow-orange-200/20 transition-all duration-300',
                            !isAdmin && 'cursor-pointer'
                        ]"
                    >
                        <div class="flex items-center gap-6">
                            <!-- Indicateur de statut stylisé -->
                            <div class="relative flex items-center justify-center">
                                <div 
                                    :class="[
                                        'w-12 h-12 rounded-xl flex items-center justify-center transition-colors',
                                        campaign.status === 'active' ? 'bg-orange-50 text-[#FF7A1A]' : 'bg-slate-50 text-slate-400'
                                    ]"
                                >
                                    <i :class="campaign.status === 'active' ? 'pi pi-bolt' : 'pi pi-pause-circle'"></i>
                                </div>
                            </div>

                            <div>
                                <h3 class="font-black text-slate-800 uppercase text-[13px] tracking-tight group-hover:text-[#FF7A1A] transition-colors">
                                    {{ campaign.name }}
                                </h3>
                                <p class="text-slate-400 text-xs mt-1 max-w-md line-clamp-1">
                                    {{ campaign.description || "Aucune description fournie pour cette campagne." }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-12">
                            <!-- Section Date épurée -->
                            <div class="hidden md:flex flex-col items-end">
                                <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-1">Calendrier</span>
                                <div class="text-[11px] font-bold text-slate-500 bg-slate-50 px-3 py-1 rounded-full border border-slate-100">
                                    {{ campaign.start_date }} <i class="pi pi-arrow-right text-[8px] mx-1 opacity-50"></i> {{ campaign.end_date }}
                                </div>
                            </div>

                            <!-- Actions avec effet hover - Visible uniquement pour Admin -->
                            <div v-if="isAdmin" class="flex items-center gap-2">
                                <Link
                                    :href="route('campaigns.show', campaign.id)"
                                    class="action-btn hover:bg-slate-900 hover:text-white"
                                    title="Voir les détails"
                                >
                                    <i class="pi pi-arrow-up-right text-[12px]"></i>
                                </Link>
                                <button
                                    @click="editCampaign(campaign)"
                                    class="action-btn hover:bg-blue-50 hover:text-blue-600"
                                    title="Modifier"
                                >
                                    <i class="pi pi-pencil text-[12px]"></i>
                                </button>
                                <button
                                    @click="deleteCampaign(campaign.id)"
                                    class="action-btn hover:bg-red-50 hover:text-red-500"
                                    title="Supprimer"
                                >
                                    <i class="pi pi-trash text-[12px]"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="campaigns.length === 0" class="text-center py-20 bg-white rounded-3xl border-2 border-dashed border-slate-100 mt-4">
                    <i class="pi pi-folder-open text-slate-200 text-5xl mb-4"></i>
                    <p class="text-slate-400 font-medium italic">
                        {{ isAdmin ? "Aucune campagne n'a encore été créée." : "Vous n'êtes assigné à aucune campagne." }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Dialog révisé pour un aspect "Modern Card" -->
        <Dialog
            v-model:visible="displayDialog"
            modal
            :header="isEditing ? 'MODIFIER LA CAMPAGNE' : 'NOUVELLE CAMPAGNE'"
            :style="{ width: '450px' }"
            class="modern-dialog"
        >
            <div class="space-y-6 pt-6">
                <div class="space-y-2">
                    <label class="label-style">Désignation du projet</label>
                    <InputText
                        v-model="form.name"
                        class="w-full p-input-modern"
                        placeholder="Ex: Récolte Maïs 2024"
                    />
                </div>

                <div class="space-y-2">
                    <label class="label-style">Objectifs & Notes</label>
                    <Textarea
                        v-model="form.description"
                        rows="3"
                        class="w-full p-input-modern"
                        placeholder="Précisez les objectifs de la campagne..."
                    />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="label-style italic">Date de lancement</label>
                        <InputText type="date" v-model="form.start_date" class="w-full p-input-modern" />
                    </div>
                    <div class="space-y-2">
                        <label class="label-style italic">Clôture prévue</label>
                        <InputText type="date" v-model="form.end_date" class="w-full p-input-modern" />
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="label-style">État actuel</label>
                    <Select
                        v-model="form.status"
                        :options="statuses"
                        optionLabel="label"
                        optionValue="value"
                        class="w-full p-input-modern"
                    />
                </div>
            </div>

            <template #footer>
                <div class="flex items-center justify-between w-full pt-8 border-t border-slate-50 mt-4">
                    <button
                        @click="displayDialog = false"
                        class="text-[10px] font-black text-slate-400 hover:text-red-500 uppercase tracking-[0.2em] transition-colors"
                    >
                        Annuler
                    </button>
                    <button
                        @click="saveCampaign"
                        class="btn-save-modern flex items-center gap-2"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing" class="pi pi-spin pi-spinner text-[10px]"></span>
                        {{ isEditing ? "VALIDER LES MODIFICATIONS" : "LANCER LA CAMPAGNE" }}
                    </button>
                </div>
            </template>
        </Dialog>
    </component>
</template>

<style scoped>
/* Dialog Customization */
:deep(.modern-dialog.p-dialog) {
    border-radius: 24px !important;
    border: none !important;
    overflow: hidden;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15) !important;
}

:deep(.p-dialog-header) {
    background: #0f172a !important; /* Ardoise foncé */
    color: white !important;
    padding: 1.5rem 2rem !important;
}

:deep(.p-dialog-title) {
    font-size: 11px !important;
    font-weight: 900 !important;
    letter-spacing: 0.15em !important;
}

:deep(.p-dialog-content) {
    padding: 0 2rem 1rem 2rem !important;
}

/* Inputs Modern */
.p-input-modern {
    @apply border-slate-200 bg-slate-50 text-slate-700 text-sm py-3 px-4 rounded-xl transition-all duration-200 border-none ring-1 ring-slate-200 focus:ring-2 focus:ring-[#FF7A1A] focus:bg-white outline-none;
}

/* Buttons */
.btn-primary {
    @apply bg-slate-900 text-white text-[10px] font-black px-6 py-3 rounded-xl hover:bg-[#FF7A1A] transition-all tracking-widest shadow-lg shadow-slate-200 hover:shadow-orange-200 duration-300;
}

.btn-save-modern {
    @apply bg-[#FF7A1A] text-white text-[10px] font-black px-8 py-4 rounded-xl hover:bg-slate-900 transition-all tracking-widest shadow-lg shadow-orange-200 hover:shadow-slate-200 duration-300;
}

.action-btn {
    @apply w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-100 text-slate-400 transition-all duration-200;
}

.label-style {
    @apply text-[9px] font-black text-slate-400 uppercase tracking-widest block ml-1;
}

/* PrimeVue Overrides */
:deep(.p-select) {
    @apply shadow-none !important;
}
:deep(.p-select-label) {
    @apply py-3 px-1 !important;
}
</style>