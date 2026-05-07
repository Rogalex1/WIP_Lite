<template>
  <AppLayout title="Modèles de Planning">
    <div class="p-6">

      <!-- Header -->
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Modèles de Planning</h1>
        <Button 
          label="Nouveau modèle" 
          icon="pi pi-plus" 
          @click="$inertia.visit(planningModelRoute('create'))" 
        />
      </div>

      <!-- Flash messages -->
      <Message v-if="$page.props.flash?.success" severity="success" class="mb-4">
        {{ $page.props.flash.success }}
      </Message>
      <Message v-if="$page.props.flash?.error" severity="error" class="mb-4">
        {{ $page.props.flash.error }}
      </Message>

      <!-- Table -->
      <DataTable 
        :value="models.data" 
        paginator 
        :rows="10"
        stripedRows
        class="shadow rounded-xl"
      >
        <Column field="name" header="Nom" sortable />
        <Column field="total_hours" header="Total heures/semaine" sortable>
          <template #body="{ data }">
            <Tag :value="data.total_hours + 'h'" severity="info" />
          </template>
        </Column>
        <Column field="created_by" header="Créé par" />
        <Column field="created_at" header="Date création" sortable />
        <Column header="Actions">
          <template #body="{ data }">
            <div class="flex gap-2">
              <Button 
                icon="pi pi-eye" 
                severity="info" 
                text 
                @click="$inertia.visit(planningModelRoute('show', data.id))" 
              />
              <Button 
                icon="pi pi-pencil" 
                severity="warning" 
                text 
                @click="$inertia.visit(planningModelRoute('edit', data.id))" 
              />
              <Button 
                icon="pi pi-trash" 
                severity="danger" 
                text 
                @click="confirmDelete(data)" 
              />
            </div>
          </template>
        </Column>
      </DataTable>

    </div>

 

  </AppLayout>
</template>

<script setup>
import { Button, DataTable, Column, Tag, Message } from 'primevue'
import { useConfirm } from 'primevue/useconfirm'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AdminLayout.vue'
import { ConfirmDialog } from 'primevue'
import { useRoleRoutes } from '@/Composables/useRoleRoutes.js'

const props = defineProps({
  models: Object,
})

const confirm = useConfirm()
const { planningModelRoute } = useRoleRoutes()

function confirmDelete(model) {
  confirm.require({
    message: `Voulez-vous supprimer le modèle "${model.name}" ?`,
    header: 'Confirmation',
    icon: 'pi pi-exclamation-triangle',
    acceptLabel: 'Supprimer',
    rejectLabel: 'Annuler',
    acceptClass: 'p-button-danger',
    accept: () => {
      router.delete(planningModelRoute('destroy', model.id))
    },
  })
}
</script>