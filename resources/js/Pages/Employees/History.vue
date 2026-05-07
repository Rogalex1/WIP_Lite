<script setup>
import { onMounted } from "vue";
import { Link, usePage, Head } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import Button from "primevue/button";
import Card from "primevue/card";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Tag from "primevue/tag";

const props = defineProps({
    employee: Object,
    history: Array,
});

const toast = useToast();

onMounted(() => {
    const page = usePage();

    if (page.props.flash?.success) {
        toast.add({ severity: "success", summary: "Succès", detail: page.props.flash.success, life: 3000 });
    }
    if (page.props.flash?.error) {
        toast.add({ severity: "error", summary: "Erreur", detail: page.props.flash.error, life: 3000 });
    }
    if (page.props.flash?.info) {
        toast.add({ severity: "info", summary: "Info", detail: page.props.flash.info, life: 3000 });
    }
});

const formatDate = (date) => {
    if (!date) return "-";
    try {
        return new Date(date).toLocaleDateString("fr-FR", { day: "2-digit", month: "long", year: "numeric" });
    } catch (e) {
        return date;
    }
};

const getManagerName = (m) => {
    if (!m) return "-";
    return `${m.first_name || ''} ${m.last_name || ''}`.trim();
};

const getStatusLabel = (s) => {
    if (!s) return "-";
    switch (s) {
        case "active":
        case "actif":
            return "Actif";
        case "inactive":
        case "inactif":
            return "Inactif";
        case "suspended":
        case "suspendu":
            return "Suspendu";
        default:
            return s;
    }
};
</script>

<template>
    <Head :title="`Historique - ${props.employee.first_name} ${props.employee.last_name}`" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Historique d'affectations — {{ props.employee.first_name }} {{ props.employee.last_name }}
                </h2>
                <div class="flex gap-2">
                    <Link :href="route('employees.show', props.employee.id)">
                        <Button label="Retour" icon="pi pi-arrow-left" class="p-button-secondary" />
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Informations Employé</h3>
                            <p class="text-sm text-gray-500">Matricule: <span class="font-mono">{{ props.employee.matricule }}</span></p>
                            <p class="text-sm text-gray-500">Poste: {{ props.employee.position?.name || '-' }}</p>
                        </div>
                        <div class="text-right">
                            <Tag :value="props.employee.status ? getStatusLabel(props.employee.status) : '-'" :severity="props.employee.status === 'actif' ? 'success' : (props.employee.status === 'inactif' ? 'secondary' : 'danger')" />
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Affectations récentes</h3>

                    <DataTable
                        :value="props.history"
                        :paginator="true"
                        :rows="10"
                        class="p-datatable-sm"
                        stripedRows
                        responsiveLayout="scroll"
                    >
                        <Column header="Projet">
                            <template #body="{ data }">
                                {{ data.project?.name || '-' }}
                            </template>
                        </Column>

                        <Column header="Manager">
                            <template #body="{ data }">
                                {{ getManagerName(data.manager) }}
                            </template>
                        </Column>

                        <Column header="Période">
                            <template #body="{ data }">
                                {{ formatDate(data.start_date) }} - {{ data.end_date ? formatDate(data.end_date) : 'En cours' }}
                            </template>
                        </Column>

                        <Column header="Statut">
                            <template #body="{ data }">
                                <Tag :value="getStatusLabel(data.status)" :severity="data.status === 'active' || data.status === 'actif' ? 'success' : 'secondary'" />
                            </template>
                        </Column>

                        <Column header="Commentaires">
                            <template #body="{ data }">
                                {{ data.note || data.comment || '-' }}
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
