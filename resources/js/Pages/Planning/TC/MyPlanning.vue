<template>
  <TCLayout>
    <div class="p-6 max-w-3xl mx-auto">
      <h1 class="text-2xl font-bold mb-6">Mon Planning</h1>

      <div v-if="assignment">
        <Card class="mb-6">
          <template #title>Planning actuel</template>
          <template #content>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <p class="text-gray-500 text-sm">Modèle</p>
                <p class="font-semibold">{{ assignment.planning_model?.name }}</p>
              </div>
              <div>
                <p class="text-gray-500 text-sm">Statut</p>
                <Tag :value="assignment.status" :severity="statusSeverity(assignment.status)" />
              </div>
              <div>
                <p class="text-gray-500 text-sm">Date début</p>
                <p class="font-semibold">{{ assignment.start_date }}</p>
              </div>
              <div>
                <p class="text-gray-500 text-sm">Date fin</p>
                <p class="font-semibold">{{ assignment.end_date ?? 'En cours' }}</p>
              </div>
            </div>
          </template>
        </Card>

        <Card>
          <template #title>Heures par jour</template>
          <template #content>
            <div class="grid grid-cols-4 gap-4">
              <div v-for="day in days" :key="day.field" class="bg-gray-50 rounded-lg p-4 text-center">
                <p class="text-gray-500 text-sm mb-1">{{ day.label }}</p>
                <p class="text-2xl font-bold" :class="assignment.planning_model?.[day.field] > 0 ? 'text-blue-600' : 'text-gray-300'">
                  {{ assignment.planning_model?.[day.field] ?? 0 }}h
                </p>
              </div>
            </div>
          </template>
        </Card>
      </div>

      <div v-else class="text-center py-12">
        <i class="pi pi-calendar-times text-gray-300 text-5xl mb-4"></i>
        <p class="text-gray-500">Aucun planning assigné pour le moment.</p>
      </div>
    </div>
  </TCLayout>
</template>

<script setup>
import TCLayout from '@/Layouts/TCLayout.vue'
import { Card, Tag } from 'primevue'

const props = defineProps({
  assignment: Object,
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

function statusSeverity(status) {
  const map = {
    'en attente': 'warning',
    'validé':     'success',
    'suspendu':   'danger',
    'terminé':    'secondary',
  }
  return map[status] ?? 'info'
}
</script>