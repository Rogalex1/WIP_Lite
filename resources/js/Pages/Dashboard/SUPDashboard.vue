<script setup>
import SUPLayout from '@/Layouts/SUPLayout.vue';
import { Head } from '@inertiajs/vue3';
import StatCard from '@/Components/StatCard.vue';

import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';

defineOptions({ layout: SUPLayout });

defineProps({
    stats: Object,
    my_agents: Array,
    pending_tasks: Array
});

const validateTask = (task) => {
    console.log('Validation de la tâche:', task);
};
</script>

<template>
    <Head title="Tableau de bord SUP" />

    <div class="space-y-8">

        <!-- HEADER -->
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">
                Bonjour, {{ $page.props.auth.user.name }}
            </h1>
            <p class="text-slate-500">Voici l'état actuel de votre plateau.</p>
        </div>

        <!-- TASKS -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-bold mb-4">Tâches en attente de validation</h2>

            <DataTable :value="pending_tasks" responsiveLayout="scroll">
                <Column field="name" header="Tâche" />
                <Column field="assignee" header="Assigné à" />

                <Column header="Action">
                    <template #body="slotProps">
                        <Button
                            label="Valider"
                            class="p-button-success p-button-sm"
                            @click.stop="validateTask(slotProps.data)"
                        />
                    </template>
                </Column>
            </DataTable>
        </div>

        <!-- STATS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <StatCard
                title="Agents Présents"
                :value="stats.present_count"
                icon="pi-user-plus"
                iconBg="bg-sky-50"
                iconColor="text-sky-600"
            />

            <StatCard
                title="Absences"
                :value="stats.absent_count"
                icon="pi-user-minus"
                iconBg="bg-rose-50"
                iconColor="text-rose-600"
            />

            <StatCard
                title="Total Équipe"
                :value="stats.total_team"
                icon="pi-users"
                iconBg="bg-slate-100"
                iconColor="text-slate-600"
            />
        </div>

        <!-- CONTENT GRID -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">

            <!-- TEAM -->
            <div class="xl:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

                <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="font-bold text-slate-800">Mon Équipe (Dernières activités)</h3>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                        Temps réel
                    </span>
                </div>

                <div class="divide-y divide-slate-100">
                    <div
                        v-for="agent in my_agents"
                        :key="agent.id"
                        class="p-4 hover:bg-slate-50 transition-colors flex items-center justify-between"
                    >
                        <div class="flex items-center gap-4">

                            <div class="relative">
                                <div class="w-10 h-10 rounded-xl bg-sky-100 flex items-center justify-center font-bold text-sky-700">
                                    {{ agent.name.charAt(0) }}
                                </div>
                                <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                            </div>

                            <div>
                                <p class="text-sm font-bold text-slate-700">{{ agent.name }}</p>
                                <p class="text-[10px] text-slate-400 italic">
                                    {{ agent.last_action }}
                                </p>
                            </div>

                        </div>

                        <button class="p-2 hover:bg-white border border-transparent hover:border-slate-200 rounded-lg transition-all text-slate-400 hover:text-sky-500">
                            <i class="pi pi-chevron-right text-xs"></i>
                        </button>
                    </div>
                </div>

            </div>

            <!-- PLANNING -->
            <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-6 text-white shadow-xl flex flex-col justify-between">

                <div>
                    <div class="w-10 h-10 bg-sky-500/20 rounded-lg flex items-center justify-center mb-6">
                        <i class="pi pi-calendar-plus text-sky-400"></i>
                    </div>

                    <h4 class="text-lg font-bold mb-2">Planning du jour</h4>

                    <p class="text-slate-400 text-sm mb-6">
                        3 agents attendent la validation de leur pause déjeuner.
                    </p>
                </div>

                <button class="w-full py-3 bg-sky-500 hover:bg-sky-400 text-white rounded-xl text-xs font-black uppercase tracking-widest">
                    Gérer les pauses
                </button>

            </div>

        </div>
    </div>
</template>