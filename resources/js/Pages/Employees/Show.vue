<script setup>
import { onMounted } from "vue";
import { Link, usePage, Head, router } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Button from "primevue/button";
import Card from "primevue/card";
import Tag from "primevue/tag";
import Divider from "primevue/divider";
import Badge from "primevue/badge";

// Props depuis le contrôleur
const props = defineProps({
    employee: Object,
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

// Formater la date en français
const formatDate = (date) => {
    if (!date) return "-";
    return new Date(date).toLocaleDateString("fr-FR", {
        day: "numeric",
        month: "long",
        year: "numeric",
    });
};

// Formater le montant en devise (Franc CFA)
const formatCurrency = (amount) => {
    return new Intl.NumberFormat("fr-FR", {
        style: "currency",
        currency: "XOF",
    }).format(amount);
};

// Changer le statut de l'employé
// Les toasts sont gérés automatiquement par le layout via les messages flash
const changeStatus = (newStatus) => {
    router.patch(
        route("employees.changeStatus", props.employee.id),
        { status: newStatus },
    );
};

// Confirmer et supprimer l'employé
// Les toasts sont gérés automatiquement par le layout via les messages flash
const confirmAndDelete = () => {
    if (!confirm("Êtes-vous sûr de vouloir supprimer cet employé ?")) return;

    router.delete(route("employees.destroy", props.employee.id), {
        onSuccess: () => {
            // Rediriger vers la liste après suppression
            router.get(route("employees.index"));
        },
    });
};
</script>

<template>
    <Head title="Détails de l'Employé" />

    <AdminLayout>

            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Détails de l'Employé : {{ employee.first_name }}
                    {{ employee.last_name }}
                </h2>
                <div class="flex gap-2">
                    <Link :href="route('employees.edit', employee.id)">
                        <Button
                            label="Modifier"
                            icon="pi pi-pencil"
                            class="p-button-warning"
                        />
                    </Link>

                    <Button
                        label="Supprimer"
                        icon="pi pi-trash"
                        class="p-button-danger"
                        @click="confirmAndDelete"
                    />

                    <Link :href="route('employees.index')">
                        <Button
                            label="Retour à la liste"
                            icon="pi pi-arrow-left"
                            class="p-button-secondary"
                        />
                    </Link>
                </div>
            </div>

        <div class="py-6">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <!-- Carte principale avec informations de base -->
                <Card class="mb-6">
                    <template #content>
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-6">
                                <!-- Avatar ou icône -->
                                <div
                                    class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center"
                                >
                                    <i
                                        class="pi pi-user text-3xl text-blue-600"
                                    ></i>
                                </div>

                                <!-- Informations principales -->
                                <div class="flex-1">
                                    <h1
                                        class="text-2xl font-bold text-gray-800 mb-2"
                                    >
                                        {{ employee.first_name }}
                                        {{ employee.last_name }}
                                    </h1>
                                    <div class="space-y-1">
                                        <p class="text-sm text-gray-600">
                                            <span class="font-medium"
                                                >Matricule:</span
                                            >
                                            <span
                                                class="font-mono bg-gray-100 px-2 py-1 rounded"
                                                >{{ employee.matricule }}</span
                                            >
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            <span class="font-medium"
                                                >Email:</span
                                            >
                                            {{ employee.email }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            <span class="font-medium"
                                                >Téléphone:</span
                                            >
                                            {{ employee.phone || "-" }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Statut -->
                            <div class="text-right">
                                <Tag
                                    :value="getStatusLabel(employee.status)"
                                    :severity="getStatusColor(employee.status)"
                                    class="text-lg px-4 py-2"
                                />
                            </div>
                        </div>
                    </template>
                </Card>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Informations personnelles -->
                    <Card>
                        <template #title>
                            <i class="pi pi-user mr-2"></i>
                            Informations Personnelles
                        </template>
                        <template #content>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-gray-500">
                                        Date de naissance
                                    </p>
                                    <p class="font-medium">
                                        {{ formatDate(employee.birth_date) }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Adresse</p>
                                    <p class="font-medium">
                                        {{
                                            employee.address || "Non renseignée"
                                        }}
                                    </p>
                                </div>
                            </div>
                        </template>
                    </Card>

                    <!-- Informations professionnelles -->
                    <Card>
                        <template #title>
                            <i class="pi pi-briefcase mr-2"></i>
                            Informations Professionnelles
                        </template>
                        <template #content>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-gray-500">Poste</p>
                                    <p class="font-medium">
                                        {{ employee.position?.name || "-" }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">
                                        Salaire de base
                                    </p>
                                    <p
                                        class="font-medium text-lg text-green-600"
                                    >
                                        {{
                                            formatCurrency(employee.salary_base)
                                        }}
                                    </p>
                                </div>
                            </div>
                        </template>
                    </Card>

                    <!-- Actions rapides -->
                    <Card>
                        <template #title>
                            <i class="pi pi-cog mr-2"></i>
                            Actions Rapides
                        </template>
                        <template #content>
                            <div class="space-y-3">
                                <Link
                                    :href="route('employees.edit', employee.id)"
                                    class="block"
                                >
                                    <Button
                                        label="Modifier les informations"
                                        icon="pi pi-pencil"
                                        class="w-full p-button-outlined"
                                    />
                                </Link>

                                <Link
                                    :href="
                                        route('employees.history', employee.id)
                                    "
                                    class="block"
                                >
                                    <Button
                                        label="Voir l'historique"
                                        icon="pi pi-history"
                                        class="w-full p-button-outlined p-button-info"
                                    />
                                </Link>

                                <!-- Actions de statut -->
                                <div class="pt-3 border-t">
                                    <p class="text-sm text-gray-500 mb-2">
                                        Changer le statut:
                                    </p>
                                    <div class="grid grid-cols-3 gap-2">
                                        <Button
                                            label="Actif"
                                            severity="success"
                                            size="small"
                                            outlined
                                            @click="changeStatus('actif')"
                                        />
                                        <Button
                                            label="Inactif"
                                            severity="secondary"
                                            size="small"
                                            outlined
                                            @click="changeStatus('inactif')"
                                        />
                                        <Button
                                            label="Suspendu"
                                            severity="danger"
                                            size="small"
                                            outlined
                                            @click="changeStatus('suspendu')"
                                        />
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Card>
                </div>

                <!-- Affectations récentes si disponibles -->
                <Card
                    v-if="
                        employee.assignments && employee.assignments.length > 0
                    "
                    class="mt-6"
                >
                    <template #title>
                        <i class="pi pi-calendar mr-2"></i>
                        Affectations Récentes
                    </template>
                    <template #content>
                        <div class="space-y-3">
                            <div
                                v-for="assignment in employee.assignments.slice(
                                    0,
                                    5,
                                )"
                                :key="assignment.id"
                                class="flex items-center justify-between p-3 bg-gray-50 rounded"
                            >
                                <div class="flex-1">
                                    <p class="font-medium">
                                        {{
                                            assignment.project?.name ||
                                            "Projet non spécifié"
                                        }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ formatDate(assignment.start_date) }}
                                        -
                                        {{
                                            formatDate(assignment.end_date) ||
                                            "En cours"
                                        }}
                                    </p>
                                </div>
                                <Badge
                                    :value="assignment.status"
                                    :severity="
                                        assignment.status === 'active'
                                            ? 'success'
                                            : 'secondary'
                                    "
                                />
                            </div>
                        </div>

                        <div
                            v-if="employee.assignments.length > 5"
                            class="mt-4 text-center"
                        >
                            <Link
                                :href="route('employees.history', employee.id)"
                            >
                                <Button
                                    label="Voir toutes les affectations"
                                    icon="pi pi-arrow-right"
                                    class="p-button-text p-button-info"
                                />
                            </Link>
                        </div>
                    </template>
                </Card>
            </div>
        </div>
    </AdminLayout>
</template>
