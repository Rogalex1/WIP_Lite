<script setup>
import { computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import Button from "primevue/button";
import InputText from "primevue/inputtext";
import Dropdown from "primevue/dropdown";
import Calendar from "primevue/calendar";
import InputNumber from "primevue/inputnumber";
import Textarea from "primevue/textarea";

const props = defineProps({
    employee: {
        type: Object,
        default: null,
    },
    positions: {
        type: Array,
        required: true,
    },
    isLoading: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["submit", "cancel"]);

const form = useForm({
    first_name: props.employee?.first_name || "",
    last_name: props.employee?.last_name || "",
    birth_date: props.employee?.birth_date || null,
    email: props.employee?.email || "",
    phone: props.employee?.phone || "",
    address: props.employee?.address || "",
    position_id: props.employee?.position_id || null,
    salary_base: props.employee?.salary_base || null,
    status: props.employee?.status || "actif",
    user_id: props.employee?.user_id || null,
});

const statusOptions = [
    { label: "Actif", value: "actif" },
    { label: "Inactif", value: "inactif" },
    { label: "Suspendu", value: "suspendu" },
];

const positionOptions = computed(() =>
    props.positions.map((position) => ({
        label: position.name,
        value: position.id,
    }))
);

const handleSubmit = () => {
    emit("submit", form);
};

const handleCancel = () => {
    emit("cancel");
};
</script>

<template>
    <form @submit.prevent="handleSubmit" class="space-y-6 bg-white p-6 rounded-lg">
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
                            'p-invalid': form.errors.position_id,
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
                            'p-invalid': form.errors.salary_base,
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

        <div class="flex justify-end gap-3 pt-6 border-t">
            <Button
                label="Annuler"
                icon="pi pi-times"
                class="p-button-secondary"
                @click="handleCancel"
                :disabled="isLoading"
            />
            <Button
                label="Enregistrer"
                icon="pi pi-save"
                class="p-button-primary"
                type="submit"
                :disabled="isLoading"
                :loading="isLoading"
            />
        </div>
    </form>
</template>
