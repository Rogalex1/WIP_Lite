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
        const role = page.props.role;
        const selectedLayout = layouts[role] || TClayout;
        return h(selectedLayout, [page]);
    }
}
</script>

<script setup>

import { ref, computed,watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import Paginator from 'primevue/paginator';

const currentPage = ref(1);
const rowsPerPage = ref(5);


const props = defineProps({
    employees: Array,
    auth_employee_id: Number, // Ajoutez ceci
    startDate: String,
    endDate: String,
    role: String,
    currentWeek: String,
});

const page = usePage();
const flash = computed(() => page.props.flash || {});


const toast = useToast();


watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) {
            toast.add({
                severity: 'success',
                summary: 'Succès',
                detail: flash.success,
                life: 4000
            });
        }

        if (flash?.error) {
            toast.add({
                severity: 'error',
                summary: 'Erreur',
                detail: flash.error,
                life: 4000
            });
        }
    },
    { immediate: true, deep: true }
);
const selectedWeek = ref(props.currentWeek);
const searchQuery = ref('');
const selectedEmployees = ref([]);
const selectAll = ref(false);


watch(selectedEmployees, (newVal) => {
    selectAll.value =
        newVal.length === filteredEmployees.value.length &&
        filteredEmployees.value.length > 0;
});
const weekDays = computed(() => {
    // S'assurer que startDate est bien un lundi
    const start = new Date(props.startDate + 'T12:00:00');
    const dayOfWeek = start.getDay();
    
    // Calcul pour trouver le lundi de cette semaine
    // getDay(): 0=Dimanche, 1=Lundi, ..., 6=Samedi
    const mondayOffset = dayOfWeek === 0 ? -6 : 1 - dayOfWeek; // Si dimanche, reculer de 6 jours, sinon reculer jusqu'à lundi
    const monday = new Date(start);
    monday.setDate(start.getDate() + mondayOffset);
    
    const days = [];
    const dayNames = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
    
    for (let i = 0; i < 7; i++) {
        const date = new Date(monday);
        date.setDate(monday.getDate() + i);
        days.push({
            // Format ISO YYYY-MM-DD
            date: date.toISOString().split('T')[0],
            name: dayNames[i],
            dayNumber: date.getDate(),
        });
    }
    return days;
});

const formatDisplayHours = (decimalHours) => {
    if (decimalHours === undefined || decimalHours === null || isNaN(decimalHours)) return "0h 00";
    const absoluteValue = Math.abs(decimalHours);
    const h = Math.floor(absoluteValue);
    const m = Math.round((absoluteValue - h) * 60);
    const sign = decimalHours < 0 ? "-" : "";
    return `${sign}${h}h ${m.toString().padStart(2, '0')}`;
};

const weekOptions = computed(() => {
    const options = [];
    // On part d'aujourd'hui
    for (let i = -4; i <= 4; i++) {
        const d = new Date();
        d.setDate(d.getDate() + (i * 7));
        
        // Calcul du numéro de semaine ISO
        const target = new Date(d.valueOf());
        const dayNr = (d.getDay() + 6) % 7;
        target.setDate(target.getDate() - dayNr + 3);
        const firstThursday = target.valueOf();
        target.setMonth(0, 1);
        if (target.getDay() !== 4) {
            target.setMonth(0, 1 + ((4 - target.getDay()) + 7) % 7);
        }
        const weekNum = 1 + Math.ceil((firstThursday - target) / 604800000);
        const year = d.getFullYear();
        
        // Format YYYY-Www (ex: 2024-W18)
        const weekValue = `${year}-W${String(weekNum).padStart(2, '0')}`;
        
        // Calcul des libellés (Lundi au Dimanche)
        const startOfWeek = new Date(d);
        const day = startOfWeek.getDay();
        const diff = startOfWeek.getDate() - day + (day === 0 ? -6 : 1);
        startOfWeek.setDate(diff);
        
        const endOfWeek = new Date(startOfWeek);
        endOfWeek.setDate(startOfWeek.getDate() + 6);
        
        options.push({
            value: weekValue,
            label: `Semaine du ${startOfWeek.toLocaleDateString('fr-FR')} au ${endOfWeek.toLocaleDateString('fr-FR')}`
        });
    }
    return options;
});

const filteredEmployees = computed(() => {
    if (!searchQuery.value) return props.employees;

    const q = searchQuery.value.toLowerCase();

    return props.employees.filter(e =>
        e.first_name.toLowerCase().includes(q) ||
        e.last_name.toLowerCase().includes(q) ||
        e.matricule.toLowerCase().includes(q)
    );
});

/* ======================
   PAGINATION
====================== */
const paginatedEmployees = computed(() => {
    const start = (currentPage.value - 1) * rowsPerPage.value;
    return filteredEmployees.value.slice(start, start + rowsPerPage.value);
});

const changeWeek = () => {
    router.get('/timesheets', { week: selectedWeek.value }, { preserveState: false });
};

const getStatusBadge = (status) => {
    const badges = {
        'brouillon': 'bg-gray-100 text-gray-800',
        'soumis': 'bg-blue-100 text-blue-800',
        'validé': 'bg-green-100 text-green-800',
        'Non débuté': 'bg-gray-100 text-gray-800'
    };
    return badges[status] || 'bg-gray-100 text-gray-800';
};

const getHoursForDay = (employee, date) => {
    const timesheet = employee.timesheet_for_period;
    if (!timesheet || !timesheet.entries) return '-';
    
    const entry = timesheet.entries.find(e => e.date === date);
    if (!entry) {
        const plannedHours = parseFloat(employee.planning_hours?.[date]) || 0;
        return plannedHours > 0 ? `<span class="text-gray-400">${formatDisplayHours(plannedHours)} prévues</span>` : '-';
    }
    
    const hours = parseFloat(entry.total_hours) || 0;
    const overtime = parseFloat(entry.overtime_hours) || 0;
    
    if (overtime > 0) {
        return `<span class="text-green-600 font-medium">${formatDisplayHours(hours)} (+${formatDisplayHours(overtime)})</span>`;
    } else if (overtime < 0) {
        // Le signe "-" est déjà géré par formatDisplayHours
        return `<span class="text-red-600 font-medium">${formatDisplayHours(hours)} (${formatDisplayHours(overtime)})</span>`;
    } else {
        return formatDisplayHours(hours);
    }
};

// Fonctions pour la sélection multiple
const toggleSelectAll = () => {
    if (selectAll.value) {
        selectedEmployees.value = filteredEmployees.value.map(emp => emp.id);
    } else {
        selectedEmployees.value = [];
    }
};

const toggleEmployeeSelection = (employeeId) => {
    const index = selectedEmployees.value.indexOf(employeeId);
    if (index > -1) {
        selectedEmployees.value.splice(index, 1);
    } else {
        selectedEmployees.value.push(employeeId);
    }
    
    // Mettre à jour l'état de "tout sélectionner"
    selectAll.value = selectedEmployees.value.length === filteredEmployees.value.length && filteredEmployees.value.length > 0;
};

const canBulkAction = computed(() => {
    return selectedEmployees.value.length > 0;
});

const getSelectedEmployeesStatus = () => {
    const statuses = selectedEmployees.value.map(empId => {
        const emp = filteredEmployees.value.find(e => e.id === empId);
        return emp ? getTimesheetStatus(emp) : null;
    }).filter(Boolean);
    
    const uniqueStatuses = [...new Set(statuses)];
    return uniqueStatuses;
};

const canBulkValidate = computed(() => {
    const statuses = getSelectedEmployeesStatus();
    return statuses.length === 1 && statuses[0] === 'soumis' && props.role === 'cp';
});

const canBulkEntry = computed(() => {
    return selectedEmployees.value.length >= 2 && props.role !== 'tc';
});

// Fonction pour la saisie groupée
const bulkEntry = () => {
    console.log("CLICK BULK ENTRY");
    console.log(selectedEmployees.value);

    const ids = selectedEmployees.value.join(',');
    const url = `/timesheets/bulk-entry?ids=${ids}&week=${selectedWeek.value}`;

    console.log(url);

    router.get(url, {}, {
    preserveState: false,
    preserveScroll: false,
    replace: false
}); // ✅ IMPORTANT (Inertia navigation)
};

const canBulkSubmit = computed(() => {
    const statuses = getSelectedEmployeesStatus();
    return statuses.every(status => status === 'brouillon') && props.role !== 'tc';
});

// Actions en masse
const bulkValidate = () => {
   
    
    router.post('/timesheets/bulk-validate', {
        employee_ids: selectedEmployees.value,
        week: selectedWeek.value
    }, {
        onSuccess: () => {
            selectedEmployees.value = [];
            selectAll.value = false;
        }
    });
};

const bulkSubmit = () => {
    
    
    router.post('/timesheets/bulk-submit', {
        employee_ids: selectedEmployees.value,
        week: selectedWeek.value
    }, {
        onSuccess: () => {
            selectedEmployees.value = [];
            selectAll.value = false;
        }
    });
};

const getTotalHours = (employee) => {
    const timesheet = employee.timesheet_for_period;
    if (!timesheet || !timesheet.entries) return '0h 00';
    
    // On calcule le cumul des heures réelles ET des écarts
    const totals = timesheet.entries.reduce((acc, entry) => {
        acc.real += parseFloat(entry.total_hours) || 0;
        acc.overtime += parseFloat(entry.overtime_hours) || 0;
        return acc;
    }, { real: 0, overtime: 0 });
    
    const formattedReal = formatDisplayHours(totals.real);
    const formattedOvertime = formatDisplayHours(totals.overtime);

    // Affichage conditionnel selon si l'écart est positif, négatif ou nul
    if (totals.overtime > 0) {
        return `<span class="font-medium">${formattedReal}</span> <span class="text-green-600 text-xs">(+${formattedOvertime})</span>`;
    } else if (totals.overtime < 0) {
        // Le signe "-" est déjà inclus dans formattedOvertime
        return `<span class="font-medium">${formattedReal}</span> <span class="text-red-600 text-xs">(${formattedOvertime})</span>`;
    } else {
        return `<span class="font-medium">${formattedReal}</span>`;
    }
};

const getTimesheetStatus = (employee) => {
    const timesheet = employee.timesheet_for_period;
    if (!timesheet) return 'Non débuté';
    return timesheet.status;
};

const getHeaderText = () => {
    if (props.role === 'sup') return 'Saisie des heures pour vos Téléconseillers';
    if (props.role === 'cp') return 'Validation des heures pour vos Superviseurs';
    return 'Consultation de vos heures';
};
</script>

<template>
    <Head title="Gestion des Heures" />
   <Toast />
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Gestion des Heures</h1>
                        <p class="text-sm text-gray-600 mt-1">{{ getHeaderText() }}</p>
                    </div>
                    
                    <!-- Sélecteur de période -->
                    <div class="flex items-center space-x-4">
                        <label class="text-sm font-medium text-gray-700">Semaine :</label>
                        <select 
                            v-model="selectedWeek" 
                            @change="changeWeek"
                            class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option v-for="option in weekOptions" :key="option.value" :value="option.value">
                                {{ option.label }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        <div v-if="flash.success" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                {{ flash.success }}
            </div>
        </div>
        <div v-if="flash.error" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                {{ flash.error }}
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <!-- Filtre de recherche -->
            <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
                <div class="flex items-center space-x-4">
                    <div class="flex-1 max-w-md">
                        <input 
                            type="text" 
                            v-model="searchQuery"
                            placeholder="Rechercher par nom, prénom ou matricule..."
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>
                    <div class="text-sm text-gray-600">
                        {{ filteredEmployees.length }} employé(s) trouvé(s)
                    </div>
                </div>
            </div>

            <!-- Tableau principal -->
            <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
                <div class="max-w-[1500px] overflow-x-auto"> 
                    <table class="min-w-[1400px] w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input 
                                        type="checkbox" 
                                        v-model="selectAll"
                                        @change="toggleSelectAll"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    />
                                    Employé
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Statut
                                </th>
                                <!-- Jours de la semaine -->
                                <th v-for="day in weekDays" :key="day.date" 
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[80px]">
                                    <div>{{ day.name }}</div>
                                    <div class="text-xs text-gray-400">{{ day.dayNumber }}</div>
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="employee in paginatedEmployees" :key="employee.id" class="hover:bg-gray-50">
                                <!-- Employé -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <input 
                                            type="checkbox" 
                                            :value="employee.id"
                                            v-model="selectedEmployees"
                                            
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3"
                                        />
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center text-white font-semibold text-sm">
                                                {{ employee.first_name[0] }}{{ employee.last_name[0] }}
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ employee.first_name }} {{ employee.last_name }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ employee.matricule }} • {{ employee.position.name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Statut -->
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span :class="'px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ' + getStatusBadge(getTimesheetStatus(employee))">
                                        {{ getTimesheetStatus(employee).charAt(0).toUpperCase() + getTimesheetStatus(employee).slice(1) }}
                                    </span>
                                </td>

                                <!-- Heures par jour -->
                                <td v-for="day in weekDays" :key="day.date" class="px-4 py-4 whitespace-nowrap text-center">
                                    <div v-html="getHoursForDay(employee, day.date)"></div>
                                </td>

                                <!-- Total -->
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-sm text-gray-900" v-html="getTotalHours(employee)">
                                    </div>
                                </td>

                                <!-- Actions -->
<td class="px-6 py-4 whitespace-nowrap text-center">
    <template v-if="employee.id === props.auth_employee_id">
        <Link v-if="getTimesheetStatus(employee) !== 'Non débuté'"
              :href="`/timesheets/${employee.id}?week=${selectedWeek}`" 
              class="inline-flex items-center px-3 py-1 border border-blue-600 text-xs font-medium rounded-md text-blue-600 hover:bg-blue-50 transition-colors">
            Consulter ma fiche
        </Link>
        <span v-else class="text-xs text-gray-400 italic">Aucune donnée</span>
    </template>
    
    <template v-else>
        <Link :href="`/timesheets/${employee.id}?week=${selectedWeek}`" 
              :class="[
                  'inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white transition-colors',
                  getTimesheetStatus(employee) === 'validé' ? 'bg-green-600 hover:bg-green-700' : 'bg-blue-600 hover:bg-blue-700'
              ]">
            {{ (getTimesheetStatus(employee) === 'validé' || props.role === 'tc') ? 'Consulter' : 'Saisir' }}
        </Link>
    </template>
</td>
                            </tr>
                        </tbody>
                    </table>
<Paginator
    :first="(currentPage - 1) * rowsPerPage"
    :rows="rowsPerPage"
    :totalRecords="filteredEmployees.length"
    :rowsPerPageOptions="[5, 10, 20]"
    @page="(e) => {
        currentPage = e.page + 1;
        rowsPerPage = e.rows;
    }"
/>
                </div>
                
                <!-- Message si vide -->
                <div v-if="filteredEmployees.length === 0" class="text-center py-12">
                    <div class="text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="mt-2">
                            {{ searchQuery ? 'Aucun employé trouvé pour cette recherche.' : 'Aucun employé disponible pour cette période.' }}
                        </p>
                    </div>
                </div>

                <!-- Actions en masse -->
                <div v-if="canBulkAction" class="flex items-center justify-between bg-white border-t px-6 py-4">
                    <div class="text-sm text-gray-700">
                        {{ selectedEmployees.length }} employé(s) sélectionné(s)
                    </div>
                    <div class="flex items-center space-x-3">
                        <button 
                            v-if="canBulkEntry"
                            @click="bulkEntry"
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-sm font-medium">
                            Saisie groupée
                        </button>
                        <button 
                            v-if="canBulkSubmit"
                            @click="bulkSubmit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                            Soumettre les timesheets
                        </button>
                        <button 
                            v-if="canBulkValidate"
                            @click="bulkValidate"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
                            Valider les timesheets
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
