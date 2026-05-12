<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    employees: Array,
    startDate: String,
    endDate: String,
    role: String,
    currentWeek: String,
});

const page = usePage();
const flash = computed(() => page.props.flash || {});

const selectedWeek = ref(props.currentWeek);
const searchQuery = ref('');

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
    
    const query = searchQuery.value.toLowerCase();
    return props.employees.filter(employee => 
        employee.first_name.toLowerCase().includes(query) ||
        employee.last_name.toLowerCase().includes(query) ||
        employee.matricule.toLowerCase().includes(query)
    );
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
    // Utiliser la timesheet de la période spécifique
    const timesheet = employee.timesheet_for_period;
    if (!timesheet || !timesheet.entries) return '-';
    
    // Chercher l'entrée pour cette date
    const entry = timesheet.entries.find(e => e.date === date);
    if (!entry) {
        // Afficher les heures prévues si aucune entrée
        const plannedHours = employee.planning_hours?.[date] || 0;
        return plannedHours > 0 ? `<span class="text-gray-400">${plannedHours}h prévues</span>` : '-';
    }
    
    const hours = entry.total_hours || 0;
    const planned = entry.planned_hours || employee.planning_hours?.[date] || 0;
    const overtime = entry.overtime_hours || 0; // Peut être négatif
    
    // Afficher les heures avec l'écart
    if (overtime > 0) {
        return `<span class="text-green-600 font-medium">${hours}h (+${overtime})</span>`;
    } else if (overtime < 0) {
        return `<span class="text-red-600 font-medium">${hours}h (${overtime})</span>`;
    } else {
        return `${hours}h`;
    }
};

const getTotalHours = (employee) => {
    const timesheet = employee.timesheet_for_period;
    if (!timesheet || !timesheet.entries) return '0h';
    
    const total = timesheet.entries.reduce((sum, entry) => sum + (entry.total_hours || 0), 0);
    return `${total}h`;
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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
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
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
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
                            <tr v-for="employee in filteredEmployees" :key="employee.id" class="hover:bg-gray-50">
                                <!-- Employé -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
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
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ getTotalHours(employee) }}
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <Link :href="`/timesheets/${employee.id}?week=${selectedWeek}`" 
                                          class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                        {{ role === 'tc' ? 'Consulter' : 'Saisir' }}
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
            </div>
        </div>
    </div>
</template>
