<template>

    <div class="p-6 max-w-3xl mx-auto">

      <h1 class="text-2xl font-bold mb-6">Modifier : {{ props.model.name }}</h1>

      <Card>
        <template #content>
          <form @submit.prevent="submit" class="flex flex-col gap-4">

            <!-- Nom -->
            <div class="flex flex-col gap-1">
              <label class="font-medium">Nom du modèle *</label>
              <InputText v-model="form.name" />
              <small class="text-red-500">{{ form.errors.name }}</small>
            </div>

            <!-- Description -->
            <div class="flex flex-col gap-1">
              <label class="font-medium">Description</label>
              <Textarea v-model="form.description" rows="3" />
            </div>

            <!-- Heures par jour -->
            <div class="grid grid-cols-2 gap-4">
              <div v-for="day in days" :key="day.field" class="flex flex-col gap-1">
                <label class="font-medium">{{ day.label }}</label>
                <InputNumber 
                  v-model="form[day.field]" 
                  :min="0" 
                  :max="24"
                  :minFractionDigits="0"
                  :maxFractionDigits="2"
                  suffix="h"
                />
                <small class="text-red-500">{{ form.errors[day.field] }}</small>
              </div>
            </div>

            <!-- Total calculé -->
            <div class="bg-blue-50 rounded-lg p-4 text-center">
              <span class="text-lg font-bold text-blue-700">
                Total : {{ totalHeures }}h / semaine
              </span>
            </div>

            <!-- Boutons -->
            <div class="flex justify-end gap-3 mt-4">
              <Button 
                label="Annuler" 
                severity="secondary" 
                type="button"
                @click="$inertia.visit(route('planning-models.index'))" 
              />
              <Button 
                label="Mettre à jour" 
                icon="pi pi-save" 
                type="submit" 
                :loading="form.processing" 
              />
            </div>

          </form>
        </template>
      </Card>

    </div>

</template>

<script setup>
import { computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AdminLayout.vue'
import { Button, Card, InputText, InputNumber, Textarea } from 'primevue'

const props = defineProps({
  model: Object,
})

const days = [
  { label: 'Lundi',    field: 'monday_hours' },
  { label: 'Mardi',    field: 'tuesday_hours' },
  { label: 'Mercredi', field: 'wednesday_hours' },
  { label: 'Jeudi',    field: 'thursday_hours' },
  { label: 'Vendredi', field: 'friday_hours' },
  { label: 'Samedi',   field: 'saturday_hours' },
  { label: 'Dimanche', field: 'sunday_hours' },
]

const form = useForm({
  name:             props.model.name,
  description:      props.model.description,
  monday_hours:     props.model.monday_hours,
  tuesday_hours:    props.model.tuesday_hours,
  wednesday_hours:  props.model.wednesday_hours,
  thursday_hours:   props.model.thursday_hours,
  friday_hours:     props.model.friday_hours,
  saturday_hours:   props.model.saturday_hours,
  sunday_hours:     props.model.sunday_hours,
})

const totalHeures = computed(() => {
  return (
    (form.monday_hours    || 0) +
    (form.tuesday_hours   || 0) +
    (form.wednesday_hours || 0) +
    (form.thursday_hours  || 0) +
    (form.friday_hours    || 0) +
    (form.saturday_hours  || 0) +
    (form.sunday_hours    || 0)
  ).toFixed(2)
})

function submit() {
  form.put(route('planning-models.update', props.model.id))
}
</script>

<script>
import CPLayout from '@/Layouts/CPLayout.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SUPLayout from '@/Layouts/SUPLayout.vue';
import TClayout from '@/Layouts/TCLayout.vue';

export default {
    layout: (h, page) => {
        const layouts = {
            cp: CPLayout,
            sup: SUPLayout,
            tc: TClayout,
            admin: AdminLayout
        };

        // ✅ FIX ICI
        const role = page.props.auth?.user?.role?.name;

        const selectedLayout = layouts[role] || TClayout;

        return h(selectedLayout, [page]);
    }
}
</script>