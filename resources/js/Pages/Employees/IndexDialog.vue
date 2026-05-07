<script setup>
import { ref, watch, onMounted } from "vue";
import { Link, router, Head, useForm, usePage } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import EmployeeForm from "@/Components/EmployeeForm.vue";
import Button from "primevue/button";
import Column from "primevue/column";
import DataTable from "primevue/datatable";
import InputText from "primevue/inputtext";
import Dropdown from "primevue/dropdown";
import Tag from "primevue/tag";
import Dialog from "primevue/dialog";
import Toolbar from "primevue/toolbar";

const props = defineProps({
    employees: Object,
    positions: Array,
    filters: Object,
});

const page = usePage();

const createDialog = ref(false);
const editDialog = ref(false);
const deleteDialog = ref(false);
const statusChangeDialog = ref(false);

const employeeToEdit = ref(null);
const employeeToDelete = ref(null);
const statusChangeData = ref({ employee: null, newStatus: "" });

const search = ref(props.filters.search || "");
const status = ref(props.filters.status || "");
const position_id = ref(props.filters.position_id || "");

const page_num = ref(props.employees.current_page || 1);
const rows = ref(props.employees.per_page || 10);

const isFormLoading = ref(false);

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

onMounted(() => {
    if (page.props.flash?.openCreateDialog) {
        createDialog.value = true;
    }

    if (page.props.flash?.editEmployeeId) {
        const employeeId = page.props.flash.editEmployeeId;
        const employee = props.employees.data.find(e => e.id === employeeId);
        if (employee) {
            employeeToEdit.value = employee;
            editDialog.value = true;
        }
    }
});

const applyFilters = (p = 1, perPage = rows.value) => {
    page_num.value = p;
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

const onPage = (event) => {
    const newPage =
        event && typeof event.page === "number" ? event.page + 1 : 1;
    const newRows =
        event && typeof event.rows === "number" ? event.rows : rows.value;
    applyFilters(newPage, newRows);
};

watch([search, status, position_id], () => {
    applyFilters(1);
});

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

const openCreateDialog = () => {
    createDialog.value = true;
};

const closeCreateDialog = () => {
    createDialog.value = false;
};

const openEditDialog = (employee) => {
    employeeToEdit.value = employee;
    editDialog.value = true;
};

const closeEditDialog = () => {
    editDialog.value = false;
    employeeToEdit.value = null;
};

const handleCreateSubmit = (form) => {
    isFormLoading.value = true;
    form.post(route("employees.store"), {
        onFinish: () => {
            isFormLoading.value = false;
            closeCreateDialog();
        },
        onError: () => {
            isFormLoading.value = false;
        },
    });
};

const handleEditSubmit = (form) => {
    isFormLoading.value = true;
    form.put(route("employees.update", employeeToEdit.value.id), {
        onFinish: () => {
            isFormLoading.value = false;
            closeEditDialog();
        },
        onError: () => {
            isFormLoading.value = false;
        },
    });
};

const confirmDelete = (employee) => {
    employeeToDelete.value = employee;
    deleteDialog.value = true;
};

const deleteEmployee = () => {
    if (employeeToDelete.value) {
        router.delete(route("employees.destroy", employeeToDelete.value.id), {
            onFinish: () => {
                deleteDialog.value = false;
                employeeToDelete.value = null;
            },
        });
    }
};

const confirmStatusChange = (employee, newStatus) => {
    statusChangeData.value = { employee, newStatus };
    statusChangeDialog.value = true;
};

const executeStatusChange = () => {
    const { employee, newStatus } = statusChangeData.value;

    router.patch(
        route("employees.changeStatus", employee.id),
        { status: newStatus },
        {
            onFinish: () => {
                statusChangeDialog.value = false;
                statusChangeData.value = { employee: null, newStatus: "" };
            },
        },
    );
};
</script>

<template>
    <Head title="Employés" />

    <AdminLayout>

            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Gestion des Employés
                </h2>
                <div class="flex gap-2">
                    <Link :href="route('employees.trash')">
                        <Button
                            label="Employés Supprimés"
                            icon="pi pi-trash"
                            class="p-button-warning"
                        />
                    </Link>
                    <Button
                        label="Nouvel Employé"
                        icon="pi pi-plus"
                        class="p-button-primary"
                        @click="openCreateDialog"
                    />
                </div>
            </div>

        <div class="h-screen flex flex-col p-4 overflow-hidden">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg flex flex-col flex-1 w-full">
                <Toolbar class="mb-4 p-4 border-b">
                    <template #start>
                        <div class="flex gap-2 items-center flex-wrap">
                            <span class="p-input-icon-left">
                                <i class="pi pi-search" />
                                <InputText
                                    v-model="search"
                                    placeholder="Rechercher..."
                                    class="w-48"
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

                <div class="flex-1 overflow-auto">
                    <DataTable
                        :value="employees.data"
                        :paginator="true"
                        :rows="rows"
                        :first="(page_num - 1) * rows"
                        :totalRecords="employees.total"
                        :lazy="true"
                        @page="onPage"
                        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                        currentPageReportTemplate="Affichage de {first} à {last} sur {totalRecords} employés"
                        class="p-datatable-sm w-full"
                        stripedRows
                        responsiveLayout="scroll"
                    >
                        <Column field="matricule" header="Matricule" sortable>
                            <template #body="{ data }">
                                <span class="font-mono text-sm">{{ data.matricule }}</span>
                            </template>
                        </Column>

                        <Column field="full_name" header="Nom complet" sortable>
                            <template #body="{ data }">
                                <div>
                                    <div class="font-medium">
                                        {{ data.first_name }} {{ data.last_name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ data.email }}
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

                        <Column header="Actions" :exportable="false" style="min-width: 8rem">
                            <template #body="{ data }">
                                <div class="flex gap-2 items-center">
                                    <Link :href="route('employees.show', data.id)">
                                        <Button
                                            label="Voir"
                                            icon="pi pi-eye"
                                            class="p-button-outlined p-button-info"
                                        />
                                    </Link>

                                    <Button
                                        label="Modifier"
                                        icon="pi pi-pencil"
                                        class="p-button-outlined p-button-warning"
                                        @click="openEditDialog(data)"
                                    />

                                    <Dropdown
                                        :modelValue="data.status"
                                        :options="[
                                            { label: 'Actif', value: 'actif' },
                                            { label: 'Inactif', value: 'inactif' },
                                            { label: 'Suspendu', value: 'suspendu' },
                                        ]"
                                        optionLabel="label"
                                        optionValue="value"
                                        @change="confirmStatusChange(data, $event.value)"
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

        <Dialog
            v-model:visible="createDialog"
            :style="{ width: '90vw', maxWidth: '800px' }"
            header="Créer un Nouvel Employé"
            :modal="true"
            class="p-fluid"
            :contentStyle="{ backgroundColor: '#ffffff' }"
        >
            <EmployeeForm
                :employee="null"
                :positions="positions"
                :isLoading="isFormLoading"
                @submit="handleCreateSubmit"
                @cancel="closeCreateDialog"
            />
        </Dialog>

        <Dialog
            v-model:visible="editDialog"
            :style="{ width: '90vw', maxWidth: '800px' }"
            header="Modifier l'Employé"
            :modal="true"
            class="p-fluid"
            :contentStyle="{ backgroundColor: '#ffffff' }"
        >
            <EmployeeForm
                v-if="employeeToEdit"
                :employee="employeeToEdit"
                :positions="positions"
                :isLoading="isFormLoading"
                @submit="handleEditSubmit"
                @cancel="closeEditDialog"
            />
        </Dialog>

        <Dialog
            v-model:visible="deleteDialog"
            :style="{ width: '450px' }"
            header="Confirmation de Suppression"
            :modal="true"
        >
            <div class="flex align-items-center justify-content-center">
                <i
                    class="pi pi-exclamation-triangle mr-3"
                    style="font-size: 2rem; color: #f87171"
                />
                <span v-if="employeeToDelete">
                    Êtes-vous sûr de vouloir supprimer l'employé
                    <b>{{ employeeToDelete.first_name }} {{ employeeToDelete.last_name }}</b>?<br />
                    <small class="text-gray-500">Cette action est réversible.</small>
                </span>
            </div>
            <template #footer>
                <Button
                    label="Annuler"
                    icon="pi pi-times"
                    class="p-button-text"
                    @click="deleteDialog = false"
                />
                <Button
                    label="Supprimer"
                    icon="pi pi-trash"
                    class="p-button-danger"
                    @click="deleteEmployee"
                />
            </template>
        </Dialog>

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
                    <b>{{ statusChangeData.employee.first_name }} {{ statusChangeData.employee.last_name }}</b><br />
                    de
                    <Tag
                        :value="getStatusLabel(statusChangeData.employee.status)"
                        :severity="getStatusColor(statusChangeData.employee.status)"
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
    </AdminLayout>
</template>
