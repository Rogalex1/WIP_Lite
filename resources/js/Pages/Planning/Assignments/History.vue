<template>
  <AppLayout title="Historique Planning">
    <div class="p-6 max-w-4xl mx-auto">

      <div class="flex justify-between items-center mb-6">
        <div>
          <h1 class="text-2xl font-bold">Historique des changements</h1>
          <p class="text-gray-500">
            {{ assignment.employee?.name }} — {{ assignment.planning_model?.name }}
          </p>
        </div>
        <Button
          label="Retour"
          icon="pi pi-arrow-left"
          severity="secondary"
          @click="$inertia.visit(planningAssignmentRoute('index'))"
        />
      </div>

      <Card>
        <template #content>
          <DataTable :value="histories" stripedRows>
            <Column field="created_at" header="Date" sortable />
            <Column header="Ancien statut">
              <template #body="{ data }">
                <Tag
                  v-if="data.old_status"
                  :value="data.old_status"
                  :severity="statusSeverity(data.old_status)"
                />
                <span v-else class="text-gray-400">—</span>
              </template>
            </Column>
            <Column header="Nouveau statut">
              <template #body="{ data }">
                <Tag :value="data.new_status" :severity="statusSeverity(data.new_status)" />
              </template>
            </Column>
            <Column field="changed_by" header="Modifié par" />
            <Column field="reason" header="Raison" />
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