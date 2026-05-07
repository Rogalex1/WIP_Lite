<script setup>
import { Link } from "@inertiajs/vue3";
import Button from "primevue/button";
import Column from "primevue/column";
import DataTable from "primevue/datatable";
import Tag from "primevue/tag";
import Dropdown from "primevue/dropdown";

// Props
const props = defineProps({
    employees: {
        type: Array,
        required: true,
    },
    loading: {
        type: Boolean,
        default: false,
    },
    pagination: {
        type: Object,
        default: () => ({}),
    },
    showActions: {
        type: Boolean,
        default: true,
    },
    showStatusChange: {
        type: Boolean,
        default: true,
    },
});

// Émissions
const emit = defineEmits(["page", "sort", "filter", "delete", "statusChange"]);

// Options pour les statuts
const statusOptions = [
    { label: "Actif", value: "actif" },
    { label: "Inactif", value: "inactif" },
    { label: "Suspendu", value: "suspendu" },
];

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

// Gérer la pagination
const onPage = (event) => {
    emit("page", event);
};

// Gérer le tri
const onSort = (event) => {
    emit("sort", event);
};

// Supprimer un employé
const confirmDelete = (employee) => {
    emit("delete", employee);
};

// Changer le statut
const onStatusChange = (employee, newStatus) => {
    emit("statusChange", { employee, status: newStatus });
};
</script>

<template>
    <DataTable
        :value="employees"
        :loading="loading"
        :paginator="pagination.total > 0"
        :rows="pagination.perPage || 10"
        :totalRecords="pagination.total"
        :lazy="true"
        @page="onPage"
        @sort="onSort"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        currentPageReportTemplate="Affichage de {first} à {last} sur {totalRecords} employés"
        class="p-datatable-sm"
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
                <div class="flex items-center">
                    <div>
                        <div class="font-medium">
                            {{ data.first_name }} {{ data.last_name }}
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
            v-if="showActions"
            header="Actions"
            :exportable="false"
            style="min-width: 8rem"
        >
            <template #body="{ data }">
                <div class="flex gap-1">
                    <Link :href="route('employees.show', data.id)">
                        <Button
                            label="Voir"
                            icon="pi pi-eye"
                            class="p-button-outlined p-button-info"
                        />
                    </Link>

                    <Link :href="route('employees.edit', data.id)">
                        <Button
                            label="Modifier"
                            icon="pi pi-pencil"
                            class="p-button-outlined p-button-warning"
                        />
                    </Link>

                    <Dropdown
                        v-if="showStatusChange"
                        :modelValue="data.status"
                        :options="statusOptions"
                        optionLabel="label"
                        optionValue="value"
                        @change="onStatusChange(data, $event.value)"
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

        <!-- Slot pour l'expansion si nécessaire -->
        <slot name="expansion" />
    </DataTable>
</template>

<style scoped>
/* Styles personnalisés pour le tableau */
.p-datatable .p-datatable-sm {
    font-size: 0.875rem;
}

.p-datatable .p-datatable-thead > tr > th {
    font-weight: 600;
    padding: 0.75rem;
}

.p-datatable .p-datatable-tbody > tr > td {
    padding: 0.75rem;
}

/* Animations pour les actions */
.p-button-rounded {
    transition: all 0.2s ease;
}

.p-button-rounded:hover {
    transform: scale(1.05);
}

/* Styles pour les tags de statut */
.p-tag {
    font-weight: 500;
}

/* Styles pour les tooltips */
.p-tooltip {
    font-size: 0.75rem;
}
</style>
