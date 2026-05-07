<template>
  <AppLayout title="Détail modèle de planning">
    <div class="p-6 max-w-4xl mx-auto">

      <!-- Header -->
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">{{ model.name }}</h1>
        <div class="flex gap-2">
          <Button
            label="Modifier"
            icon="pi pi-pencil"
            severity="warning"
            @click="$inertia.visit(planningModelRoute('edit', model.id))"
          />
          <Button
            label="Retour"
            icon="pi pi-arrow-left"
            severity="secondary"
            @click="$inertia.visit(planningModelRoute('index'))"
          />
        </div>
      </div>

      <!-- Infos générales -->
      <Card class="mb-6">
        <template #title>Informations générales</template>
        <template #content>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-gray-500 text-sm">Nom</p>
              <p class="font-semibold">{{ model.name }}</p>
            </div>
            <div>
              <p class="text-gray-500 text-sm">Total heures / semaine</p>
              <Tag :value="model.total_hours + 'h'" severity="info" />
            </div>
            <div>
              <p class="text-gray-500 text-sm">Créé par</p>
              <p class="font-semibold">{{ model.created_by ?? 'N/A' }}</p>
            </div>
            <div>
              <p class="text-gray-500 text-sm">Date création</p>
              <p class="font-semibold">{{ model.created_at }}</p>
            </div>
            <div class="col-span-2">
              <p class="text-gray-500 text-sm">Description</p>
              <p class="font-semibold">{{ model.description ?? 'Aucune description' }}</p>
            </div>
          </div>
        </template>
      </Card>

      <!-- Heures par jour -->
      <Card>
        <template #title>Heures par jour</template>
        <template #content>
          <div class="grid grid-cols-4 gap-4">
            <div
              v-for="day in days"
              :key="day.field"
              class="bg-gray-50 rounded-lg p-4 text-center"
            >
              <p class="text-gray-500 text-sm mb-1">{{ day.label }}</p>
              <p class="text-2xl font-bold" :class="model[day.field] > 0 ? 'text-blue-600' : 'text-gray-300'">
                {{ model[day.field] }}h
              </p>
            </div>
          </div>
        </template>
      </Card>

    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AdminLayout.vue'
import { Button, Card, Tag } from 'primevue'
import { useRoleRoutes } from '@/Composables/useRoleRoutes.js'

const { planningModelRoute } = useRoleRoutes()

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
</script>