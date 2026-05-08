<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, computed } from "vue";
import { useForm, router, Head } from "@inertiajs/vue3";
import Dialog from "primevue/dialog";
import Select from "primevue/select";
import Tag from "primevue/tag";
import Toast from "primevue/toast";
import { useToast } from "primevue/usetoast";
import CPLayout from "@/Layouts/CPLayout.vue";
import ConfirmDialog from "primevue/confirmdialog";
import { useConfirm } from "primevue/useconfirm";
import InputText from "primevue/inputtext";

const props = defineProps({
    campaignsTree: Array,
    availableEmployees: Array,
    campaigns: Array,
});

const toast = useToast();
const confirm = useConfirm();
const displayModal = ref(false);
const selectedEmployee = ref(null);
const searchQuery = ref("");

const form = useForm({
    employee_id: "",
    campaign_id: null,
    manager_id: null,
    position_id: null,
    reason: "Affectation par CP"
});

const filteredVivier = computed(() => {
    return props.availableEmployees.filter(emp => {
        const nameMatch = (emp.first_name + " " + emp.last_name).toLowerCase().includes(searchQuery.value.toLowerCase());
        const roleMatch = [2, 3].includes(emp.position_id);
        return nameMatch && roleMatch;
    }).sort((a, b) => a.position_id - b.position_id || a.first_name.localeCompare(b.first_name));
});

const availableManagers = computed(() => {
    const campaign = props.campaignsTree.find(c => c.id === form.campaign_id);
    return campaign ? campaign.assignments.filter(a => a.position_id === 2) : [];
});

const getSUPs = (campaign) => campaign.assignments?.filter(a => a.position_id === 2) || [];
const getTCs = (campaign, supId) => campaign.assignments?.filter(a => a.position_id === 3 && a.manager_id === supId) || [];

const openAssignModal = (emp) => {
    form.reset();
    selectedEmployee.value = emp;
    form.employee_id = emp.id;
    form.position_id = emp.position_id;
    displayModal.value = true;
};

const handleRelease = (id) => {
    confirm.require({
        message: 'Êtes-vous sûr de vouloir libérer ce membre ? Cette action peut impacter son équipe.',
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
                onSuccess: () => toast.add({ severity: 'success', summary: 'Succès', detail: 'Agent libéré', life: 3000 })
            });
        }
    });
};
</script>

<template>
    <Head title="Gestion de mes équipes" />
    <ConfirmDialog />
    <CPLayout>
        <Toast />
        <div class="p-8 bg-[#FBFDFF] min-h-screen">
            <div class="flex flex-col xl:flex-row gap-8 max-w-[1600px] mx-auto">
                
                <div class="flex-1 space-y-6">
                    <h1 class="text-2xl font-black text-slate-900 tracking-tighter uppercase">Gestion de mes équipes</h1>
                    
                    <div v-for="campaign in campaignsTree" :key="campaign.id" class="bg-white rounded-3xl border border-orange-100 p-8 shadow-sm">
                        <h2 class="text-xl font-bold mb-6 flex items-center gap-3">
                            <div class="w-2 h-6 bg-orange-500 rounded-full"></div>
                            {{ campaign.name }}
                        </h2>
                        
                        <div v-for="sup in getSUPs(campaign)" :key="sup.id" class="bg-orange-50/30 rounded-2xl p-6 mb-6 border border-orange-100">
                            <div class="flex justify-between items-center mb-6 pb-2 border-b border-orange-100">
                                <div class="flex items-center gap-2">
                                    <div class="p-2 bg-orange-100 rounded-lg text-orange-600">
                                        <i class="pi pi-user text-xs"></i>
                                    </div>
                                    <span class="font-black text-slate-800 uppercase text-xs tracking-wider">Équipe de {{ sup.employee.first_name }} (SUP)</span>
                                </div>
                                <button @click="handleRelease(sup.id)" class="text-[10px] text-red-500 font-black uppercase hover:bg-red-50 px-3 py-1 rounded-full transition-colors border border-red-100">Libérer SUP</button>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                <div v-for="tc in getTCs(campaign, sup.employee_id)" :key="tc.id" class="bg-white p-4 rounded-xl border border-orange-100 flex justify-between items-center group hover:border-orange-300 transition-all">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-[10px] text-slate-400 font-bold">
                                            {{ tc.employee.first_name.charAt(0) }}
                                        </div>
                                        <span class="text-xs font-bold text-slate-700">{{ tc.employee.first_name }}</span>
                                    </div>
                                    <button @click="handleRelease(tc.id)" class="text-slate-200 hover:text-red-500 transition-colors"><i class="pi pi-times text-[10px]"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <aside class="w-full xl:w-80">
                    <div class="bg-white rounded-3xl border border-orange-100 p-8 shadow-sm sticky top-8">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-2">
                                <div class="w-1.5 h-4 bg-orange-500 rounded-full"></div>
                                <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider">Vivier</h3>
                            </div>
                            <Tag :value="filteredVivier.length" severity="warning" class="!bg-orange-50 !text-orange-600" />
                        </div>

                        <!-- Barre de recherche -->
                        <div class="relative mb-6">
                            <i class="pi pi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <InputText v-model="searchQuery" placeholder="Rechercher SUP/TC..." class="w-full !pl-9 !py-2.5 !text-xs !rounded-xl !bg-slate-50 !border-slate-100 focus:!ring-orange-500" />
                        </div>

                        <div class="space-y-2 max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
                            <div v-for="emp in filteredVivier" :key="emp.id" class="flex justify-between items-center p-4 hover:bg-orange-50 rounded-xl transition-all border border-transparent hover:border-orange-100 group">
                                <div class="flex flex-col">
                                    <span class="text-xs font-black text-slate-700 uppercase tracking-tight">{{ emp.first_name }} {{ emp.last_name }}</span>
                                    <span class="text-[9px] text-orange-500 font-bold uppercase tracking-widest mt-0.5">{{ emp.position_id === 2 ? 'Superviseur' : 'Téléconseiller' }}</span>
                                </div>
                                <button @click="openAssignModal(emp)" class="text-orange-500 opacity-0 group-hover:opacity-100 transition-all hover:scale-110">
                                    <i class="pi pi-plus-circle text-lg"></i>
                                </button>
                            </div>

                            <!-- Empty State Vivier -->
                            <div v-if="filteredVivier.length === 0" class="text-center py-10">
                                <i class="pi pi-users text-slate-200 text-3xl mb-3"></i>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Aucun profil disponible</p>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>

        <Dialog v-model:visible="displayModal" header="Affecter à mon équipe" :style="{ width: '25vw' }" modal>
            <div class="flex flex-col gap-4 py-4">
                <Select v-model="form.campaign_id" :options="campaigns" optionLabel="name" optionValue="id" placeholder="Campagne" class="w-full" />
                <Select v-if="form.position_id === 3" v-model="form.manager_id" :options="availableManagers" :optionLabel="opt => opt.employee.first_name" optionValue="employee_id" placeholder="Choisir le Superviseur" class="w-full" />
                <button @click="form.post(route('assignments.store'), { onSuccess: () => displayModal = false })" class="bg-slate-900 text-white p-3 rounded-xl font-bold">VALIDER</button>
            </div>
        </Dialog>
    </CPLayout>
</template>