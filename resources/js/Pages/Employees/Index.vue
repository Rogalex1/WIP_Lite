<template>
  <div>
    <p>Page Employés - À compléter</p>
  </div>
</template>

<script setup>
import { ref, watch } from "vue";
import { Link, router, Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Button from "primevue/button";
import Column from "primevue/column";
import DataTable from "primevue/datatable";
import InputText from "primevue/inputtext";
import Dropdown from "primevue/dropdown";
import Tag from "primevue/tag";
import Dialog from "primevue/dialog";
import Toolbar from "primevue/toolbar";
import AdminLayout from "@/Layouts/AdminLayout.vue";



// Props depuis le contrôleur
const props = defineProps({
    employees: Object,
    positions: Array,
    filters: Object,
});

// État réactif pour les dialogues de confirmation
const deleteDialog = ref(false);
const employeeToDelete = ref(null);
const statusChangeDialog = ref(false);
const statusChangeData = ref({ employee: null, newStatus: "" });

// Filtres de recherche et de tri
const search = ref(props.filters.search || "");
const status = ref(props.filters.status || "");
const position_id = ref(props.filters.position_id || "");

// Pagination (synchronisée avec la pagination Laravel)
const page = ref(props.employees.current_page || 1);
const rows = ref(props.employees.per_page || 10);

// Options pour les filtres de statut
const statusOptions = [
    { label: "Tous les statuts", value: "" },
    { label: "Actif", value: "actif" },
    { label: "Inactif", value: "inactif" },
    { label: "Suspendu", value: "suspendu" },
];

// Options pour les filtres de poste
const positionOptions = [
    { label: "Tous les postes", value: "" },
    ...props.positions.map((position) => ({
        label: position.name,
        value: position.id,
    })),
];

// Appliquer les filtres avec pagination
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

// Handler pour la pagination du DataTable PrimeVue
const onPage = (event) => {
    const newPage =
        event && typeof event.page === "number" ? event.page + 1 : 1; // PrimeVue utilise un index 0
    const newRows =
        event && typeof event.rows === "number" ? event.rows : rows.value;
    applyFilters(newPage, newRows);
};

// Surveiller les changements de filtres et revenir à la première page
watch([search, status, position_id], () => {
    applyFilters(1);
});

// Obtenir la couleur du tag selon le statut
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

// Obtenir le libellé du statut en français
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

// Confirmer la suppression d'un employé
const confirmDelete = (employee) => {
    employeeToDelete.value = employee;
    deleteDialog.value = true;
};

// Supprimer un employé (les toasts sont gérés par le layout via les messages flash)
const deleteEmployee = () => {
    if (employeeToDelete.value) {
        router.delete(route("employees.destroy", employeeToDelete.value.id), {
            onFinish: () => {
                // Fermer le dialogue après la requête
                deleteDialog.value = false;
                employeeToDelete.value = null;
            },
        });
    }
};

// Confirmer le changement de statut
const confirmStatusChange = (employee, newStatus) => {
    statusChangeData.value = { employee, newStatus };
    statusChangeDialog.value = true;
};

// Exécuter le changement de statut (les toasts sont gérés par le layout)
const executeStatusChange = () => {
    const { employee, newStatus } = statusChangeData.value;

    router.patch(
        route("employees.changeStatus", employee.id),
        { status: newStatus },
        {
            onFinish: () => {
                // Fermer le dialogue après la requête
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
                    <Link :href="route('employees.create')">
                        <Button
                            label="Nouvel Employé"
                            icon="pi pi-plus"
                            class="p-button-primary"
                        />
                    </Link>
                </div>
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

        <!-- Dialog de confirmation de suppression -->
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
                    <b
                        >{{ employeeToDelete.first_name }}
                        {{ employeeToDelete.last_name }}</b
                    >
                    ?<br />
                    <small class="text-gray-500"
                        >Cette action est réversible.</small
                    >
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
    </AdminLayout>
</template>
