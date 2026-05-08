<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { ref, computed, watch, onMounted} from "vue";
import { useForm, router } from "@inertiajs/vue3";
import Dialog from "primevue/dialog";
import Select from "primevue/select";
import ConfirmDialog from "primevue/confirmdialog";
import { useConfirm } from "primevue/useconfirm";
import Tag from "primevue/tag";
import { useToast } from "primevue/usetoast";

const toast = useToast();

const props = defineProps({
    campaignsTree: Array,
    availableEmployees: Array,
    campaigns: Array,
});
const searchQuery = ref("");
const selectedPositionFilter = ref(null);
const confirm = useConfirm();
const displayModal = ref(false);
const selectedEmployee = ref(null);

const form = useForm({
    employee_id: "",
    campaign_id: null,
    manager_id: null,
    position_id: null,
});

// --- LOGIQUE DYNAMIQUE DU FORMULAIRE ---
const selectedCampaignData = computed(() => {
    return props.campaignsTree.find((c) => c.id === form.campaign_id);
});

const availableManagers = computed(() => {
    if (!selectedCampaignData.value || !form.position_id) return [];
    if (form.position_id === 2) {
        return selectedCampaignData.value.assignments.filter(
            (a) => a.position_id === 1,
        );
    }
    if (form.position_id === 3) {
        return selectedCampaignData.value.assignments.filter(
            (a) => a.position_id === 2,
        );
    }
    return [];
});

watch(
    () => [form.campaign_id, form.position_id],
    () => {
        form.manager_id = null;
    },
);

// --- HELPERS D'AFFICHAGE PLATEAU ---
const getCPs = (campaign) =>
    campaign.assignments?.filter((a) => a.position_id === 1) || [];
const getSUPs = (campaign, cpId) =>
    campaign.assignments?.filter(
        (a) => a.position_id === 2 && a.manager_id === cpId,
    ) || [];
const getTCs = (campaign, supId) =>
    campaign.assignments?.filter(
        (a) => a.position_id === 3 && a.manager_id === supId,
    ) || [];

const activeCampaigns = computed(
    () => props.campaignsTree?.filter((c) => c.assignments?.length > 0) || [],
);

const getCampaignStats = (campaign) => {
    const total = campaign.assignments?.length || 0;
    const tcs =
        campaign.assignments?.filter((a) => a.position_id === 3).length || 0;
    return { total, tcs };
};

const formatDate = (dateString) => {
    if (!dateString) return "Récents";
    return new Date(dateString).toLocaleDateString("fr-FR", {
        day: "numeric",
        month: "short",
    });
};

const vivierByPosition = computed(() => {
    const assignedIds =
        props.campaignsTree?.reduce((acc, campaign) => {
            if (campaign.assignments) {
                campaign.assignments.forEach((a) => acc.push(a.employee_id));
            }
            return acc;
        }, []) || [];

    const available =
        props.availableEmployees?.filter((emp) => {
            const isNotAssigned = !assignedIds.includes(emp.id);
            const matchesSearch = (emp.first_name + " " + emp.last_name)
                .toLowerCase()
                .includes(searchQuery.value.toLowerCase());

            // FILTRE PAR POSTE
            const matchesPosition = selectedPositionFilter.value
                ? emp.position_id === selectedPositionFilter.value
                : true;

            return isNotAssigned && matchesSearch && matchesPosition;
        }) || [];

    return {
        CP: available.filter((e) => e.position_id === 1),
        SUP: available.filter((e) => e.position_id === 2),
        TC: available.filter((e) => e.position_id === 3),
    };
});

const openAssignModal = (emp) => {
    form.reset();
    selectedEmployee.value = emp;
    form.employee_id = emp.id;
    form.position_id = emp.position_id;
    displayModal.value = true;
};

const confirmRelease = (id) => {
    confirm.require({
        header: "Désaffectation",
        message: "L'agent sera remis dans le vivier disponible.",
        icon: "pi pi-info-circle",
        acceptLabel: "Confirmer",
        acceptClass: "p-button-danger p-button-sm",
        accept: () => router.delete(route("assignments.release", id)),
    });
};

onMounted(() => {
    toast.add({
        severity: "info",
        summary: "Nettoyage",
        detail: "Chargement des Assignations",
        life: 3000,
    });
});
</script>

<template>
    <ConfirmDialog />
    <AdminLayout>
    <Toast/>
    <div class="p-8 bg-[#FBFDFF] min-h-screen font-sans text-slate-700">
        <div class="flex flex-col xl:flex-row gap-10 max-w-[1800px] mx-auto">
            <div class="flex-1 space-y-10">
                <header
                    class="flex justify-between items-center pb-2 border-b border-slate-100"
                >
                    <div>
                        <p
                            class="text-xs font-semibold text-orange-500 uppercase tracking-widest"
                        >
                            Wip Lite
                        </p>
                        <h1
                            class="text-3xl font-extrabold text-slate-900 tracking-tighter"
                        >
                            Plateau de Production
                        </h1>
                    </div>
                    <Tag
                        :value="`${activeCampaigns.length} Campagnes Actives`"
                        severity="secondary"
                        class="!bg-slate-100 !text-slate-600 !rounded-full !px-4"
                    />
                </header>

                <div
                    v-for="campaign in activeCampaigns"
                    :key="campaign.id"
                    class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8"
                >
                    <div
                        class="flex justify-between items-center mb-8 pb-4 border-b border-slate-100"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="p-2.5 bg-slate-100 rounded-xl text-slate-500"
                            >
                                <i class="pi pi-building text-lg"></i>
                            </div>
                            <h2
                                class="text-2xl font-bold text-slate-800 tracking-tight"
                            >
                                {{ campaign.name }}
                            </h2>
                        </div>
                        <div
                            class="flex gap-3 items-center bg-slate-50 px-4 py-2 rounded-full border border-slate-100"
                        >
                            <span class="text-sm font-medium text-slate-500"
                                >{{
                                    getCampaignStats(campaign).total
                                }}
                                Collaborateurs</span
                            >
                            <span class="text-slate-200">|</span>
                            <span class="text-sm font-semibold text-orange-600"
                                >{{ getCampaignStats(campaign).tcs }} TC</span
                            >
                        </div>
                    </div>

                    <div class="space-y-8">
                        <div
                            v-for="cp in getCPs(campaign)"
                            :key="cp.id"
                            class="space-y-8"
                        >
                            <div
                                class="grid grid-cols-1 md:grid-cols-[280px,1fr] gap-8 items-start"
                            >
                                <div
                                    class="flex flex-col items-center justify-center p-6 bg-orange-50 rounded-2xl border border-orange-100 text-center shadow-inner-sm"
                                >
                                    <div
                                        class="w-16 h-16 rounded-full bg-orange-500 flex items-center justify-center text-white mb-4 shadow-lg shadow-orange-100"
                                    >
                                        <i class="pi pi-star-fill text-2xl"></i>
                                    </div>
                                    <p
                                        class="font-bold text-slate-900 text-lg leading-tight"
                                    >
                                        {{ cp.employee.first_name }}
                                        {{ cp.employee.last_name }}
                                    </p>
                                    <p
                                        class="text-[11px] text-orange-700 font-semibold uppercase tracking-wider mt-1"
                                    >
                                        Chef de Projet
                                    </p>
                                    <p class="text-[10px] text-slate-400 mt-3">
                                        Depuis le
                                        {{ formatDate(cp.created_at) }}
                                    </p>
                                    <button
                                        @click="confirmRelease(cp.id)"
                                        class="mt-4 text-xs p-button-text p-button-secondary p-button-sm"
                                    >
                                        <i class="pi pi-sign-out mr-1"></i>
                                        Désaffecter
                                    </button>
                                </div>

                                <div class="space-y-6">
                                    <div
                                        v-if="
                                            getSUPs(campaign, cp.employee_id)
                                                .length === 0
                                        "
                                        class="flex items-center justify-center h-full border-2 border-dashed border-slate-100 rounded-2xl py-10 text-slate-400 text-sm"
                                    >
                                        Aucune équipe rattachée
                                    </div>

                                    <div
                                        v-for="sup in getSUPs(
                                            campaign,
                                            cp.employee_id,
                                        )"
                                        :key="sup.id"
                                        class="bg-slate-50 rounded-2xl p-6 border border-slate-100 shadow-sm"
                                    >
                                        <div
                                            class="flex items-center justify-between mb-5 pb-3 border-b border-slate-200/60"
                                        >
                                            <div
                                                class="flex items-center gap-3"
                                            >
                                                <i
                                                    class="pi pi-user text-emerald-500"
                                                ></i>
                                                <span
                                                    class="font-semibold text-slate-800"
                                                    >{{
                                                        sup.employee.first_name
                                                    }}
                                                    {{
                                                        sup.employee.last_name
                                                    }}</span
                                                >
                                                <Tag
                                                    value="SUP"
                                                    severity="success"
                                                    class="!text-[9px] !px-2 !py-0.5"
                                                />
                                            </div>
                                            <button
                                                @click="confirmRelease(sup.id)"
                                                class="text-slate-400 hover:text-red-500"
                                            >
                                                <i
                                                    class="pi pi-times-circle"
                                                ></i>
                                            </button>
                                        </div>

                                        <div
                                            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3"
                                        >
                                            <div
                                                v-for="tc in getTCs(
                                                    campaign,
                                                    sup.employee_id,
                                                )"
                                                :key="tc.id"
                                                class="p-3.5 bg-white border border-slate-100 rounded-xl group flex justify-between items-center shadow-sm hover:border-orange-100 hover:shadow-md transition-all"
                                            >
                                                <div class="flex flex-col">
                                                    <span
                                                        class="text-sm font-semibold text-slate-700"
                                                        >{{
                                                            tc.employee
                                                                .first_name
                                                        }}</span
                                                    >
                                                    <span
                                                        class="text-[10px] text-slate-400"
                                                        >Le
                                                        {{
                                                            formatDate(
                                                                tc.created_at,
                                                            )
                                                        }}</span
                                                    >
                                                </div>
                                                <button
                                                    @click="
                                                        confirmRelease(tc.id)
                                                    "
                                                    class="opacity-0 group-hover:opacity-100 text-slate-300 hover:text-red-400 transition-opacity"
                                                >
                                                    <i
                                                        class="pi pi-trash text-[11px]"
                                                    ></i>
                                                </button>
                                            </div>
                                            <div
                                                v-if="
                                                    getTCs(
                                                        campaign,
                                                        sup.employee_id,
                                                    ).length === 0
                                                "
                                                class="col-span-full text-center py-4 text-xs text-slate-400 italic"
                                            >
                                                Pas encore de téléconseillers
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <aside class="w-full xl:w-96 flex-shrink-0">
                <div
                    class="bg-white rounded-3xl border border-slate-100 shadow-sm p-7 sticky top-10"
                >
                    <div
                        class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100"
                    >
                        <div
                            class="p-3 bg-orange-50 rounded-xl text-orange-500"
                        >
                            <i class="pi pi-users text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800">
                            Employés Disponible
                        </h3>
                    </div>
                    <div class="relative mb-4">
                        <i
                            class="pi pi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"
                        ></i>
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Rechercher un nom..."
                            class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition-all"
                        />
                    </div>

                    <div class="flex gap-2 mb-6">
                        <button
                            @click="selectedPositionFilter = null"
                            :class="[
                                'flex-1 py-2 text-[9px] font-black rounded-xl border transition-all tracking-widest',
                                selectedPositionFilter === null
                                    ? 'bg-slate-900 text-white border-slate-900 shadow-md'
                                    : 'bg-white text-slate-400 border-slate-100 hover:border-slate-300',
                            ]"
                        >
                            TOUS
                        </button>
                        <button
                            v-for="pos in [
                                { l: 'CP', v: 1 },
                                { l: 'SUP', v: 2 },
                                { l: 'TC', v: 3 },
                            ]"
                            :key="pos.v"
                            @click="selectedPositionFilter = pos.v"
                            :class="[
                                'flex-1 py-2 text-[9px] font-black rounded-xl border transition-all tracking-widest',
                                selectedPositionFilter === pos.v
                                    ? 'bg-[#FF7A1A] text-white border-[#FF7A1A] shadow-md shadow-orange-100'
                                    : 'bg-white text-slate-400 border-slate-100 hover:border-slate-300',
                            ]"
                        >
                            {{ pos.l }}
                        </button>
                    </div>
                    <!-- BARRE DE RECHERCHE DINGUERIE -->
                    <div class="relative mb-6">
                        <i
                            class="pi pi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"
                        ></i>
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Rechercher un nom..."
                            class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition-all"
                        />
                    </div>

                    <div
                        class="space-y-6 max-h-[75vh] overflow-y-auto pr-3 custom-scrollbar"
                    >
                        <div
                            v-for="(emps, role) in vivierByPosition"
                            :key="role"
                        >
                            <p
                                v-if="emps && emps.length > 0"
                                class="text-[10px] font-black uppercase text-slate-400 mb-3 tracking-[0.2em]"
                            >
                                {{ role }} —
                                <span class="text-orange-500">{{
                                    emps.length
                                }}</span>
                            </p>

                            <div class="space-y-2">
                                <div
                                    v-for="emp in emps"
                                    :key="emp.id"
                                    class="flex justify-between items-center p-4 bg-white hover:bg-slate-50 rounded-2xl border border-slate-100 transition-all duration-200 group"
                                >
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm font-bold text-slate-700 group-hover:text-slate-900"
                                        >
                                            {{ emp.first_name }}
                                            {{ emp.last_name }}
                                        </span>
                                        <span
                                            class="text-[10px] text-slate-400 font-medium"
                                        >
                                            ID: #{{ emp.id }} • Disponible
                                        </span>
                                    </div>

                                    <button
                                        @click="openAssignModal(emp)"
                                        class="w-9 h-9 flex items-center justify-center bg-slate-50 text-slate-400 rounded-xl group-hover:bg-[#FF7A1A] group-hover:text-white transition-all shadow-sm"
                                    >
                                        <i class="pi pi-user-plus text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="
                                searchQuery &&
                                Object.values(vivierByPosition).every(
                                    (a) => a.length === 0,
                                )
                            "
                            class="text-center py-10"
                        >
                            <i
                                class="pi pi-search text-slate-200 text-3xl mb-2"
                            ></i>
                            <p class="text-xs text-slate-400 italic">
                                Aucun résultat pour "{{ searchQuery }}"
                            </p>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <Dialog
        v-model:visible="displayModal"
        modal
        header="Affectation"
        :style="{ width: '32rem' }"
        :pt="{
            root: 'rounded-3xl border-none',
            header: 'px-8 pt-8 pb-4 border-none',
        }"
    >
        <div class="p-4 space-y-6">
            <div
                class="bg-slate-50 p-6 rounded-2xl text-center border border-slate-100 flex flex-col items-center"
            >
                <div
                    class="w-16 h-16 bg-white rounded-full flex items-center justify-center text-slate-300 border border-slate-100 mb-4 shadow-inner"
                >
                    <i class="pi pi-user text-3xl"></i>
                </div>
                <p
                    class="text-xs text-slate-500 uppercase font-semibold tracking-wider"
                >
                    Affecter
                </p>
                <h3
                    class="text-2xl font-extrabold text-slate-900 tracking-tight"
                >
                    {{ selectedEmployee?.first_name }}
                    {{ selectedEmployee?.last_name }}
                </h3>
            </div>

            <div class="space-y-5">
                <div>
                    <label
                        class="text-xs font-semibold text-slate-500 mb-1.5 block"
                        >1. Campagne</label
                    >
                    <Select
                        v-model="form.campaign_id"
                        :options="campaigns"
                        optionLabel="name"
                        optionValue="id"
                        placeholder="Choisir une campagne"
                        class="w-full !rounded-xl h-12"
                    />
                </div>

                <div>
                    <label
                        class="text-xs font-semibold text-slate-500 mb-1.5 block"
                        >2. Rôle</label
                    >
                    <Select
                        v-model="form.position_id"
                        :options="[
                            { l: 'Chef de Projet', v: 1 },
                            { l: 'Superviseur', v: 2 },
                            { l: 'Téléconseiller', v: 3 },
                        ]"
                        optionLabel="l"
                        optionValue="v"
                        placeholder="Définir le poste"
                        class="w-full !rounded-xl h-12"
                    />
                </div>

                <div v-if="form.position_id > 1">
                    <label
                        class="text-xs font-semibold text-slate-500 mb-1.5 block"
                    >
                        3. Rattacher au
                        {{
                            form.position_id === 2
                                ? "Chef de Projet"
                                : "Superviseur"
                        }}
                    </label>
                    <Select
                        v-model="form.manager_id"
                        :options="availableManagers"
                        :optionLabel="
                            (opt) =>
                                `${opt.employee.first_name} ${opt.employee.last_name}`
                        "
                        optionValue="employee_id"
                        :placeholder="
                            availableManagers.length
                                ? 'Choisir le responsable...'
                                : 'Aucun manager disponible'
                        "
                        class="w-full !rounded-xl h-12"
                        :disabled="!availableManagers.length"
                    />
                </div>
            </div>

            <button
                @click="
                    form.post(route('assignments.store'), {
                        onSuccess: () => (displayModal = false),
                    })
                "
                :disabled="
                    !form.campaign_id ||
                    !form.position_id ||
                    (form.position_id > 1 && !form.manager_id)
                "
                class="w-full bg-slate-900 text-white font-bold py-4 rounded-2xl disabled:opacity-30 hover:bg-orange-600 transition-colors shadow-lg mt-4"
            >
                VALIDER L'AFFECTATION
            </button>
        </div>
    </Dialog>
    </AdminLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #cbd5e1;
}

:deep(.p-tag) {
    font-size: 10px;
    font-weight: 700;
}
:deep(.p-select) {
    border-color: #e2e8f0;
}
</style>
