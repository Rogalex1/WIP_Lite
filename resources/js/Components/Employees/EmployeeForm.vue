<script setup>
import { computed } from "vue";
import InputText from "primevue/inputtext";
import Select from "primevue/dropdown";
import Calendar from "primevue/calendar";
import InputNumber from "primevue/inputnumber";
import Textarea from "primevue/textarea";

// Props et émissions
const props = defineProps({
    modelValue: {
        type: Object,
        required: true,
    },
    positions: {
        type: Array,
        required: true,
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["update:modelValue"]);

// Options pour les statuts
const statusOptions = [
    { label: "Actif", value: "actif" },
    { label: "Inactif", value: "inactif" },
    { label: "Suspendu", value: "suspendu" },
];

// Préparer les options pour le dropdown des postes
const positionOptions = computed(() => {
    return props.positions.map((position) => ({
        label: position.name,
        value: position.id,
    }));
});

// Mettre à jour le modèle
const updateModel = (field, value) => {
    emit("update:modelValue", {
        ...props.modelValue,
        [field]: value,
    });
};

// Vérifier si un champ a une erreur
const hasError = (field) => {
    return props.errors && props.errors[field];
};

// Obtenir le message d'erreur pour un champ
const getError = (field) => {
    return props.errors ? props.errors[field] : "";
};
</script>

<template>
    <div class="space-y-6">
        <!-- Informations personnelles -->
        <div>
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                Informations Personnelles
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label
                        for="first_name"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Prénom <span class="text-red-500">*</span>
                    </label>
                    <InputText
                        id="first_name"
                        :modelValue="modelValue.first_name"
                        @update:modelValue="
                            (value) => updateModel('first_name', value)
                        "
                        :class="{ 'p-invalid': hasError('first_name') }"
                        :disabled="disabled"
                        class="w-full"
                        placeholder="Entrez le prénom"
                    />
                    <small v-if="hasError('first_name')" class="p-error">
                        {{ getError("first_name") }}
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
                        :modelValue="modelValue.last_name"
                        @update:modelValue="
                            (value) => updateModel('last_name', value)
                        "
                        :class="{ 'p-invalid': hasError('last_name') }"
                        :disabled="disabled"
                        class="w-full"
                        placeholder="Entrez le nom"
                    />
                    <small v-if="hasError('last_name')" class="p-error">
                        {{ getError("last_name") }}
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
                        :modelValue="modelValue.birth_date"
                        @update:modelValue="
                            (value) => updateModel('birth_date', value)
                        "
                        :class="{ 'p-invalid': hasError('birth_date') }"
                        :disabled="disabled"
                        dateFormat="yy-mm-dd"
                        :maxDate="new Date()"
                        class="w-full"
                        placeholder="JJ/MM/AAAA"
                    />
                    <small v-if="hasError('birth_date')" class="p-error">
                        {{ getError("birth_date") }}
                    </small>
                </div>

                <div>
                    <label
                        for="email"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Email <span class="text-red-500">*</span>
                    </label>
                    <InputText
                        id="email"
                        :modelValue="modelValue.email"
                        @update:modelValue="
                            (value) => updateModel('email', value)
                        "
                        type="email"
                        :class="{ 'p-invalid': hasError('email') }"
                        :disabled="disabled"
                        class="w-full"
                        placeholder="email@exemple.com"
                    />
                    <small v-if="hasError('email')" class="p-error">
                        {{ getError("email") }}
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
                        :modelValue="modelValue.phone"
                        @update:modelValue="
                            (value) => updateModel('phone', value)
                        "
                        :class="{ 'p-invalid': hasError('phone') }"
                        :disabled="disabled"
                        class="w-full"
                        placeholder="+225 00 00 00 00"
                    />
                    <small v-if="hasError('phone')" class="p-error">
                        {{ getError("phone") }}
                    </small>
                </div>
            </div>
        </div>

        <!-- Adresse -->
        <div>
            <label
                for="address"
                class="block text-sm font-medium text-gray-700 mb-2"
            >
                Adresse
            </label>
            <Textarea
                id="address"
                :modelValue="modelValue.address"
                @update:modelValue="(value) => updateModel('address', value)"
                :class="{ 'p-invalid': hasError('address') }"
                :disabled="disabled"
                rows="3"
                class="w-full"
                placeholder="Entrez l'adresse complète"
            />
            <small v-if="hasError('address')" class="p-error">
                {{ getError("address") }}
            </small>
        </div>

        <!-- Informations professionnelles -->
        <div>
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                Informations Professionnelles
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label
                        for="position_id"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Poste <span class="text-red-500">*</span>
                    </label>
                    <Select
                        id="position_id"
                        :modelValue="modelValue.position_id"
                        @update:modelValue="
                            (value) => updateModel('position_id', value)
                        "
                        :options="positionOptions"
                        optionLabel="label"
                        optionValue="value"
                        :class="{ 'p-invalid': hasError('position_id') }"
                        :disabled="disabled"
                        class="w-full"
                        placeholder="Sélectionnez un poste"
                    />
                    <small v-if="hasError('position_id')" class="p-error">
                        {{ getError("position_id") }}
                    </small>
                </div>

                <div>
                    <label
                        for="salary_base"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Salaire de base <span class="text-red-500">*</span>
                    </label>
                    <InputNumber
                        id="salary_base"
                        :modelValue="modelValue.salary_base"
                        @update:modelValue="
                            (value) => updateModel('salary_base', value)
                        "
                        :class="{ 'p-invalid': hasError('salary_base') }"
                        :disabled="disabled"
                        mode="currency"
                        currency="XOF"
                        locale="fr-FR"
                        class="w-full"
                        placeholder="0"
                    />
                    <small v-if="hasError('salary_base')" class="p-error">
                        {{ getError("salary_base") }}
                    </small>
                </div>

                <div>
                    <label
                        for="status"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Statut <span class="text-red-500">*</span>
                    </label>
                    <Select
                        id="status"
                        :modelValue="modelValue.status"
                        @update:modelValue="
                            (value) => updateModel('status', value)
                        "
                        :options="statusOptions"
                        optionLabel="label"
                        optionValue="value"
                        :class="{ 'p-invalid': hasError('status') }"
                        :disabled="disabled"
                        class="w-full"
                        placeholder="Sélectionnez un statut"
                    />
                    <small v-if="hasError('status')" class="p-error">
                        {{ getError("status") }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</template>
