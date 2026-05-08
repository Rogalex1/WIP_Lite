<script setup>
import { ref, watch, onMounted } from "vue";
import { Link, router, usePage, Head } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Button from "primevue/button";
import Column from "primevue/column";
import DataTable from "primevue/datatable";
import InputText from "primevue/inputtext";
import Dropdown from "primevue/dropdown";
import Tag from "primevue/tag";
import Dialog from "primevue/dialog";
import Toolbar from "primevue/toolbar";
import ConfirmDialog from "primevue/confirmdialog";
import { useConfirm } from "primevue/useconfirm";

// Props depuis le contrôleur
const props = defineProps({
    employees: Object,
    positions: Array,
    filters: Object,
});

// État réactif
const toast = useToast();
const confirm = useConfirm();
const statusChangeDialog = ref(false);
const statusChangeData = ref({ employee: null, newStatus: "" });

// Filtres
const search = ref(props.filters.search || "");
const status = ref(props.filters.status || "");
const position_id = ref(props.filters.position_id || "");
// Pagination (synchronisée avec la pagination Laravel)
const page = ref(props.employees.current_page || 1);
const rows = ref(props.employees.per_page || 10);

// Options pour les filtres
const statusOptions = [
    { label: "Tous les statuts", value: "" },
    { label: "Actif", value: "actif" },
    { label: "Inactif", value: "inactif" },
    { label: "Suspendu", value: "suspendu" },
];

const positionOptions = [
    { label: "Tous les postes", value: "" },
    ...props.positions.map((position) => ({
        label: position.name,
        value: position.id,
    })),
];

// Afficher les messages flash
onMounted(() => {
    const page = usePage();

    // Afficher les messages de succès
    if (page.props.flash?.success) {
        toast.add({
            severity: "success",
            summary: "Succès",
            detail: page.props.flash.success,
            life: 3000,
        });
    }

    // Afficher les messages d'erreur
    if (page.props.flash?.error) {
        toast.add({
            severity: "error",
            summary: "Erreur",
            detail: page.props.flash.error,
            life: 3000,
        });
    }

    // Afficher les messages d'information
    if (page.props.flash?.info) {
        toast.add({
            severity: "info",
            summary: "Information",
            detail: page.props.flash.info,
            life: 3000,
        });
    }
});

// Appliquer les filtres (page et per_page)
const applyFilters = (p = 1, perPage = rows.value) => {
    page.value = p;
    rows.value = perPage;

    router.get(
        route("employees.index"),
        {
            search: search.value,
            status: status.value,
            position_id: position_id.value,
            page: p,
            per_page: perPage,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
};

// Handler pour le composant DataTable (PrimeVue) qui émet un event { first, rows, page }
const onPage = (event) => {
    const newPage =
        event && typeof event.page === "number" ? event.page + 1 : 1; // PrimeVue page is 0-based
    const newRows =
        event && typeof event.rows === "number" ? event.rows : rows.value;
    applyFilters(newPage, newRows);
};

// Watch pour appliquer les filtres automatiquement
watch([search, status, position_id], () => {
    // quand on change les filtres, revenir à la première page
    applyFilters(1);
});

// Obtenir la couleur du statut
const getStatusColor = (status) => {
    switch (status) {
        case "actif":
            return "success";
        case "inactif":
            return "secondary";
        case "suspendu":
            return "danger";
        default:
            return "info";
    }
};

// Obtenir le libellé du statut
const getStatusLabel = (status) => {
    switch (status) {
        case "actif":
            return "Actif";
        case "inactif":
            return "Inactif";
        case "suspendu":
            return "Suspendu";
        default:
            return status;
    }
};

// Confirmer la suppression
const confirmDelete = (employee) => {
    confirm.require({
        message: `Êtes-vous sûr de vouloir supprimer l'employé ${employee.first_name} ${employee.last_name} ?`,
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
            router.delete(route("employees.destroy", employee.id), {
                onSuccess: () => {
                    toast.add({
                        severity: "success",
                        summary: "Succès",
                        detail: "Employé supprimé avec succès",
                        life: 3000,
                    });
                },
                onError: () => {
                    toast.add({
                        severity: "error",
                        summary: "Erreur",
                        detail: "Une erreur est survenue lors de la suppression",
                        life: 3000,
                    });
                },
            });
        }
    });
};

// Confirmer le changement de statut
const confirmStatusChange = (employee, newStatus) => {
    statusChangeData.value = { employee, newStatus };
    statusChangeDialog.value = true;
};

// Exécuter le changement de statut
const executeStatusChange = () => {
    const { employee, newStatus } = statusChangeData.value;

    router.patch(
        route("employees.changeStatus", employee.id),
        { status: newStatus },
        {
            onSuccess: () => {
                statusChangeDialog.value = false;
                statusChangeData.value = { employee: null, newStatus: "" };
                toast.add({
                    severity: "success",
                    summary: "Succès",
                    detail: `Statut mis à jour : ${getStatusLabel(newStatus)}`,
                    life: 3000,
                });
            },
            onError: () => {
                statusChangeDialog.value = false;
                statusChangeData.value = { employee: null, newStatus: "" };
                toast.add({
                    severity: "error",
                    summary: "Erreur",
                    detail: "Une erreur est survenue lors de la mise à jour du statut",
                    life: 3000,
                });
            },
        },
    );
};
</script>

<template>
    <Head title="Gestion des Employés" />
    <ConfirmDialog />

    <AdminLayout>
        <div class="space-y-8">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-8 bg-[#FF7A1A] rounded-full"></div>
                    <h2 class="text-xl font-black text-slate-800 tracking-tighter uppercase">
                        Gestion des Collaborateurs
                    </h2>
                </div>
                <Link :href="route('employees.create')">
                    <button class="bg-[#FF7A1A] hover:bg-orange-600 text-white font-black py-3 px-6 rounded-xl transition-all shadow-lg shadow-orange-200 text-xs tracking-widest uppercase flex items-center gap-2">
                        <i class="pi pi-user-plus"></i>
                        Ajouter un collaborateur
                    </button>
                </Link>
            </div>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <!-- Filtres -->
                    <Toolbar class="mb-4">
                        <template #start>
                            <div class="flex gap-2 items-center">
                                <span class="p-input-icon-left">
                                    <i class="pi pi-search" />
                                    <InputText
                                        v-model="search"
                                        placeholder="Rechercher..."
                                        class="w-64"
                                    />
                                </span>

                                <Dropdown
                                    v-model="status"
                                    :options="statusOptions"
                                    optionLabel="label"
                                    optionValue="value"
                                    placeholder="Statut"
                                    class="w-40"
                                />

                                <Dropdown
                                    v-model="position_id"
                                    :options="positionOptions"
                                    optionLabel="label"
                                    optionValue="value"
                                    placeholder="Poste"
                                    class="w-40"
                                />
                            </div>
                        </template>
                    </Toolbar>

                    <!-- Tableau des employés -->
                    <DataTable
                        :value="employees.data"
                        :paginator="true"
                        :rows="rows"
                        :first="(page - 1) * rows"
                        :totalRecords="employees.total"
                        :lazy="true"
                        @page="onPage"
                        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                        currentPageReportTemplate="Affichage de {first} à {last} sur {totalRecords} employés"
                        class="p-datatable-sm"
                        stripedRows
                        responsiveLayout="scroll"
                    >
                        <Column field="matricule" header="Matricule" sortable>
                            <template #body="{ data }">
                                <span class="font-mono text-sm">{{
                                    data.matricule
                                }}</span>
                            </template>
                        </Column>

                        <Column field="full_name" header="Nom complet" sortable>
                            <template #body="{ data }">
                                <div class="flex items-center">
                                    <div>
                                        <div class="font-medium">
                                            {{ data.first_name }}
                                            {{ data.last_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ data.email }}
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </Column>

                        <Column field="position.name" header="Poste" sortable>
                            <template #body="{ data }">
                                {{ data.position?.name || "-" }}
                            </template>
                        </Column>

                        <Column field="phone" header="Téléphone">
                            <template #body="{ data }">
                                {{ data.phone || "-" }}
                            </template>
                        </Column>

                        <Column field="salary_base" header="Salaire" sortable>
                            <template #body="{ data }">
                                <span class="font-medium">
                                    {{
                                        new Intl.NumberFormat("fr-FR", {
                                            style: "currency",
                                            currency: "XOF",
                                        }).format(data.salary_base)
                                    }}
                                </span>
                            </template>
                        </Column>

                        <Column field="status" header="Statut" sortable>
                            <template #body="{ data }">
                                <Tag
                                    :value="getStatusLabel(data.status)"
                                    :severity="getStatusColor(data.status)"
                                />
                            </template>
                        </Column>

                        <Column
                            header="Actions"
                            :exportable="false"
                            style="min-width: 8rem"
                        >
                            <template #body="{ data }">
                                <div class="flex gap-2 items-center">
                                    <Link
                                        :href="route('employees.show', data.id)"
                                    >
                                        <Button
                                            label="Voir"
                                            icon="pi pi-eye"
                                            class="p-button-outlined p-button-info"
                                        />
                                    </Link>

                                    <Link
                                        :href="route('employees.edit', data.id)"
                                    >
                                        <Button
                                            label="Modifier"
                                            icon="pi pi-pencil"
                                            class="p-button-outlined p-button-warning"
                                        />
                                    </Link>

                                    <Dropdown
                                        :modelValue="data.status"
                                        :options="[
                                            { label: 'Actif', value: 'actif' },
                                            {
                                                label: 'Inactif',
                                                value: 'inactif',
                                            },
                                            {
                                                label: 'Suspendu',
                                                value: 'suspendu',
                                            },
                                        ]"
                                        optionLabel="label"
                                        optionValue="value"
                                        @change="
                                            confirmStatusChange(
                                                data,
                                                $event.value,
                                            )
                                        "
                                        class="w-32"
                                        size="small"
                                    />

                                    <Button
                                        label="Supprimer"
                                        icon="pi pi-trash"
                                        class="p-button-danger"
                                        @click="confirmDelete(data)"
                                    />
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>

        <!-- Dialog de confirmation de changement de statut -->
        <Dialog
            v-model:visible="statusChangeDialog"
            :style="{ width: '450px' }"
            header="Confirmation de Changement de Statut"
            :modal="true"
        >
            <div class="flex align-items-center justify-content-center">
                <i
                    class="pi pi-question-circle mr-3"
                    style="font-size: 2rem; color: #3b82f6"
                />
                <span v-if="statusChangeData.employee">
                    Changer le statut de
                    <b
                        >{{ statusChangeData.employee.first_name }}
                        {{ statusChangeData.employee.last_name }}</b
                    ><br />
                    de
                    <Tag
                        :value="
                            getStatusLabel(statusChangeData.employee.status)
                        "
                        :severity="
                            getStatusColor(statusChangeData.employee.status)
                        "
                        class="mr-2"
                    />
                    à
                    <Tag
                        :value="getStatusLabel(statusChangeData.newStatus)"
                        :severity="getStatusColor(statusChangeData.newStatus)"
                    />
                </span>
            </div>
            <template #footer>
                <Button
                    label="Annuler"
                    icon="pi pi-times"
                    class="p-button-text"
                    @click="statusChangeDialog = false"
                />
                <Button
                    label="Confirmer"
                    icon="pi pi-check"
                    class="p-button-primary"
                    @click="executeStatusChange"
                />
            </template>
        </Dialog>
        </div>
    </AdminLayout>
</template>
