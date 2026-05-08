 

<script setup>
import { ref } from "vue";
import { useForm, Head, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import Dialog from "primevue/dialog";
import InputText from "primevue/inputtext";
import Textarea from "primevue/textarea";
import Select from "primevue/select";

const props = defineProps({ campaigns: Array });
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
    if (confirm("Supprimer cette campagne ?")) {
        form.delete(route("campaigns.destroy", id));
    }
};
</script>

<template>
    <Head title="Campagnes | WIP_Lite" />

    <AdminLayout>
        
            <div class="flex justify-between items-center max-w-6xl mx-auto">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-8 bg-[#FF7A1A] rounded-full"></div>
                    <h2 class="text-xl font-black text-slate-800 tracking-tighter uppercase">
                        Gestion des Campagnes
                    </h2>
                </div>
                <button @click="openNew" class="btn-primary flex items-center shadow-sm">
                    <i class="pi pi-plus mr-2 text-[9px]"></i> NOUVELLE CAMPAGNE
                </button>
            </div>
        

        <div class="py-12 bg-[#F8FAFC]">
            <div class="max-w-6xl mx-auto px-4">
                <!-- Grid system pour un rendu plus aéré -->
                <div class="grid grid-cols-1 gap-4">
                    <div
                        v-for="campaign in campaigns"
                        :key="campaign.id"
                        class="group bg-white p-6 rounded-2xl border border-slate-100 flex items-center justify-between hover:border-[#FF7A1A]/30 hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300"
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

                            <!-- Actions avec effet hover -->
                            <div class="flex items-center gap-2">
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
                <div v-if="campaigns.length === 0" class="text-center py-20 bg-white rounded-3xl border-2 border-dashed border-slate-100">
                    <i class="pi pi-folder-open text-slate-200 text-5xl mb-4"></i>
                    <p class="text-slate-400 font-medium italic">Aucune campagne n'a encore été créée.</p>
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
    </AdminLayout>
</template>

 