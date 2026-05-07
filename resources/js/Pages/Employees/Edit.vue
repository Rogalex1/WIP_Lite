<script setup>
import { ref } from "vue";
import { Link, useForm, Head, router } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import Button from "primevue/button";
import InputText from "primevue/inputtext";
import Dropdown from "primevue/dropdown";
import Calendar from "primevue/calendar";
import InputNumber from "primevue/inputnumber";
import Textarea from "primevue/textarea";
import Tag from "primevue/tag";
import Dialog from "primevue/dialog";

// Props depuis le contrôleur
const props = defineProps({
    employee: Object,
    positions: Array,
});

// État réactif
const toast = useToast();
const submitDialog = ref(false);
const cancelDialog = ref(false);

// Formulaire avec Inertia (pré-rempli avec les données de l'employé)
const form = useForm({
    first_name: props.employee.first_name,
    last_name: props.employee.last_name,
    birth_date: props.employee.birth_date,
    email: props.employee.email,
    phone: props.employee.phone,
    address: props.employee.address,
    position_id: props.employee.position_id,
    salary_base: props.employee.salary_base,
    status: props.employee.status,
    user_id: props.employee.user_id,
});

// Options pour les statuts
const statusOptions = [
    { label: "Actif", value: "actif" },
    { label: "Inactif", value: "inactif" },
    { label: "Suspendu", value: "suspendu" },
];

// Préparer les options pour le dropdown des postes
const positionOptions = props.positions.map((position) => ({
    label: position.name,
    value: position.id,
}));

// Confirmer la soumission
const confirmSubmit = () => {
    submitDialog.value = true;
};

// Soumettre le formulaire
// Les toasts de succès sont gérés automatiquement par le layout via les messages flash
const submit = () => {
    submitDialog.value = false;
    form.put(route("employees.update", props.employee.id), {
        onError: (errors) => {
            // Afficher un toast d'erreur pour les erreurs de validation
            toast.add({
                severity: "error",
                summary: "Erreur de validation",
                detail: "Veuillez corriger les erreurs dans le formulaire",
                life: 4000,
            });
        },
    });
};

// Confirmer l'annulation
const confirmCancel = () => {
    if (form.isDirty) {
        cancelDialog.value = true;
    } else {
        cancel();
    }
};

// Annuler et retourner à la liste
const cancel = () => {
    router.get(route("employees.index"));
};

// Obtenir la couleur du statut pour les tags PrimeVue
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
</script>

<template>
    <Head title="Modifier un Employé" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Modifier l'Employé : {{ employee.first_name }}
                    {{ employee.last_name }}
                </h2>
                <div class="flex gap-2">
                    <Link :href="route('employees.show', employee.id)">
                        <Button
                            label="Voir les détails"
                            icon="pi pi-eye"
                            class="p-button-info"
                        />
                    </Link>
                    <Link :href="route('employees.index')">
                        <Button
                            label="Retour à la liste"
                            icon="pi pi-arrow-left"
                            class="p-button-secondary"
                        />
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Informations de l'employé -->
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6"
                >
                    <div class="px-6 py-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="text-lg font-medium text-gray-900">
                                    {{ employee.first_name }}
                                    {{ employee.last_name }}
                                </h3>
                                <p class="text-sm text-gray-500">
                                    Matricule: {{ employee.matricule }}
                                </p>
                            </div>
                            <Tag
                                :value="getStatusLabel(employee.status)"
                                :severity="getStatusColor(employee.status)"
                                class="text-sm"
                            />
                        </div>
                    </div>
                </div>

                <!-- Formulaire de modification -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="space-y-6 p-6">
                        <!-- Informations personnelles -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">
                                Informations Personnelles
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label
                                        for="first_name"
                                        class="block text-sm font-medium text-gray-700 mb-2"
                                    >
                                        Prénom
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <InputText
                                        id="first_name"
                                        v-model="form.first_name"
                                        :class="{
                                            'p-invalid': form.errors.first_name,
                                        }"
                                        class="w-full"
                                        placeholder="Entrez le prénom"
                                    />
                                    <small
                                        v-if="form.errors.first_name"
                                        class="p-error"
                                    >
                                        {{ form.errors.first_name }}
                                    </small>
                                </div>

                                <div>
                                    <label
                                        for="last_name"
                                        class="block text-sm font-medium text-gray-700 mb-2"
                                    >
                                        Nom <span class="text-red-500">*</span>
                                    </label>
                                    <InputText
                                        id="last_name"
                                        v-model="form.last_name"
                                        :class="{
                                            'p-invalid': form.errors.last_name,
                                        }"
                                        class="w-full"
                                        placeholder="Entrez le nom"
                                    />
                                    <small
                                        v-if="form.errors.last_name"
                                        class="p-error"
                                    >
                                        {{ form.errors.last_name }}
                                    </small>
                                </div>

                                <div>
                                    <label
                                        for="birth_date"
                                        class="block text-sm font-medium text-gray-700 mb-2"
                                    >
                                        Date de naissance
                                    </label>
                                    <Calendar
                                        id="birth_date"
                                        v-model="form.birth_date"
                                        :class="{
                                            'p-invalid': form.errors.birth_date,
                                        }"
                                        dateFormat="yy-mm-dd"
                                        :maxDate="new Date()"
                                        class="w-full"
                                        placeholder="JJ/MM/AAAA"
                                    />
                                    <small
                                        v-if="form.errors.birth_date"
                                        class="p-error"
                                    >
                                        {{ form.errors.birth_date }}
                                    </small>
                                </div>

                                <div>
                                    <label
                                        for="email"
                                        class="block text-sm font-medium text-gray-700 mb-2"
                                    >
                                        Email
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <InputText
                                        id="email"
                                        v-model="form.email"
                                        type="email"
                                        :class="{
                                            'p-invalid': form.errors.email,
                                        }"
                                        class="w-full"
                                        placeholder="email@exemple.com"
                                    />
                                    <small
                                        v-if="form.errors.email"
                                        class="p-error"
                                    >
                                        {{ form.errors.email }}
                                    </small>
                                </div>

                                <div>
                                    <label
                                        for="phone"
                                        class="block text-sm font-medium text-gray-700 mb-2"
                                    >
                                        Téléphone
                                    </label>
                                    <InputText
                                        id="phone"
                                        v-model="form.phone"
                                        :class="{
                                            'p-invalid': form.errors.phone,
                                        }"
                                        class="w-full"
                                        placeholder="+225 00 00 00 00"
                                    />
                                    <small
                                        v-if="form.errors.phone"
                                        class="p-error"
                                    >
                                        {{ form.errors.phone }}
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Adresse -->
                        <div class="space-y-4">
                            <label
                                for="address"
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                Adresse
                            </label>
                            <Textarea
                                id="address"
                                v-model="form.address"
                                :class="{ 'p-invalid': form.errors.address }"
                                rows="3"
                                class="w-full"
                                placeholder="Entrez l'adresse complète"
                            />
                            <small v-if="form.errors.address" class="p-error">
                                {{ form.errors.address }}
                            </small>
                        </div>

                        <!-- Informations professionnelles -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">
                                Informations Professionnelles
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label
                                        for="position_id"
                                        class="block text-sm font-medium text-gray-700 mb-2"
                                    >
                                        Poste
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <Dropdown
                                        id="position_id"
                                        v-model="form.position_id"
                                        :options="positionOptions"
                                        optionLabel="label"
                                        optionValue="value"
                                        :class="{
                                            'p-invalid':
                                                form.errors.position_id,
                                        }"
                                        class="w-full"
                                        placeholder="Sélectionnez un poste"
                                    />
                                    <small
                                        v-if="form.errors.position_id"
                                        class="p-error"
                                    >
                                        {{ form.errors.position_id }}
                                    </small>
                                </div>

                                <div>
                                    <label
                                        for="salary_base"
                                        class="block text-sm font-medium text-gray-700 mb-2"
                                    >
                                        Salaire de base
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <InputNumber
                                        id="salary_base"
                                        v-model="form.salary_base"
                                        :class="{
                                            'p-invalid':
                                                form.errors.salary_base,
                                        }"
                                        mode="currency"
                                        currency="XOF"
                                        locale="fr-FR"
                                        class="w-full"
                                        placeholder="0"
                                    />
                                    <small
                                        v-if="form.errors.salary_base"
                                        class="p-error"
                                    >
                                        {{ form.errors.salary_base }}
                                    </small>
                                </div>

                                <div>
                                    <label
                                        for="status"
                                        class="block text-sm font-medium text-gray-700 mb-2"
                                    >
                                        Statut
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <Dropdown
                                        id="status"
                                        v-model="form.status"
                                        :options="statusOptions"
                                        optionLabel="label"
                                        optionValue="value"
                                        :class="{
                                            'p-invalid': form.errors.status,
                                        }"
                                        class="w-full"
                                        placeholder="Sélectionnez un statut"
                                    />
                                    <small
                                        v-if="form.errors.status"
                                        class="p-error"
                                    >
                                        {{ form.errors.status }}
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="flex justify-end gap-3 pt-6 border-t">
                            <Button
                                label="Annuler"
                                icon="pi pi-times"
                                class="p-button-secondary"
                                @click="confirmCancel"
                                :disabled="form.processing"
                            />
                            <Button
                                label="Mettre à jour"
                                icon="pi pi-save"
                                class="p-button-primary"
                                @click="confirmSubmit"
                                :disabled="form.processing"
                            />
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Dialog de confirmation de mise à jour -->
        <Dialog
            v-model:visible="submitDialog"
            :style="{ width: '450px' }"
            header="Confirmation de Mise à Jour"
            :modal="true"
        >
            <div class="flex align-items-center justify-content-center">
                <i
                    class="pi pi-user-edit mr-3"
                    style="font-size: 2rem; color: #3b82f6"
                />
                <span>
                    Êtes-vous sûr de vouloir mettre à jour les informations de
                    <b
                        >{{ props.employee.first_name }}
                        {{ props.employee.last_name }}</b
                    >
                    ?<br />
                    <small class="text-gray-500"
                        >Cette action modifiera les données de l'employé.</small
                    >
                </span>
            </div>
            <template #footer>
                <Button
                    label="Annuler"
                    icon="pi pi-times"
                    class="p-button-text"
                    @click="submitDialog = false"
                />
                <Button
                    label="Confirmer"
                    icon="pi pi-check"
                    class="p-button-primary"
                    @click="submit"
                />
            </template>
        </Dialog>

        <!-- Dialog de confirmation d'annulation -->
        <Dialog
            v-model:visible="cancelDialog"
            :style="{ width: '450px' }"
            header="Confirmation d'Annulation"
            :modal="true"
        >
            <div class="flex align-items-center justify-content-center">
                <i
                    class="pi pi-exclamation-triangle mr-3"
                    style="font-size: 2rem; color: #f59e0b"
                />
                <span>
                    Vous avez des modifications non enregistrées.<br />
                    Êtes-vous sûr de vouloir quitter cette page ?
                </span>
            </div>
            <template #footer>
                <Button
                    label="Continuer"
                    icon="pi pi-pencil"
                    class="p-button-primary"
                    @click="cancelDialog = false"
                />
                <Button
                    label="Quitter"
                    icon="pi pi-times"
                    class="p-button-danger"
                    @click="cancel"
                />
            </template>
        </Dialog>
    </AdminLayout>
</template>
