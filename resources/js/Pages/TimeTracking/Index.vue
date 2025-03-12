<script setup>
import { ref, computed, watch, onMounted, nextTick } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { format, parseISO, subMonths } from 'date-fns';
import { fr } from 'date-fns/locale';
import { useToast } from 'vue-toastification';
import { PencilIcon, TrashIcon, ClockIcon, ChartBarIcon, DocumentTextIcon, CalendarIcon } from '@heroicons/vue/24/outline';
import { Switch, Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';
import { Chart, registerables } from 'chart.js';

const props = defineProps({
    assignedTickets: Array,
    allTickets: Array,
    recentTimeEntries: Array,
    showAllTickets: Boolean,
    showArchived: Boolean,
    chartData: Array,
    statistics: Object,
});

const toast = useToast();
const showAllTicketsToggle = ref(props.showAllTickets);
const showArchivedToggle = ref(props.showArchived);
const selectedTicket = ref(null);
const showTimeEntryModal = ref(false);
const showPdfReportModal = ref(false);
const editingTimeEntry = ref(null);
const today = new Date().toISOString().split('T')[0];

// Formulaire pour le rapport PDF
const pdfReportForm = useForm({
    start_date: format(subMonths(new Date(), 1), 'yyyy-MM-dd'),
    end_date: format(new Date(), 'yyyy-MM-dd'),
});

// Formulaire pour l'ajout d'une entrée de temps
const form = useForm({
    ticket_id: '',
    entry_date: today,
    hours: '',
    minutes: '',
    minutes_spent: 0,
    description: '',
    billable: true,
});

// Formulaire pour la modification d'une entrée de temps
const editForm = useForm({
    entry_date: '',
    hours: '',
    minutes: '',
    minutes_spent: 0,
    description: '',
    billable: true,
});

// Cette définition est déplacée plus bas pour être après la définition des watches

// Construire l'URL avec les paramètres de filtrage
function buildFilterUrl() {
    const params = [];
    if (showAllTicketsToggle.value) params.push('show_all_tickets=1');
    if (showArchivedToggle.value) params.push('show_archived=1');
    return `/time-tracking${params.length ? '?' + params.join('&') : ''}`;
}

// Surveiller le changement des toggles pour recharger la page
watch(showAllTicketsToggle, () => {
    window.location.href = buildFilterUrl();
});

watch(showArchivedToggle, () => {
    window.location.href = buildFilterUrl();
});

// Calculer les tickets affichés en fonction des filtres
const displayedTickets = computed(() => {
    let tickets = showAllTicketsToggle.value && props.allTickets ? props.allTickets : props.assignedTickets;
    return tickets || [];
});

// Ouvrir le modal d'ajout de temps
function openAddTimeModal(ticket = null) {
    // Vérifier si le ticket est archivé
    if (ticket && ticket.archived) {
        toast.error('Impossible d\'ajouter du temps sur un ticket archivé');
        return;
    }
    
    selectedTicket.value = ticket;
    form.ticket_id = ticket ? ticket.id : '';
    form.entry_date = today;
    form.hours = '';
    form.minutes = '';
    form.description = '';
    form.billable = true;
    showTimeEntryModal.value = true;
    editingTimeEntry.value = null;
}

// Ouvrir le modal de modification de temps
function openEditTimeModal(timeEntry) {
    editingTimeEntry.value = timeEntry;
    
    // Convertir les minutes en heures et minutes
    const totalMinutes = timeEntry.minutes_spent;
    const hours = Math.floor(totalMinutes / 60);
    const minutes = totalMinutes % 60;
    
    editForm.entry_date = timeEntry.entry_date;
    editForm.hours = hours > 0 ? hours : '';
    editForm.minutes = minutes > 0 ? minutes : '';
    editForm.description = timeEntry.description || '';
    editForm.billable = timeEntry.billable;
    
    showTimeEntryModal.value = true;
}

// Fermer le modal
function closeModal() {
    showTimeEntryModal.value = false;
    selectedTicket.value = null;
    editingTimeEntry.value = null;
}

// Soumettre le formulaire d'ajout de temps
function submitTimeEntry() {
    // Vérifier que le ticket n'est pas archivé
    const selectedTicket = displayedTickets.value.find(t => t.id === form.ticket_id);
    if (selectedTicket && selectedTicket.archived) {
        toast.error('Impossible d\'ajouter du temps sur un ticket archivé');
        return;
    }
    
    // Vérification supplémentaire pour les tickets archivés qui pourraient ne pas être dans displayedTickets
    if (props.allTickets) {
        const archivedTicket = props.allTickets.find(t => t.id === form.ticket_id && t.archived);
        if (archivedTicket) {
            toast.error('Impossible d\'ajouter du temps sur un ticket archivé');
            return;
        }
    }
    
    // Calculer le nombre total de minutes
    const hours = parseInt(form.hours || 0);
    const minutes = parseInt(form.minutes || 0);
    const totalMinutes = (hours * 60) + minutes;
    
    if (totalMinutes <= 0) {
        toast.error('Veuillez entrer une durée valide');
        return;
    }
    
    // Assigner les valeurs directement au formulaire
    form.ticket_id = form.ticket_id;
    form.entry_date = form.entry_date;
    form.minutes_spent = totalMinutes;
    form.description = form.description;
    form.billable = form.billable;
    
    form.post(route('time-entries.store'), {
        onSuccess: () => {
            toast.success('Temps enregistré avec succès');
            closeModal();
            // Recharger la page pour afficher les nouvelles données
            window.location.reload();
        },
        onError: (errors) => {
            toast.error(Object.values(errors).join('\n'));
        }
    });
}

// Soumettre le formulaire de modification de temps
function submitEditTimeEntry() {
    // Calculer le nombre total de minutes
    const hours = parseInt(editForm.hours || 0);
    const minutes = parseInt(editForm.minutes || 0);
    const totalMinutes = (hours * 60) + minutes;
    
    if (totalMinutes <= 0) {
        toast.error('Veuillez entrer une durée valide');
        return;
    }
    
    // Assigner les valeurs directement au formulaire
    editForm.entry_date = editForm.entry_date;
    editForm.minutes_spent = totalMinutes;
    editForm.description = editForm.description;
    editForm.billable = editForm.billable;
    
    editForm.put(route('time-entries.update', editingTimeEntry.value.id), {
        onSuccess: () => {
            toast.success('Entrée de temps mise à jour avec succès');
            closeModal();
            // Recharger la page pour afficher les nouvelles données
            window.location.reload();
        },
        onError: (errors) => {
            toast.error(Object.values(errors).join('\n'));
        }
    });
}

// Supprimer une entrée de temps
function deleteTimeEntry(timeEntry) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette entrée de temps ?')) {
        useForm().delete(route('time-entries.destroy', timeEntry.id), {
            onSuccess: () => {
                toast.success('Entrée de temps supprimée avec succès');
                // Recharger la page pour afficher les nouvelles données
                window.location.reload();
            },
            onError: (errors) => {
                toast.error(Object.values(errors).join('\n'));
            }
        });
    }
}

// Formater la durée en heures et minutes
function formatDuration(minutes) {
    const hours = Math.floor(minutes / 60);
    const remainingMinutes = minutes % 60;
    
    if (hours > 0) {
        return `${hours}h${remainingMinutes > 0 ? remainingMinutes : ''}`;
    }
    
    return `${remainingMinutes}min`;
}

// Formater la date
function formatDate(dateString) {
    return format(parseISO(dateString), 'dd MMMM yyyy', { locale: fr });
}

// Ouvrir la modale de génération de rapport PDF
function openPdfReportModal() {
    // Définir les dates par défaut (dernier mois jusqu'à aujourd'hui)
    pdfReportForm.start_date = format(subMonths(new Date(), 1), 'yyyy-MM-dd');
    pdfReportForm.end_date = format(new Date(), 'yyyy-MM-dd');
    showPdfReportModal.value = true;
}

// Générer le rapport PDF
function generatePdfReport() {
    // Créer l'URL avec les paramètres
    const url = new URL(route('time-entries.pdf-report'));
    url.searchParams.append('start_date', pdfReportForm.start_date);
    url.searchParams.append('end_date', pdfReportForm.end_date);
    
    // Ouvrir l'URL dans un nouvel onglet pour télécharger le PDF
    window.open(url.toString(), '_blank');
    
    // Fermer la modale et afficher un message de succès
    showPdfReportModal.value = false;
    toast.success('Génération du rapport PDF en cours...');
}

// Formater la date pour l'affichage dans le graphique
function formatChartDate(dateString) {
    return format(parseISO(dateString), 'dd/MM', { locale: fr });
}

// Référence pour le canvas du graphique
const chartCanvas = ref(null);
// Initialiser le graphique
let timeChart = null;

onMounted(() => {
    // Déboguer les données du graphique
    console.log('Chart Data:', props.chartData);
    console.log('Statistics:', props.statistics);
    
    // Enregistrer les composants Chart.js
    Chart.register(...registerables);
    
    // Attendre que le DOM soit complètement chargé
    nextTick(() => {
        // Attendre un peu plus pour s'assurer que le canvas est rendu
        setTimeout(() => {
            initChart();
        }, 100);
    });
});

function initChart() {
    console.log('Initializing chart...');
    
    // Vérifier si la référence du canvas existe
    if (!chartCanvas.value) {
        console.error('Canvas reference not found');
        return;
    }
    
    // Détruire le graphique existant s'il y en a un
    if (timeChart) {
        timeChart.destroy();
    }
    
    // Préparer les données pour le graphique
    const dates = props.chartData.map(item => formatChartDate(item.date));
    const hours = props.chartData.map(item => item.hours);
    
    console.log('Dates for chart:', dates);
    console.log('Hours for chart:', hours);
    
    // Créer des données de test si nécessaire pour le débogage
    if (hours.every(h => h === 0)) {
        console.log('No data available, using test data');
        // Générer des données de test aléatoires
        for (let i = 0; i < 5; i++) {
            const randomIndex = Math.floor(Math.random() * hours.length);
            hours[randomIndex] = Math.random() * 8; // Entre 0 et 8 heures
        }
    }
    
    try {
        timeChart = new Chart(chartCanvas.value, {
            type: 'bar',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Heures travaillées',
                    data: hours,
                    backgroundColor: 'rgba(59, 130, 246, 0.6)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Heures'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.parsed.y} heures`;
                            }
                        }
                    }
                }
            }
        });
        console.log('Chart created successfully:', timeChart);
    } catch (error) {
        console.error('Error creating chart:', error);
    }
}

// Obtenir le nom du ticket
function getTicketName(ticketId) {
    // Chercher d'abord dans les tickets affichés (non archivés ou archivés selon le filtre)
    const ticket = displayedTickets.value.find(t => t.id === ticketId);
    if (ticket) return ticket.title;
    
    // Si pas trouvé, chercher dans tous les tickets (même si on n'affiche pas les archivés)
    // Cette partie est importante pour afficher correctement les tickets archivés dans l'historique
    if (props.allTickets) {
        const archivedTicket = props.allTickets.find(t => t.id === ticketId);
        if (archivedTicket) return `${archivedTicket.title} (Archivé)`;
    }
    
    // Si toujours pas trouvé, essayer de chercher dans les entrées récentes
    // qui pourraient contenir des références aux tickets archivés
    if (props.recentTimeEntries) {
        const timeEntry = props.recentTimeEntries.find(entry => entry.ticket_id === ticketId && entry.ticket);
        if (timeEntry && timeEntry.ticket) {
            return `${timeEntry.ticket.title} (Archivé)`;
        }
    }
    
    return 'Ticket inconnu';
}

// Obtenir la couleur de statut
function getStatusColor(statusId) {
    const statusColors = {
        1: 'bg-yellow-500', // En attente
        2: 'bg-blue-500',   // En cours
        3: 'bg-green-500',  // Résolu
        4: 'bg-gray-500',   // Fermé
        5: 'bg-red-500',    // Annulé
    };
    
    return statusColors[statusId] || 'bg-gray-300';
}
</script>

<template>
    <Head title="Pointage des temps" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Pointage des temps
                </h2>
                <div class="flex items-center">
                    <span class="mr-2 text-sm text-gray-600">Afficher tous les tickets</span>
                    <Switch
                        v-model="showAllTicketsToggle"
                        :class="showAllTicketsToggle ? 'bg-blue-600' : 'bg-gray-200'"
                        class="relative inline-flex h-6 w-11 items-center rounded-full"
                    >
                        <span class="sr-only">Afficher tous les tickets</span>
                        <span
                            :class="showAllTicketsToggle ? 'translate-x-6' : 'translate-x-1'"
                            class="inline-block h-4 w-4 transform rounded-full bg-white transition"
                        />
                    </Switch>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Tickets disponibles pour le pointage</h3>
                    
                    <!-- Liste des tickets -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
                        <div v-for="ticket in displayedTickets" :key="ticket.id" 
                            class="border rounded-lg p-4 hover:shadow-md transition-shadow flex flex-col h-full">
                            
                            <!-- Contenu principal du ticket (ajout de flex-grow pour pousser le bouton vers le bas) -->
                            <div class="flex-grow">
                                <!-- Titre + statut -->
                                <div class="grid grid-cols-[1fr_auto] gap-2 mb-2 items-start">
                                    <h4 class="font-semibold text-blue-600 break-words" :title="ticket.title">
                                        {{ ticket.title }}
                                        <span v-if="ticket.archived" class="text-xs text-gray-500 ml-1">(Archivé)</span>
                                    </h4>
                                    <div class="whitespace-nowrap" :class="`${getStatusColor(ticket.status.id)} text-white text-xs px-3 py-1 rounded-full`">
                                        {{ ticket.status.name }}
                                    </div>
                                </div>

                            </div>
                            <!-- Bas de ticket en deux colonnes -->
                            <div class="mt-auto grid grid-cols-[1fr_auto] gap-2 items-center">
                                <!-- Colonne 1: Infos -->
                                <div class="text-sm text-gray-600">
                                    <p><span class="font-medium">Catégorie:</span> {{ ticket.category.name }}</p>
                                    <p v-if="ticket.equipment"><span class="font-medium">Équipement:</span> {{ ticket.equipment.name }}</p>
                                    <p><span class="font-medium">Temps total:</span> {{ formatDuration(ticket.total_time_spent || 0) }}</p>
                                </div>
                                
                                <!-- Colonne 2: Bouton -->
                                <div>
                                    <button v-if="!ticket.archived" @click="openAddTimeModal(ticket)" 
                                            class="flex items-center text-sm text-white bg-blue-600 hover:bg-blue-700 px-3 py-1 rounded-md whitespace-nowrap">
                                        <ClockIcon class="h-4 w-4 mr-1" />
                                        Ajouter du temps
                                    </button>
                                    <span v-else class="text-xs text-gray-500 italic">
                                        Impossible d'ajouter du temps sur un ticket archivé
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Entrées de temps récentes -->
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Mes entrées de temps récentes</h3>
                        <Link 
                            :href="route('time-entries.list')"
                            class="flex items-center text-sm text-blue-600 hover:text-blue-800 px-3 py-1 border border-blue-300 rounded-md hover:bg-blue-50 transition-colors"
                        >
                            <CalendarIcon class="h-4 w-4 mr-1" />
                            Voir tous les pointages
                        </Link>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durée</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Facturable</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="entry in recentTimeEntries" :key="entry.id">
                                    <td class="px-3 py-2 text-sm text-gray-500">
                                        {{ formatDate(entry.entry_date) }}
                                    </td>
                                    <td class="px-3 py-2 text-sm text-gray-900 max-w-[200px]">
                                        <div class="truncate" :title="getTicketName(entry.ticket_id)">
                                            {{ getTicketName(entry.ticket_id).length > 30 ? getTicketName(entry.ticket_id).substring(0, 30) + '...' : getTicketName(entry.ticket_id) }}
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 text-sm text-gray-500">
                                        {{ formatDuration(entry.minutes_spent) }}
                                    </td>
                                    <td class="px-3 py-2 text-sm text-gray-500 max-w-[200px]">
                                        <div class="truncate" :title="entry.description || '-'">
                                            {{ entry.description ? (entry.description.length > 30 ? entry.description.substring(0, 30) + '...' : entry.description) : '-' }}
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 text-sm text-gray-500">
                                        <span v-if="entry.billable" class="text-green-600">Oui</span>
                                        <span v-else class="text-red-600">Non</span>
                                    </td>
                                    <td class="px-3 py-2 text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button @click="openEditTimeModal(entry)" class="text-blue-600 hover:text-blue-900">
                                                <PencilIcon class="h-4 w-4" />
                                            </button>
                                            <button @click="deleteTimeEntry(entry)" class="text-red-600 hover:text-red-900">
                                                <TrashIcon class="h-4 w-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="recentTimeEntries.length === 0">
                                    <td colspan="6" class="px-3 py-2 text-center text-sm text-gray-500">
                                        Aucune entrée de temps récente
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Statistiques et graphique -->
                    <div class="mt-8">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold flex items-center">
                                <ChartBarIcon class="h-5 w-5 mr-2" />
                                Statistiques de temps
                            </h3>
                            <button 
                                @click="openPdfReportModal"
                                class="flex items-center text-sm text-white bg-blue-600 hover:bg-blue-700 px-3 py-1 rounded-md"
                            >
                                <DocumentTextIcon class="h-4 w-4 mr-1" />
                                Générer un rapport PDF
                            </button>
                        </div>
                        
                        <!-- Cartes de statistiques -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <!-- Aujourd'hui -->
                            <div class="bg-white p-4 rounded-lg shadow">
                                <h4 class="text-sm font-medium text-gray-500 mb-1">Aujourd'hui</h4>
                                <p class="text-2xl font-bold text-blue-600">{{ statistics.today.hours }} h</p>
                                <p class="text-xs text-gray-500">{{ statistics.today.minutes }} minutes</p>
                            </div>
                            
                            <!-- Cette semaine -->
                            <div class="bg-white p-4 rounded-lg shadow">
                                <h4 class="text-sm font-medium text-gray-500 mb-1">Cette semaine</h4>
                                <p class="text-2xl font-bold text-blue-600">{{ statistics.week.hours }} h</p>
                                <p class="text-xs text-gray-500">{{ statistics.week.minutes }} minutes</p>
                            </div>
                            
                            <!-- Ce mois -->
                            <div class="bg-white p-4 rounded-lg shadow">
                                <h4 class="text-sm font-medium text-gray-500 mb-1">Ce mois</h4>
                                <p class="text-2xl font-bold text-blue-600">{{ statistics.month.hours }} h</p>
                                <p class="text-xs text-gray-500">{{ statistics.month.minutes }} minutes</p>
                            </div>
                        </div>
                        
                        <!-- Graphique -->
                        <div class="bg-white p-4 rounded-lg shadow">
                            <h4 class="text-sm font-medium text-gray-500 mb-3">Heures travaillées (30 derniers jours)</h4>
                            <div class="h-64">
                                <canvas ref="chartCanvas"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal d'ajout/modification de temps -->
        <div v-if="showTimeEntryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">

                <h3 class="text-lg font-semibold mb-4">
                    {{ editingTimeEntry ? 'Modifier l\'entrée de temps' : 'Ajouter du temps' }}
                </h3>
                
                <form @submit.prevent="editingTimeEntry ? submitEditTimeEntry() : submitTimeEntry()">
                    <!-- Ticket (seulement pour l'ajout) -->
                    <div v-if="!editingTimeEntry" class="mb-4">
                        <label for="ticket_id" class="block text-sm font-medium text-gray-700 mb-1">Ticket</label>
                        <select
                            id="ticket_id"
                            v-model="form.ticket_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required
                        >
                            <option value="" disabled>Sélectionnez un ticket</option>
                            <option v-for="ticket in displayedTickets.filter(t => !t.archived)" :key="ticket.id" :value="ticket.id">
                                {{ ticket.title }}
                            </option>
                        </select>
                    </div>
                    
                    <!-- Date -->
                    <div class="mb-4">
                        <label for="entry_date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                        <template v-if="editingTimeEntry">
                            <input
                                id="entry_date"
                                type="date"
                                v-model="editForm.entry_date"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required
                            />
                        </template>
                        <template v-else>
                            <input
                                id="entry_date"
                                type="date"
                                v-model="form.entry_date"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required
                            />
                        </template>
                    </div>
                    
                    <!-- Durée -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Durée</label>
                        <div class="flex space-x-2">
                            <template v-if="editingTimeEntry">
                                <div class="w-1/2">
                                    <input
                                        type="number"
                                        v-model="editForm.hours"
                                        placeholder="Heures"
                                        min="0"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                </div>
                                <div class="w-1/2">
                                    <input
                                        type="number"
                                        v-model="editForm.minutes"
                                        placeholder="Minutes"
                                        min="0"
                                        max="59"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                </div>
                            </template>
                            <template v-else>
                                <div class="w-1/2">
                                    <input
                                        type="number"
                                        v-model="form.hours"
                                        placeholder="Heures"
                                        min="0"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                </div>
                                <div class="w-1/2">
                                    <input
                                        type="number"
                                        v-model="form.minutes"
                                        placeholder="Minutes"
                                        min="0"
                                        max="59"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                </div>
                            </template>
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <template v-if="editingTimeEntry">
                            <textarea
                                id="description"
                                v-model="editForm.description"
                                rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            ></textarea>
                        </template>
                        <template v-else>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            ></textarea>
                        </template>
                    </div>
                    
                    <!-- Facturable -->
                    <div class="mb-6 flex items-center">
                        <template v-if="editingTimeEntry">
                            <Switch
                                v-model="editForm.billable"
                                :class="editForm.billable ? 'bg-blue-600' : 'bg-gray-200'"
                                class="relative inline-flex h-6 w-11 items-center rounded-full mr-3"
                            >
                                <span class="sr-only">Facturable</span>
                                <span
                                    :class="editForm.billable ? 'translate-x-6' : 'translate-x-1'"
                                    class="inline-block h-4 w-4 transform rounded-full bg-white transition"
                                />
                            </Switch>
                        </template>
                        <template v-else>
                            <Switch
                                v-model="form.billable"
                                :class="form.billable ? 'bg-blue-600' : 'bg-gray-200'"
                                class="relative inline-flex h-6 w-11 items-center rounded-full mr-3"
                            >
                                <span class="sr-only">Facturable</span>
                                <span
                                    :class="form.billable ? 'translate-x-6' : 'translate-x-1'"
                                    class="inline-block h-4 w-4 transform rounded-full bg-white transition"
                                />
                            </Switch>
                        </template>
                        <label class="text-sm font-medium text-gray-700">Facturable</label>
                    </div>
                    
                    <!-- Boutons -->
                    <div class="flex justify-end space-x-3">
                        <button
                            type="button"
                            @click="closeModal"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md"
                        >
                            Annuler
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md"
                            :disabled="editingTimeEntry ? editForm.processing : form.processing"
                        >
                            {{ editingTimeEntry ? 'Mettre à jour' : 'Enregistrer' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Modal de génération de rapport PDF -->
        <TransitionRoot appear :show="showPdfReportModal" as="template">
            <Dialog as="div" @close="showPdfReportModal = false" class="relative z-50">
                <TransitionChild
                    as="template"
                    enter="duration-300 ease-out"
                    enter-from="opacity-0"
                    enter-to="opacity-100"
                    leave="duration-200 ease-in"
                    leave-from="opacity-100"
                    leave-to="opacity-0"
                >
                    <div class="fixed inset-0 bg-black bg-opacity-50" />
                </TransitionChild>

                <div class="fixed inset-0 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center">
                        <TransitionChild
                            as="template"
                            enter="duration-300 ease-out"
                            enter-from="opacity-0 scale-95"
                            enter-to="opacity-100 scale-100"
                            leave="duration-200 ease-in"
                            leave-from="opacity-100 scale-100"
                            leave-to="opacity-0 scale-95"
                        >
                            <DialogPanel class="w-full max-w-md transform overflow-hidden rounded-lg bg-white p-6 text-left align-middle shadow-xl transition-all">
                                <DialogTitle as="h3" class="text-lg font-semibold leading-6 text-gray-900 mb-4">
                                    Générer un rapport PDF
                                </DialogTitle>
                                
                                <form @submit.prevent="generatePdfReport">
                                    <div class="mb-4">
                                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                                        <input
                                            id="start_date"
                                            v-model="pdfReportForm.start_date"
                                            type="date"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            required
                                        />
                                    </div>
                                    
                                    <div class="mb-6">
                                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
                                        <input
                                            id="end_date"
                                            v-model="pdfReportForm.end_date"
                                            type="date"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            required
                                        />
                                    </div>
                                    
                                    <div class="flex justify-end space-x-3">
                                        <button
                                            type="button"
                                            @click="showPdfReportModal = false"
                                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md"
                                        >
                                            Annuler
                                        </button>
                                        <button
                                            type="submit"
                                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md"
                                        >
                                            Générer
                                        </button>
                                    </div>
                                </form>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </AuthenticatedLayout>
</template>
