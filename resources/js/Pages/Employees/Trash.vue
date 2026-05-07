<script setup>
import { ref, watch } from "vue";
import { Link, router, Head } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import Button from "primevue/button";
import Column from "primevue/column";
import DataTable from "primevue/datatable";
import InputText from "primevue/inputtext";
import Dropdown from "primevue/dropdown";
import Tag from "primevue/tag";
import Dialog from "primevue/dialog";
import Toolbar from "primevue/toolbar";

// Props depuis le contrôleur
const props = defineProps({
    employees: Object,
    positions: Array,
    filters: Object,
});

// État réactif pour les dialogues de confirmation
const restoreDialog = ref(false);
const forceDeleteDialog = ref(false);
const employeeToRestore = ref(null);
const employeeToDelete = ref(null);

// Filtres de recherche et de tri
const search = ref(props.filters.search || "");
const position_id = ref(props.filters.position_id || "");

// Pagination (synchronisée avec la pagination Laravel)
const page = ref(props.employees.current_page || 1);
const rows = ref(props.employees.per_page || 10);

// Options pour les filtres de poste
const positionOptions = [
    { label: "Tous les postes", value: "" },
    ...props.positions.map((position) => ({
        label: position.name,
        value: position.id,
    })),
];

/**
 * Appliquer les filtres avec pagination
 */
const applyFilters = (p = 1, perPage = rows.value) => {
    page.value = p;
    rows.value = perPage;

    router.get(
        route("employees.trash"),
        {
            search: search.value,
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

/**
 * Handler pour la pagination du DataTable PrimeVue
 */
const onPage = (event) => {
    const newPage =
        event && typeof event.page === "number" ? event.page + 1 : 1;
    const newRows =
        event && typeof event.rows === "number" ? event.rows : rows.value;
    applyFilters(newPage, newRows);
};

/**
 * Surveiller les changements de filtres et revenir à la première page
 */
watch([search, position_id], () => {
    applyFilters(1);
});

/**
 * Obtenir le libellé du statut en français
 */
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

/**
 * Obtenir la couleur du tag selon le statut
 */
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

/**
 * Formater la date en français
 */
const formatDate = (date) => {
    if (!date) return "-";
    return new Date(date).toLocaleDateString("fr-FR", {
        day: "numeric",
        month: "long",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

/**
 * Confirmer la restauration d'un employé
 */
const confirmRestore = (employee) => {
    employeeToRestore.value = employee;
    restoreDialog.value = true;
};

/**
 * Restaurer un employé supprimé
 * Les toasts sont gérés automatiquement par le layout via les messages flash
 */
const restoreEmployee = () => {
    if (employeeToRestore.value) {
        router.patch(
            route("employees.restore", employeeToRestore.value.id),
            {},
            {
                onFinish: () => {
                    restoreDialog.value = false;
                    employeeToRestore.value = null;
                },
            }
        );
    }
};

/**
 * Confirmer la suppression définitive d'un employé
 */
const confirmForceDelete = (employee) => {
    employeeToDelete.value = employee;
    forceDeleteDialog.value = true;
};

/**
 * Supprimer définitivement un employé
 * Les toasts sont gérés automatiquement par le layout via les messages flash
 */
const forceDeleteEmployee = () => {
    if (employeeToDelete.value) {
        router.delete(
            route("employees.forceDelete", employeeToDelete.value.id),
            {
                onFinish: () => {
                    forceDeleteDialog.value = false;
                    employeeToDelete.value = null;
                },
            }
        );
    }
};
</script>

<template>
    <Head title="Employés Supprimés" />

    <AdminLayout>

            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Historique des Employés Supprimés
                </h2>
                <Link :href="route('employees.index')">
                    <Button
                        label="Retour à la liste"
                        icon="pi pi-arrow-left"
                        class="p-button-secondary"
                    />
                </Link>
            </div>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <!-- Filtres -->
                    <Toolbar class="mb-4">
                        <template #start>
                            <div class="flex gap-2 items-center">
                                <!-- Champ de recherche -->
                                <span class="p-input-icon-left">
                                    <i class="pi pi-search" />
                                    <InputText
                                        v-model="search"
                                        placeholder="Rechercher..."
                                        class="w-64"
                                    />
                                </span>

                                <!-- Filtre par poste -->
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

                    <!-- Tableau des employés supprimés -->
                    <DataTable
                        :value="employees.data"
                        :paginator="true"
                        :rows="rows"
                        :first="(page - 1) * rows"
                        :totalRecords="employees.total"
                        :lazy="true"
                        @page="onPage"
                        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                        currentPageReportTemplate="Affichage de {first} à {last} sur {totalRecords} employés supprimés"
                        class="p-datatable-sm"
                        stripedRows
                        responsiveLayout="scroll"
                    >
                        <!-- Colonne : Matricule -->
                        <Column field="matricule" header="Matricule" sortable>
                            <template #body="{ data }">
                                <span class="font-mono text-sm">{{
                                    data.matricule
                                }}</span>
                            </template>
                        </Column>

                        <!-- Colonne : Nom complet -->
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

                        <!-- Colonne : Poste -->
                        <Column field="position.name" header="Poste" sortable>
                            <template #body="{ data }">
                                {{ data.position?.name || "-" }}
                            </template>
                        </Column>

                        <!-- Colonne : Statut -->
                        <Column field="status" header="Statut" sortable>
                            <template #body="{ data }">
                                <Tag
                                    :value="getStatusLabel(data.status)"
                                    :severity="getStatusColor(data.status)"
                                />
                            </template>
                        </Column>

                        <!-- Colonne : Date de suppression -->
                        <Column
                            field="deleted_at"
                            header="Date de suppression"
                            sortable
                        >
                            <template #body="{ data }">
                                <span class="text-sm">
                                    {{ formatDate(data.deleted_at) }}
                                </span>
                            </template>
                        </Column>

                        <!-- Colonne : Actions -->
                        <Column
                            header="Actions"
                            :exportable="false"
                            style="min-width: 12rem"
                        >
                            <template #body="{ data }">
                                <div class="flex gap-2 items-center">
                                    <!-- Bouton Restaurer -->
                                    <Button
                                        label="Restaurer"
                                        icon="pi pi-refresh"
                                        class="p-button-outlined p-button-success"
                                        @click="confirmRestore(data)"
                                    />

                                    <!-- Bouton Supprimer définitivement -->
                                    <Button
                                        label="Supprimer"
                                        icon="pi pi-trash"
                                        class="p-button-danger"
                                        @click="confirmForceDelete(data)"
                                    />
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>

        <!-- Dialog de confirmation de restauration -->
        <Dialog
            v-model:visible="restoreDialog"
            :style="{ width: '450px' }"
            header="Confirmation de Restauration"
            :modal="true"
        >
            <div class="flex align-items-center justify-content-center">
                <i
                    class="pi pi-refresh mr-3"
                    style="font-size: 2rem; color: #10b981"
                />
                <span v-if="employeeToRestore">
                    Êtes-vous sûr de vouloir restaurer l'employé
                    <b
                        >{{ employeeToRestore.first_name }}
                        {{ employeeToRestore.last_name }}</b
                    >
                    ?<br />
                    <small class="text-gray-500"
                        >Il réapparaîtra dans la liste principale des
                        employés.</small
                    >
                </span>
            </div>
            <template #footer>
                <Button
                    label="Annuler"
                    icon="pi pi-times"
                    class="p-button-text"
                    @click="restoreDialog = false"
                />
                <Button
                    label="Restaurer"
                    icon="pi pi-refresh"
                    class="p-button-success"
                    @click="restoreEmployee"
                />
            </template>
        </Dialog>

        <!-- Dialog de confirmation de suppression définitive -->
        <Dialog
            v-model:visible="forceDeleteDialog"
            :style="{ width: '450px' }"
            header="Confirmation de Suppression Définitive"
            :modal="true"
        >
            <div class="flex align-items-center justify-content-center">
                <i
                    class="pi pi-exclamation-triangle mr-3"
                    style="font-size: 2rem; color: #ef4444"
                />
                <span v-if="employeeToDelete">
                    Êtes-vous sûr de vouloir supprimer définitivement l'employé
                    <b
                        >{{ employeeToDelete.first_name }}
                        {{ employeeToDelete.last_name }}</b
                    >
                    ?<br />
                    <small class="text-red-600"
                        ><strong>Attention :</strong> Cette action est
                        irréversible et supprimera toutes les données
                        associées.</small
                    >
                </span>
            </div>
            <template #footer>
                <Button
                    label="Annuler"
                    icon="pi pi-times"
                    class="p-button-text"
                    @click="forceDeleteDialog = false"
                />
                <Button
                    label="Supprimer définitivement"
                    icon="pi pi-trash"
                    class="p-button-danger"
                    @click="forceDeleteEmployee"
                />
            </template>
        </Dialog>
    </AdminLayout>
</template>
