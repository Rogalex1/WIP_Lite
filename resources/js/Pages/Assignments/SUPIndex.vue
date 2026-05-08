<script setup>
import { ref, computed } from "vue";
import { Head, router, useForm } from "@inertiajs/vue3";
import SUPLayout from "@/Layouts/SUPLayout.vue";
import ConfirmDialog from "primevue/confirmdialog";
import { useConfirm } from "primevue/useconfirm";
import Tag from "primevue/tag";
import InputText from "primevue/inputtext";
import Dialog from "primevue/dialog";
import Select from "primevue/select";
import { useToast } from "primevue/usetoast";
import Toast from "primevue/toast";

const props = defineProps({
    campaignsTree: Array,
    availableEmployees: Array,
    campaigns: Array,
});

const confirm = useConfirm();
const toast = useToast();
const searchQueryTeam = ref("");
const searchQueryVivier = ref("");
const displayModal = ref(false);
const selectedEmployee = ref(null);

const form = useForm({
    employee_id: "",
    campaign_id: null,
    manager_id: null,
    position_id: 3, // TC uniquement pour le SUP
    reason: "Affectation par SUP"
});

// Filtrage de l'équipe actuelle
const filteredTeam = (campaign) => {
    return campaign.assignments.filter(tc => {
        const name = (tc.employee.first_name + " " + tc.employee.last_name).toLowerCase();
        return name.includes(searchQueryTeam.value.toLowerCase());
    });
};

// Filtrage du vivier disponible (TC uniquement)
const filteredVivier = computed(() => {
    return props.availableEmployees.filter(emp => {
        const name = (emp.first_name + " " + emp.last_name).toLowerCase();
        return name.includes(searchQueryVivier.value.toLowerCase()) && emp.position_id === 3;
    });
});

const openAssignModal = (emp) => {
    form.reset();
    selectedEmployee.value = emp;
    form.employee_id = emp.id;
    // Si le SUP n'est affecté qu'à une seule campagne, on la sélectionne par défaut
    if (props.campaigns.length === 1) {
        form.campaign_id = props.campaigns[0].id;
    }
    displayModal.value = true;
};

const handleRelease = (id) => {
    confirm.require({
        message: 'Voulez-vous libérer ce TC de votre équipe ?',
        header: 'Confirmation de libération',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Annuler',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Libérer',
            severity: 'danger' 
        },
        accept: () => {
            router.delete(route('assignments.release', id), {
                onSuccess: () => toast.add({ severity: 'success', summary: 'Succès', detail: 'TC libéré', life: 3000 })
            });
        }
    });
};
</script>

<template>
    <Head title="Mon Équipe" />
    <ConfirmDialog />
    <SUPLayout>
        <Toast />
        <div class="p-8 bg-[#FBFDFF] min-h-screen">
            <div class="flex flex-col xl:flex-row gap-8 max-w-[1600px] mx-auto">
                
                <!-- Section Équipe -->
                <div class="flex-1 space-y-6">
                    <div class="flex items-center justify-between gap-4 border-b border-orange-100 pb-4">
                        <div class="flex items-center gap-4">
                            <div class="w-2 h-8 bg-orange-500 rounded-full"></div>
                            <h1 class="text-2xl font-black text-slate-900 tracking-tighter uppercase">Ma Team</h1>
                        </div>
                        
                        <!-- Recherche Équipe -->
                        <div class="relative w-64">
                            <i class="pi pi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <InputText v-model="searchQueryTeam" placeholder="Rechercher dans ma team..." class="w-full !pl-9 !py-2 !text-xs !rounded-xl !bg-white !border-orange-100 focus:!ring-orange-500" />
                        </div>
                    </div>

                    <div v-for="campaign in campaignsTree" :key="campaign.id" class="bg-white rounded-3xl border border-orange-100 p-8 shadow-sm">
                        <div class="flex justify-between items-center mb-8 pb-4 border-b border-orange-50">
                            <h2 class="text-xl font-bold text-slate-700">{{ campaign.name }}</h2>
                            <Tag :value="`${filteredTeam(campaign).length} Téléconseillers`" severity="warning" class="!bg-orange-50 !text-orange-600" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div v-for="tc in filteredTeam(campaign)" :key="tc.id" class="flex justify-between items-center p-5 bg-orange-50/20 rounded-2xl border border-orange-100 group hover:border-orange-300 transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-orange-400 border border-orange-100 shadow-sm font-black text-xs">
                                        {{ tc.employee.first_name.charAt(0) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-black text-slate-800 text-xs uppercase tracking-tight">{{ tc.employee.first_name }} {{ tc.employee.last_name }}</span>
                                        <span class="text-[10px] text-orange-500 font-bold uppercase">Téléconseiller</span>
                                    </div>
                                </div>
                                <button @click="handleRelease(tc.id)" class="text-slate-300 hover:text-red-500 transition-colors p-2 hover:bg-white rounded-lg">
                                    <i class="pi pi-sign-out"></i>
                                </button>
                            </div>

                            <!-- Empty State Team -->
                            <div v-if="filteredTeam(campaign).length === 0" class="col-span-full text-center py-10">
                                <i class="pi pi-users text-slate-100 text-4xl mb-3"></i>
                                <p class="text-sm text-slate-400 font-medium">Aucun membre ne correspond à votre recherche</p>
                            </div>
                        </div>
                    </div>

                    <!-- Si aucune campagne -->
                    <div v-if="campaignsTree.length === 0" class="bg-white rounded-3xl border border-dashed border-orange-200 p-20 text-center">
                        <i class="pi pi-folder-open text-orange-100 text-6xl mb-4"></i>
                        <p class="text-slate-400 font-bold uppercase tracking-[0.2em]">Aucune campagne assignée</p>
                    </div>
                </div>

                <!-- Section Vivier TC (Sidebar) -->
                <aside class="w-full xl:w-80">
                    <div class="bg-white rounded-3xl border border-orange-100 p-8 shadow-sm sticky top-8">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-2">
                                <div class="w-1.5 h-4 bg-orange-500 rounded-full"></div>
                                <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider">Vivier TC</h3>
                            </div>
                            <Tag :value="filteredVivier.length" severity="warning" class="!bg-orange-50 !text-orange-600" />
                        </div>

                        <!-- Recherche Vivier -->
                        <div class="relative mb-6">
                            <i class="pi pi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <InputText v-model="searchQueryVivier" placeholder="Rechercher un TC..." class="w-full !pl-9 !py-2.5 !text-xs !rounded-xl !bg-slate-50 !border-slate-100 focus:!ring-orange-500" />
                        </div>

                        <div class="space-y-2 max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
                            <div v-for="emp in filteredVivier" :key="emp.id" class="flex justify-between items-center p-4 hover:bg-orange-50 rounded-xl transition-all border border-transparent hover:border-orange-100 group">
                                <div class="flex flex-col">
                                    <span class="text-xs font-black text-slate-700 uppercase tracking-tight">{{ emp.first_name }} {{ emp.last_name }}</span>
                                    <span class="text-[9px] text-orange-400 font-bold uppercase">Disponible</span>
                                </div>
                                <button @click="openAssignModal(emp)" class="text-orange-500 opacity-0 group-hover:opacity-100 transition-all hover:scale-110">
                                    <i class="pi pi-plus-circle text-lg"></i>
                                </button>
                            </div>

                            <!-- Empty State Vivier -->
                            <div v-if="filteredVivier.length === 0" class="text-center py-10">
                                <i class="pi pi-search-minus text-slate-200 text-3xl mb-3"></i>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Aucun TC disponible</p>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>

        <!-- Modal d'affectation pour le SUP -->
        <Dialog v-model:visible="displayModal" header="Ajouter à mon équipe" :style="{ width: '400px' }" modal class="modern-dialog">
            <div class="p-6 space-y-6">
                <div class="bg-orange-50 p-6 rounded-2xl text-center border border-orange-100">
                    <p class="text-[10px] font-black text-orange-400 uppercase tracking-widest mb-1">Affecter le TC</p>
                    <h3 class="text-xl font-black text-slate-800 uppercase tracking-tighter">
                        {{ selectedEmployee?.first_name }} {{ selectedEmployee?.last_name }}
                    </h3>
                </div>

                <div class="space-y-4">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Campagne cible</label>
                        <Select v-model="form.campaign_id" :options="campaigns" optionLabel="name" optionValue="id" placeholder="Sélectionner la campagne" class="w-full !rounded-xl" />
                    </div>
                </div>

                <button 
                    @click="form.post(route('assignments.store'), { 
                        onSuccess: () => {
                            displayModal = false;
                            toast.add({ severity: 'success', summary: 'Affecté !', detail: 'Le TC a rejoint votre équipe.', life: 3000 });
                        }
                    })" 
                    :disabled="!form.campaign_id || form.processing"
                    class="w-full bg-slate-900 hover:bg-orange-600 text-white font-black py-4 rounded-2xl transition-all shadow-lg shadow-orange-100 disabled:opacity-30 uppercase tracking-widest text-xs"
                >
                    Confirmer l'affectation
                </button>
            </div>
        </Dialog>
    </SUPLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #FF7A1A;
    border-radius: 10px;
}
</style>