<script>
import CPLayout from '@/Layouts/CPLayout.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SUPLayout from '@/Layouts/SUPLayout.vue';
import TClayout from '@/Layouts/TCLayout.vue';

export default {
layout: (h, page) => {
    console.log('📦 PAGE PROPS DANS LAYOUT:', page.props);

    const layouts = {
        cp: CPLayout,
        sup: SUPLayout,
        tc: TClayout,
        admin: AdminLayout
    };

    const role = page.props.role || page.props.auth?.user?.role?.name;

    console.log('🎭 ROLE DANS LAYOUT:', role);

    const selectedLayout = layouts[role] || TClayout;

    console.log('🧩 LAYOUT CHOISI:', selectedLayout.name);

    return h(selectedLayout, [page]);
}
}
</script>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    employee: Object,
    timesheet: Object,
    entries: Object,
    planningHours: Object,
    startDate: String,
    endDate: String,
    planningAssignment: Object,
    week: String,
    isBulkEntry: Boolean,
    employeeIds: Array,
    employees: Array,
});

const page = usePage();
const flash = computed(() => page.props.flash || {});

// État réactif
const weekEntries = ref({});
const weekTotal = ref(0);
const weekOvertime = ref(0);
const weekPlannedTotal = ref(0);

// --- 1. Définition des fonctions ---

const calculateWeekTotals = () => {
    let total = 0;
    let overtime = 0;
    let plannedTotal = 0;
    
    // Forcer la réactivité en utilisant Object.keys
    Object.keys(weekEntries.value).forEach(dateStr => {
        const entry = weekEntries.value[dateStr];
        if (entry) {
           total += Number(entry.total_hours || 0);
            overtime += Number(entry.overtime_hours || 0);
            plannedTotal += Number(entry.planned_hours || 0);
        }
    });
    
    weekTotal.value = total;
    weekOvertime.value = overtime;
    weekPlannedTotal.value = plannedTotal;
};

const initializeEntries = () => {
    const start = new Date(props.startDate);
    const dayOfWeek = start.getDay();
    const mondayOffset = dayOfWeek === 0 ? -6 : 1 - dayOfWeek;
    const monday = new Date(start);
    monday.setDate(start.getDate() + mondayOffset);
    
    for (let i = 0; i < 7; i++) {
        const date = new Date(monday);
        date.setDate(monday.getDate() + i);
        const dateStr = date.toISOString().split('T')[0];
        const entry = props.entries[dateStr];
        
        weekEntries.value[dateStr] = {
            check_in: entry?.check_in || '',
            check_out: entry?.check_out || '',
            break_duration: Number(entry?.break_duration || 0),
            planned_hours: Number(props.planningHours[dateStr] || 0), // <--- Ici
            total_hours: Number(entry?.total_hours || 0),            // <--- Ici
            overtime_hours: Number(entry?.overtime_hours || 0),
            absence_type: entry?.absence_type || '',
            comment: entry?.comment || '',
        };
    }
};

const formatDisplayHours = (decimalHours) => {
    if (decimalHours === undefined || decimalHours === null || isNaN(decimalHours)) return "0h 00";
    
    const absoluteValue = Math.abs(decimalHours);
    const h = Math.floor(absoluteValue);
    const m = Math.round((absoluteValue - h) * 60);
    
    const sign = decimalHours < 0 ? "-" : "";
    return `${sign}${h}h ${m.toString().padStart(2, '0')}`;
};

const calculateHours = (dateStr) => {
    const entry = weekEntries.value[dateStr];
    let totalHours = 0;
    let overtimeHours = 0;
    
    if (entry.check_in && entry.check_out) {
        const start = new Date(`2000-01-01T${entry.check_in}`);
        const end = new Date(`2000-01-01T${entry.check_out}`);
        
        if (end > start) {
            totalHours = (end - start) / (1000 * 60 * 60) - (entry.break_duration / 60);
            overtimeHours = totalHours - entry.planned_hours;
        } else if (end < start) {
            // Optionnel : Gestion si l'heure de fin est le lendemain (ex: 22h à 02h)
            const endNextDay = new Date(`2000-01-02T${entry.check_out}`);
            totalHours = (endNextDay - start) / (1000 * 60 * 60) - (entry.break_duration / 60);
            overtimeHours = totalHours - entry.planned_hours;
        }
    }
    
    // Mise à jour de l'état local
    weekEntries.value[dateStr].total_hours = totalHours;
    weekEntries.value[dateStr].overtime_hours = overtimeHours;

    // IMPORTANT : Synchroniser avec le formulaire Inertia pour l'envoi
    form.entries = { ...weekEntries.value }; 
    
    calculateWeekTotals();
};

// --- 2. Initialisation du Formulaire Inertia ---

const form = useForm({
    entries: {}, // Sera rempli au montage
    action: 'draft',
    week: props.week,
    employee_ids: props.employeeIds ?? [],   // 🔥 AJOUT IMPORTANT
    is_bulk_entry: false,
});

// --- 3. Cycle de vie (Lifecycle) ---

onMounted(() => {
    initializeEntries();
    calculateWeekTotals();
    // On synchronise les données initialisées avec le formulaire
    form.entries = weekEntries.value;
});

// --- 4. Autres fonctions et calculs ---

const submit = (action) => {
    console.log("🔥 SUBMIT CLICKED");
    console.log("Action:", action);
    console.log("Bulk mode:", props.isBulkEntry);
    console.log("Employee ID:", props.employee?.id);
    console.log("Entries:", weekEntries.value);

    form.action = action;
    form.entries = weekEntries.value;

    console.log("FORM BEFORE SEND:", form);

if (props.isBulkEntry) {
    console.log("🚀 BULK MODE REQUEST");

    form.employee_ids = [...props.employeeIds.map(id => Number(id))];
    form.is_bulk_entry = true;

    console.log("FINAL PAYLOAD:", form.data());

    form.post(route('timesheets.bulkStore'), {
        preserveScroll: true,

        onSuccess: (page) => {
            console.log("✅ BULK SUCCESS:", page);
        },

        onError: (errors) => {
            console.log("❌ BULK ERRORS:", errors);
        }
    });

    return;
}

    console.log("🚀 NORMAL MODE REQUEST");

    form.post(route('timesheets.store', props.employee.id), {
        preserveScroll: true,
        onSuccess: (page) => {
            console.log("✅ SUCCESS NORMAL:", page);
        },
        onError: (errors) => {
            console.log("❌ NORMAL ERRORS:", errors);
        }
    });
};

const validateTimesheet = () => {
    router.post(`/timesheets/${props.timesheet.id}/validate`, {}, {
        onSuccess: () => {
            // Succès géré par les messages flash
        },
    });
};

const getDayName = (date) => {
    const days = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
    return days[date.getDay()];
};

const getStatusBadge = (status) => {
    const badges = {
        'brouillon': 'bg-gray-100 text-gray-800',
        'soumis': 'bg-blue-100 text-blue-800',
        'validé': 'bg-green-100 text-green-800',
    };
    return badges[status] || 'bg-gray-100 text-gray-800';
};

const isFormDisabled = computed(() => {
    return props.timesheet.status === 'validé' || page.props.auth.user.role.name === 'tc';
});

const canValidate = computed(() => {
    return props.timesheet.status === 'soumis' && page.props.auth.user.role.name === 'cp';
});

const hasPlanning = computed(() => {
    return props.planningAssignment !== null;
});

const weekDays = computed(() => {
    // Utiliser la même logique que initializeEntries pour garantir la cohérence
    const start = new Date(props.startDate);
    const dayOfWeek = start.getDay();
    const mondayOffset = dayOfWeek === 0 ? -6 : 1 - dayOfWeek;
    const monday = new Date(start);
    monday.setDate(start.getDate() + mondayOffset);
    
    const days = [];
    const dayNames = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
    
    for (let i = 0; i < 7; i++) {
        const date = new Date(monday);
        date.setDate(monday.getDate() + i);
        const dateStr = date.toISOString().split('T')[0];
        
        // S'assurer que l'entrée existe dans weekEntries
        if (!weekEntries.value[dateStr]) {
            weekEntries.value[dateStr] = {
                check_in: '',
                check_out: '',
                break_duration: 0,
                planned_hours: props.planningHours[dateStr] || 0,
                total_hours: 0,
                overtime_hours: 0,
                absence_type: '',
                comment: '',
            };
        }
        
        days.push({
            date: dateStr,
            name: dayNames[i],
            dayNumber: date.getDate(),
        });
    }
    return days;
});
</script>

<template>
    <Head :title="`Feuille de temps - ${employee.first_name} ${employee.last_name}`" />

    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div class="flex items-center space-x-4">
                        <Link href="/timesheets" :="{ week }" class="text-gray-600 hover:text-gray-900">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </Link>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">
                                <span v-if="props.isBulkEntry">
                                    Mode Saisie Groupée : {{ props.employees.length }} employés sélectionnés
                                </span>
                                <span v-else>
                                    Feuille de temps - {{ employee.first_name }} {{ employee.last_name }}
                                </span>
                            </h1>
                            <p class="text-sm text-gray-600 mt-1">
                                <span v-if="props.isBulkEntry">
                                    Saisie groupée pour la semaine du {{ new Date(startDate).toLocaleDateString('fr-FR') }} au {{ new Date(endDate).toLocaleDateString('fr-FR') }}
                                </span>
                                <span v-else>
                                    {{ employee.matricule }} • {{ employee.position?.name || 'Non défini' }} • 
                                    Semaine du {{ new Date(startDate).toLocaleDateString('fr-FR') }} au {{ new Date(endDate).toLocaleDateString('fr-FR') }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div>
                        <span :class="'px-4 py-2 text-sm font-medium rounded-full ' + getStatusBadge(timesheet.status)">
                            {{ timesheet.status.charAt(0).toUpperCase() + timesheet.status.slice(1) }}
                        </span>
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

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <!-- Planning de référence -->
            <div v-if="hasPlanning" class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-blue-900">Planning de référence</h3>
                    <span class="text-sm text-blue-600">{{ planningAssignment.planning_model.name }}</span>
                </div>
                <div class="grid grid-cols-7 gap-4">
                    <div v-for="(hours, date) in planningHours" :key="date" class="text-center">
                        <div class="font-medium text-blue-800 text-sm">{{ getDayName(new Date(date)) }}</div>
                        <div class="text-2xl font-bold text-blue-600 mt-1">{{ hours }}h</div>
                    </div>
                </div>
            </div>
            
            <div v-else class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    <div>
                        <p class="text-yellow-800 font-medium">Aucun planning validé trouvé</p>
                        <p class="text-yellow-600 text-sm mt-1">La saisie des heures n'est pas possible sans planning de référence.</p>
                    </div>
                </div>
            </div>

            <!-- Formulaire de saisie -->
            <form @submit.prevent="submit('draft')">
                <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Jour
                                    </th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Arrivée
                                    </th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Départ
                                    </th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Pause
                                    </th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Prévues
                                    </th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Réelles
                                    </th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Écart
                                    </th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Absence
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Commentaire
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
    <tr v-for="day in weekDays" :key="day.date" class="hover:bg-gray-50">
        <td class="px-6 py-4 whitespace-nowrap">
            <div class="text-sm font-medium text-gray-900">
                {{ day.name }}
            </div>
            <div class="text-xs text-gray-500">
                {{ new Date(day.date).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' }) }}
            </div>
        </td>
        <td class="px-4 py-4 whitespace-nowrap text-center">
            <input type="time" 
                   v-model="weekEntries[day.date].check_in"
                   @change="calculateHours(day.date)"
                   :disabled="isFormDisabled"
                   class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed">
        </td>
        <td class="px-4 py-4 whitespace-nowrap text-center">
            <input type="time" 
                   v-model="weekEntries[day.date].check_out"
                   @change="calculateHours(day.date)"
                   :disabled="isFormDisabled"
                   class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed">
        </td>
        <td class="px-4 py-4 whitespace-nowrap text-center">
            <input type="number" 
                   v-model="weekEntries[day.date].break_duration"
                   @change="calculateHours(day.date)"
                   :disabled="isFormDisabled"
                   min="0"
                   class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed">
        </td>
        <td class="px-4 py-4 whitespace-nowrap text-center">
            <span class="text-sm font-medium text-gray-600">
                {{ formatDisplayHours(weekEntries[day.date]?.planned_hours) }}
            </span>
        </td>
        <td class="px-4 py-4 whitespace-nowrap text-center">
            <span class="text-sm font-medium text-gray-900">
                {{ formatDisplayHours(weekEntries[day.date]?.total_hours) }}
            </span>
        </td>
        <td class="px-4 py-4 whitespace-nowrap text-center">
            <span :class="'text-sm font-medium ' + ((weekEntries[day.date]?.overtime_hours || 0) < 0 ? 'text-red-600' : (weekEntries[day.date]?.overtime_hours || 0) > 0 ? 'text-green-600' : 'text-gray-600')">
                {{ formatDisplayHours(weekEntries[day.date]?.overtime_hours) }}
            </span>
        </td>
        <td class="px-4 py-4 whitespace-nowrap text-center">
            <select v-model="weekEntries[day.date].absence_type" 
                    :disabled="isFormDisabled"
                    class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed">
                <option value="">-</option>
                <option value="maladie">Maladie</option>
                <option value="congé">Congé</option>
                <option value="RTT">RTT</option>
            </select>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <input type="text" 
                   v-model="weekEntries[day.date].comment"
                   :disabled="isFormDisabled"
                   placeholder="Commentaire..."
                   class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed">
        </td>
    </tr>
</tbody>
                            <tfoot class="bg-gray-50 border-t">
    <tr>
        <td colspan="4" class="px-6 py-4 text-right font-medium text-gray-900">
            Total semaine :
        </td>
        <td class="px-4 py-4 text-right font-medium text-gray-600">
            {{ formatDisplayHours(weekPlannedTotal) }}
        </td>
        <td class="px-4 py-4 text-right font-medium text-gray-900">
            {{ formatDisplayHours(weekTotal) }}
        </td>
        <td class="px-4 py-4 text-right font-medium" 
            :class="weekOvertime > 0 ? 'text-green-600' : (weekOvertime < 0 ? 'text-red-600' : 'text-gray-600')">
            {{ formatDisplayHours(weekOvertime) }}
        </td>
        <td colspan="2"></td>
    </tr>
</tfoot>
                        </table>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="flex justify-between items-center mt-6">
    <div>
        <Link href="/timesheets" :data="{ week }" class="text-gray-600 hover:text-gray-900 inline-flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Retour à la liste
        </Link>
    </div>
    
    <div v-if="timesheet.status === 'brouillon' && hasPlanning" class="flex space-x-4">
        <button type="button"
                @click="submit('draft')"
                :disabled="form.processing"
                class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors disabled:opacity-50">
            <span v-if="props.isBulkEntry">Enregistrer pour tous</span>
            <span v-else>Enregistrer le brouillon</span>
        </button>
        <button type="button"
                @click="submit('submit')"
                :disabled="form.processing"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50">
            <span v-if="props.isBulkEntry">Soumettre pour tous</span>
            <span v-else>Soumettre pour validation</span>
        </button>
    </div>

    <div v-else-if="timesheet.status === 'soumis' && page.props.auth.user.role.name !== 'cp'" 
         class="bg-blue-50 border border-blue-200 text-blue-700 px-6 py-3 rounded-xl font-medium shadow-sm">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            En attente de validation par le Chef de Plateau
        </div>
    </div>
    
    <div v-else-if="canValidate">
        <button type="button"
                @click="validateTimesheet"
                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors shadow-lg">
            Valider la feuille de temps
        </button>
    </div>
    
</div>
            </form>
        </div>
    </div>
</template>
