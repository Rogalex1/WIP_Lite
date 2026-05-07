<template>
  <AppLayout title="Détail affectation">
    <div class="p-6 max-w-4xl mx-auto">

      <!-- Header -->
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Détail de l'affectation</h1>
        <Button
          label="Retour"
          icon="pi pi-arrow-left"
          severity="secondary"
          @click="$inertia.visit(planningAssignmentRoute('index'))"
        />
      </div>

      <!-- Infos -->
      <Card class="mb-6">
        <template #title>Informations</template>
        <template #content>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-gray-500 text-sm">Employé</p>
              <p class="font-semibold">{{ assignment.employee?.name }}</p>
              <p class="text-sm text-gray-400">{{ assignment.employee?.matricule }}</p>
            </div>
            <div>
              <p class="text-gray-500 text-sm">Modèle Planning</p>
              <p class="font-semibold">{{ assignment.planningModel?.name }}</p>
              <Tag
                v-if="assignment.planningModel"
                :value="assignment.planningModel.total_hours + 'h/sem'"
                severity="info"
              />
            </div>
            <div>
              <p class="text-gray-500 text-sm">Date début</p>
              <p class="font-semibold">{{ assignment.start_date }}</p>
            </div>
            <div>
              <p class="text-gray-500 text-sm">Date fin</p>
              <p class="font-semibold">{{ assignment.end_date ?? 'En cours' }}</p>
            </div>
            <div>
              <p class="text-gray-500 text-sm">Statut</p>
              <Tag :value="assignment.status" :severity="statusSeverity(assignment.status)" />
            </div>
            <div v-if="assignment.validated_by">
              <p class="text-gray-500 text-sm">Validé par</p>
              <p class="font-semibold">{{ assignment.validated_by }}</p>
              <p class="text-sm text-gray-400">{{ assignment.validated_at }}</p>
            </div>
          </div>
        </template>
      </Card>

      <!-- Historique -->
      <Card>
        <template #title>Historique des changements</template>
        <template #content>
          <DataTable :value="histories" stripedRows>
            <Column field="old_status" header="Ancien statut" />
            <Column field="new_status" header="Nouveau statut" />
            <Column field="changed_by" header="Modifié par" />
            <Column field="reason" header="Raison" />
            <Column field="created_at" header="Date" />
          </DataTable>
        </template>
      </Card>

    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AdminLayout.vue'
import { Button, Card, Tag, DataTable, Column } from 'primevue'
import { useRoleRoutes } from '@/Composables/useRoleRoutes.js'

const { planningAssignmentRoute } = useRoleRoutes()

const props = defineProps({
  assignment: Object,
  histories:  Array,
})

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