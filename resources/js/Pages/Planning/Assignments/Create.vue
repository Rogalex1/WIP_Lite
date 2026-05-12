<template>

    <div class="p-6 max-w-3xl mx-auto">

      <h1 class="text-2xl font-bold mb-6">Nouvelle affectation</h1>

      <Card>
        <template #content>
          <form @submit.prevent="submit" class="flex flex-col gap-4">

            <!-- Employé -->
            <div class="flex flex-col gap-1">
              <label class="font-medium">Employé *</label>
              <Dropdown
                v-model="form.employee_id"
                :options="employees"
                optionLabel="label"
                optionValue="id"
                placeholder="Sélectionner un employé"
                filter
                class="w-full"
              />
              <small class="text-red-500">{{ form.errors.employee_id }}</small>
            </div>

            <!-- Modèle de planning -->
            <div class="flex flex-col gap-1">
              <label class="font-medium">Modèle de planning *</label>
              <Dropdown
                v-model="form.planning_model_id"
                :options="planningModels"
                optionLabel="label"
                optionValue="id"
                placeholder="Sélectionner un modèle"
                class="w-full"
              />
              <small class="text-red-500">{{ form.errors.planning_model_id }}</small>
            </div>

            <!-- Dates -->
            <div class="grid grid-cols-2 gap-4">
              <div class="flex flex-col gap-1">
                <label class="font-medium">Date de début *</label>
                <DatePicker v-model="form.start_date" dateFormat="dd/mm/yy" class="w-full" />
                <small class="text-red-500">{{ form.errors.start_date }}</small>
              </div>
              <div class="flex flex-col gap-1">
                <label class="font-medium">Date de fin</label>
                <DatePicker v-model="form.end_date" dateFormat="dd/mm/yy" class="w-full" />
                <small class="text-red-500">{{ form.errors.end_date }}</small>
              </div>
            </div>

            <!-- Boutons -->
            <div class="flex justify-end gap-3 mt-4">
              <Button
                label="Annuler"
                severity="secondary"
                type="button"
                @click="$inertia.visit(route('planning-assignments.index'))"
              />
              <Button
                label="Enregistrer"
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
import { Button, Card, Dropdown, DatePicker } from 'primevue'

const props = defineProps({
  employees:      Array,
  planningModels: Array,
})

// Formater pour le Dropdown
const employees = computed(() =>
  props.employees.map(e => ({
    id:    e.id,
    label: `${e.first_name} ${e.last_name} (${e.matricule})`,
  }))
)

const planningModels = computed(() =>
  props.planningModels.map(m => ({
    id:    m.id,
    label: `${m.name} — ${m.total_hours}h/sem`,
  }))
)

const form = useForm({
  employee_id:       null,
  planning_model_id: null,
  start_date:        null,
  end_date:          null,
})

function submit() {
  const data = {
    ...form.data(),
    start_date: form.start_date ? new Date(form.start_date).toISOString().split('T')[0] : null,
    end_date:   form.end_date   ? new Date(form.end_date).toISOString().split('T')[0]   : null,
  }
  form.transform(() => data).post(route('planning-assignments.store'))
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