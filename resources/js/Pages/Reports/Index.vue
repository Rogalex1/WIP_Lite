<script setup>
import { Head } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Tag from 'primevue/tag';
import AdminLayout from '@/Layouts/AdminLayout.vue';

import { ref, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { useToast } from "primevue/usetoast";

// Composants PrimeVue
import Dialog from 'primevue/dialog';
import Toast from 'primevue/toast';
import InputNumber from 'primevue/inputnumber';
import Calendar from 'primevue/calendar';
import Dropdown from 'primevue/dropdown';
import Textarea from 'primevue/textarea';
import Button from 'primevue/button';
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from "primevue/useconfirm"; // Import

const confirm = useConfirm();
const toast = useToast();

const props = defineProps({ reports: Array, campaigns: Array });
const visible = ref(false); // État du pop-up
const isEdit = ref(false);

// Formulaire Inertia
const form = useForm({
    id: null,
    campaign_id: null,
    report_date: new Date(),
    calls_count: 0,
    success_count: 0,
    dmc: 0,
    comment: ''
});

const openNew = () => {
    form.reset();
    isEdit.value = false;
    visible.value = true;
};

const editReport = (data) => {
    form.id = data.id;
    form.campaign_id = data.campaign_id;
    form.report_date = new Date(data.report_date);
    form.calls_count = data.calls_count;
    form.success_count = data.success_count;
    form.dmc = data.dmc;
    form.comment = data.comment;
    isEdit.value = true;
    visible.value = true;
};

const submitForm = () => {
    // Formater la date en YYYY-MM-DD
    const payload = { ...form.data() };
    if (payload.report_date instanceof Date) {
        payload.report_date = payload.report_date.toISOString().split('T')[0];
    }

    if (isEdit.value) {
        form.transform((data) => ({
            ...data,
            report_date: new Date(data.report_date).toISOString().split('T')[0]
        })).put(route('reporting.update', form.id), {
            onSuccess: () => {
                visible.value = false;
                toast.add({ severity: 'success', summary: 'Succès', detail: 'Rapport mis à jour', life: 3000 });
            }
        });
    } else {
        form.transform((data) => ({
            ...data,
            report_date: new Date(data.report_date).toISOString().split('T')[0]
        })).post(route('reporting.store'), {
            onSuccess: () => {
                visible.value = false;
                toast.add({ severity: 'success', summary: 'Création', detail: 'Rapport enregistré', life: 3000 });
            }
        });
    }
};

const deleteReport = (id) => {
    confirm.require({
        message: 'Êtes-vous sûr de vouloir supprimer ce rapport ?',
        header: 'Confirmation de suppression',
        icon: 'pi pi-exclamation-triangle',
        rejectLabel: 'Annuler',
        acceptLabel: 'Supprimer',
        rejectClass: 'p-button-secondary p-button-outlined',
        acceptClass: 'p-button-danger',
        accept: () => {
            // Logique Inertia si l'utilisateur accepte
            form.delete(route('reporting.destroy', id), {
                onSuccess: () => {
                    toast.add({ 
                        severity: 'info', 
                        summary: 'Supprimé', 
                        detail: 'Le rapport a été retiré avec succès', 
                        life: 3000 
                    });
                },
            });
        }
    });
}

// const props = defineProps({ reports: Array });

// Couleurs pour le taux de conversion
const getSeverity = (rate) => {
    if (rate >= 15) return 'success';
    if (rate >= 5) return 'warning';
    return 'danger';
};
</script>

<template>
    <AdminLayout>
        <Head title="Reporting de Production" />
        <Toast />
        <ConfirmDialog/>

        <div class="max-w-[1200px] mx-auto p-6 space-y-8">
            
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Production</h1>
                    <p class="text-slate-500 mt-1">Suivi des performances et statistiques d'appels.</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="hidden sm:block text-right mr-2">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total</p>
                        <p class="text-lg font-semibold text-slate-700">{{ reports.length }} Rapports</p>
                    </div>
                    <Button 
                        label="Nouveau Rapport" 
                        icon="pi pi-plus" 
                        @click="openNew" 
                        class="!bg-indigo-600 !border-none hover:!bg-indigo-700 shadow-lg shadow-indigo-200" 
                    />
                </div>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <DataTable 
                    :value="reports" 
                    responsiveLayout="scroll" 
                    class="p-datatable-custom"
                    :rows="10"
                    paginator
                    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink"
                >
                    <Column field="report_date" header="Date" sortable>
                        <template #body="{ data }">
                            <div class="flex flex-col">
                                <span class="font-semibold text-slate-700">
                                    {{ new Date(data.report_date).toLocaleDateString('fr-FR') }}
                                </span>
                            </div>
                        </template>
                    </Column>
                    
                    <Column field="user.name" header="Agent" v-if="$page.props.auth.user.role !== 'tc'">
                        <template #body="{ data }">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 text-xs font-bold">
                                    {{ data.user?.name?.charAt(0) || 'A' }}
                                </div>
                                <span class="text-slate-600 font-medium">{{ data.user?.name }}</span>
                            </div>
                        </template>
                    </Column>
                    
                    <Column field="campaign.name" header="Campagne">
                        <template #body="{ data }">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                {{ data.campaign?.name }}
                            </span>
                        </template>
                    </Column>

                    <Column field="calls_count" header="Appels" class="text-center font-mono text-slate-600"></Column>
                    <Column field="success_count" header="Succès" class="text-center font-mono text-slate-600"></Column>
                    
                    <Column header="Performance">
                        <template #body="{ data }">
                            <div class="flex items-center gap-2">
                                <Tag :value="data.conversion_rate + '%'" :severity="getSeverity(data.conversion_rate)" class="!rounded-md" />
                            </div>
                        </template>
                    </Column>

                    <Column field="dmc" header="DMC">
                        <template #body="{ data }">
                            <span class="text-slate-500 font-medium italic">{{ data.dmc }} min</span>
                        </template>
                    </Column>

                    <!-- BLOC ACTIONS CORRECTEMENT PLACÉ ICI -->
                    <Column header="Actions" headerClass="text-right" bodyClass="text-right">
                        <template #body="{ data }">
                            <div class="flex justify-end gap-1">
                                <Button icon="pi pi-pencil" text rounded severity="secondary" @click="editReport(data)" />
                                <Button icon="pi pi-trash" text rounded severity="danger" @click="deleteReport(data.id)" />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>

        <!-- Dialog de Saisie -->
        <Dialog 
            v-model:visible="visible" 
            :header="isEdit ? 'Modifier le rapport' : 'Nouvelle Saisie Production'" 
            :style="{ width: '450px' }" 
            modal 
            class="custom-dialog"
        >
            <div class="grid gap-5 py-4">
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Campagne</label>
                    <Dropdown v-model="form.campaign_id" :options="campaigns" optionLabel="name" optionValue="id" placeholder="Choisir une campagne" class="w-full shadow-sm" />
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Nb Appels</label>
                        <InputNumber v-model="form.calls_count" showButtons :min="0" class="shadow-sm" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Nb Succès</label>
                        <InputNumber v-model="form.success_count" showButtons :min="0" class="shadow-sm" />
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">DMC (min)</label>
                    <InputNumber v-model="form.dmc" :min="0" mode="decimal" :minFractionDigits="1" class="shadow-sm" />
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Commentaire</label>
                    <Textarea v-model="form.comment" rows="3" placeholder="Notes optionnelles..." class="shadow-sm shadow-slate-100" />
                </div>
            </div>

            <template #footer>
                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                    <Button label="Annuler" text severity="secondary" @click="visible = false" class="!text-slate-500" />
                    <Button :label="isEdit ? 'Mettre à jour' : 'Enregistrer'" icon="pi pi-check" @click="submitForm" :loading="form.processing" class="!bg-indigo-600 !border-none" />
                </div>
            </template>
        </Dialog>

    </AdminLayout>
</template>

<style scoped>
/* Style de la table moderne */
.p-datatable-custom :deep(.p-datatable-thead > tr > th) {
    background: #ffffff;
    color: #64748b;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 1.25rem 1rem;
    border-bottom: 2px solid #f1f5f9;
}

.p-datatable-custom :deep(.p-datatable-tbody > tr) {
    background: #ffffff;
    transition: background 0.2s;
}

.p-datatable-custom :deep(.p-datatable-tbody > tr:hover) {
    background: #f8fafc;
}

.p-datatable-custom :deep(.p-datatable-tbody > tr > td) {
    padding: 1rem;
    border-bottom: 1px solid #f1f5f9;
}

/* Style de la pagination */
.p-datatable-custom :deep(.p-paginator) {
    border: none;
    padding: 1rem;
    background: #ffffff;
}

/* Style du Dialog */
.custom-dialog :deep(.p-dialog-header) {
    border-bottom: 1px solid #f1f5f9;
    padding: 1.5rem;
}

.custom-dialog :deep(.p-dialog-content) {
    padding: 0 1.5rem;
}

/* Focus des inputs */
:deep(.p-inputtext:focus), :deep(.p-dropdown:not(.p-disabled).p-focus) {
    border-color: #6366f1;
    box-shadow: 0 0 0 2px #e0e7ff;
}
</style>
