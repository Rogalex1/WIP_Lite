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
import Dialog from "primevue/dialog";

// Props depuis le contrôleur
const props = defineProps({
    positions: Array,
});

// État réactif
const toast = useToast();
const submitDialog = ref(false);
const cancelDialog = ref(false);

// Formulaire avec Inertia
const form = useForm({
    first_name: "",
    last_name: "",
    birth_date: null,
    email: "",
    phone: "",
    address: "",
    position_id: null,
    salary_base: null,
    status: "actif",
    user_id: null,
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
    form.post(route("employees.store"), {
        onError: (errors) => {
            // Afficher un toast d'erreur pour les erreurs de validation côté client
            // (ces erreurs ne passent pas par un redirect, donc gérées ici directement)
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
</script>

<template>
    <Head title="Créer un Employé" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Créer un Nouvel Employé
                </h2>
                <Link :href="route('employees.index')">
                    <Button
                        label="Retour à la liste"
                        icon="pi pi-arrow-left"
                        class="p-button-secondary"
                    />
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="space-y-6">
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
                                label="Créer l'employé"
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

        <!-- Dialog de confirmation de création -->
        <Dialog
            v-model:visible="submitDialog"
            :style="{ width: '450px' }"
            header="Confirmation de Création"
            :modal="true"
        >
            <div class="flex align-items-center justify-content-center">
                <i
                    class="pi pi-user-plus mr-3"
                    style="font-size: 2rem; color: #10b981"
                />
                <span>
                    Êtes-vous sûr de vouloir créer ce nouvel employé ?<br />
                    <small class="text-gray-500">
                        {{ form.first_name || "Nouveau" }}
                        {{ form.last_name || "Employé" }}
                    </small>
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
