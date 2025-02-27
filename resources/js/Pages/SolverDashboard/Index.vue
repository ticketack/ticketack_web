<template>
    <Head :title="$page.props.translations.solver.title" />
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $page.props.translations.solver.title }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Stats -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4">{{ $page.props.translations.solver.assigned_tickets }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-indigo-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">{{ $page.props.translations.solver.total_time }}</p>
                            <p class="text-2xl font-bold text-indigo-600">{{ formatDuration(stats.totalEstimatedTime) }}</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">{{ $page.props.translations.solver.tickets_count }}</p>
                            <p class="text-2xl font-bold text-green-600">{{ assignedTickets.length }}</p>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">{{ $page.props.translations.solver.average_time }}</p>
                            <p class="text-2xl font-bold text-purple-600">{{ formatDuration(averageTime) }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex gap-6">
                    <!-- Calendrier -->
                    <div class="flex-1 bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <div ref="calendarEl" class="planning-calendar"></div>
                        </div>
                    </div>

                    <!-- Liste des tickets draggables -->
                    <div id="external-events" class="w-96 bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">{{ $page.props.translations.solver.assigned_tickets }}</h3>
                            <div class="space-y-4">
                                <div v-for="ticket in assignedTickets" 
                                    :key="ticket.id"
                                    class="bg-white border rounded-lg p-4 cursor-move shadow-sm hover:shadow-md transition-shadow external-event"
                                    :data-ticket="JSON.stringify(ticket)"
                                >
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-gray-600">#{{ ticket.id }}</span>
                                        <span :class="getPriorityClass(ticket.priority)">
                                            {{ typeof ticket.priority === 'object' ? ticket.priority.name : ticket.priority }}
                                        </span>
                                    </div>
                                    <Link :href="`/tickets/${ticket.id}`" class="text-indigo-600 hover:text-indigo-900 font-medium block mb-2">
                                        {{ ticket.title }}
                                    </Link>
                                    <div class="flex items-center justify-between">
                                        <span :class="getStatusClass(ticket.status)">
                                            {{ ticket.status?.name || 'Nouveau' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de planification -->
        <Modal :show="scheduleModalOpen" @close="closeScheduleModal">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    {{ $page.props.translations.solver.schedule.title }}
                </h3>
                <form @submit.prevent="submitSchedule" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            {{ $page.props.translations.solver.schedule.start_date }}
                        </label>
                        <input
                            type="datetime-local"
                            v-model="scheduleForm.start_at"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            {{ $page.props.translations.solver.schedule.duration }}
                        </label>
                        <input
                            type="number"
                            v-model="scheduleForm.estimated_duration"
                            min="1"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            {{ $page.props.translations.solver.schedule.comments }}
                        </label>
                        <textarea
                            v-model="scheduleForm.comments"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        ></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button
                            type="button"
                            @click="closeScheduleModal"
                            class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            {{ $page.props.translations.solver.schedule.cancel }}
                        </button>
                        <button
                            type="submit"
                            class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            {{ $page.props.translations.solver.schedule.save }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AuthenticatedLayout.vue'
import Modal from '@/Components/Modal.vue'
import { Calendar } from '@fullcalendar/core'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import listPlugin from '@fullcalendar/list'
import interactionPlugin, { Draggable } from '@fullcalendar/interaction'
import { format } from 'date-fns'
import { fr } from 'date-fns/locale'
import frLocale from '@fullcalendar/core/locales/fr'
import axios from 'axios'

import { usePage, Link } from '@inertiajs/vue3'

const page = usePage()
const props = defineProps({
    assignedTickets: Array,
    schedules: Array,
    stats: Object,
})

const scheduleModalOpen = ref(false)
const selectedTicket = ref(null)
const scheduleForm = ref({
    start_at: '',
    estimated_duration: 60,
    comments: ''
})
const draggedTicket = ref(null)

const averageTime = computed(() => {
    if (props.assignedTickets.length === 0) return 0;
    return Math.round(props.stats.totalEstimatedTime / props.assignedTickets.length);
});

// Référence à l'élément du calendrier
const calendarEl = ref(null);

// Référence au calendrier
let calendar = null;

// Initialiser le drag & drop
onMounted(() => {
    // Initialiser le calendrier
    calendar = new Calendar(calendarEl.value, calendarOptions);
    calendar.render();

    // Initialiser les éléments draggables
    new Draggable(document.getElementById('external-events'), {
        itemSelector: '.external-event',
        eventData: function(eventEl) {
            const ticket = JSON.parse(eventEl.dataset.ticket);
            return {
                title: `#${ticket.id} - ${ticket.title} (${ticket.status?.name || 'Nouveau'})`,
                duration: '01:00',
                backgroundColor: getPriorityColor(ticket.priority),
                borderColor: getPriorityColor(ticket.priority),
                textColor: '#000000',
                extendedProps: {
                    ticket: ticket
                }
            };
        }
    });
});

// Options du calendrier
const calendarOptions = {
    plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
    editable: true,
    dragRevertDuration: 0,
    eventOverlap: false,
    droppable: true,
    defaultTimedEventDuration: '01:00:00',
    dropAccept: '.external-event',
    dragScroll: true,
    forceEventDuration: true,
    eventReceive: (info) => {
        console.log('Event received:', info);
        const ticket = info.event.extendedProps.ticket;
        
        // Créer le schedule dans la base de données
        const scheduleData = {
            ticket_id: ticket.id,
            start_at: format(info.event.start, 'yyyy-MM-dd HH:mm:ss'),
            estimated_duration: 60,
            comments: ''
        };
        
        axios.post('/schedules', scheduleData)
            .then(response => {
                console.log('Schedule created:', response.data);
                // Mettre à jour l'ID de l'événement avec l'ID du schedule
                info.event.setProp('id', response.data.id);
                info.event.setExtendedProp('ticket', {
                    ...ticket,
                    schedule_id: response.data.id
                });
            })
            .catch(error => {
                console.error('Error creating schedule:', error);
                if (error.response) {
                    console.error('Server response:', error.response.data);
                }
                // Supprimer l'événement en cas d'erreur
                info.event.remove();
            });
    },
    drop: (info) => {
        console.log('=== DROP EVENT FROM CALENDAR ===');
        console.log('Drop info:', {
            date: info.date,
            draggedEl: info.draggedEl?.className,
            jsEvent: info.jsEvent?.type,
            resource: info.resource,
            allDay: info.allDay
        });
    },
    eventDragStart: (info) => {
        console.log('Event drag start:', info);
    },
    eventDragStop: (info) => {
        console.log('Event drag stop:', info);
    },
    initialView: 'timeGridWeek',
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
    },
    locale: frLocale,
    height: '800px',
    allDaySlot: false,
    slotMinTime: '00:00:00',
    slotMaxTime: '24:00:00',
    scrollTime: '08:00:00',
    businessHours: [
        {
            daysOfWeek: [1, 2, 3, 4, 5],
            startTime: '09:00',
            endTime: '12:00'
        },
        {
            daysOfWeek: [1, 2, 3, 4, 5],
            startTime: '14:00',
            endTime: '18:00'
        }
    ],
    selectable: true,
    nowIndicator: true,
    initialDate: new Date(),
    eventClick: (info) => {
        const ticketId = info.event.extendedProps?.ticket?.id
        if (ticketId) {
            window.location.href = `/tickets/${ticketId}`
        }
    },

    eventSources: [
        {
            events: props.schedules.map(schedule => ({
                id: schedule.id,
                title: schedule.ticket ? `#${schedule.ticket.id} - ${schedule.ticket.title} (${schedule.ticket.status.name})` : 'No ticket info',
                start: schedule.start_at,
                end: new Date(new Date(schedule.start_at).getTime() + schedule.estimated_duration * 60000).toISOString(),
                backgroundColor: getPriorityColor(schedule.ticket?.priority),
                borderColor: getPriorityColor(schedule.ticket?.priority),
                textColor: '#000000',
                allDay: false,
                extendedProps: {
                    ticket: schedule.ticket
                }
            }))
        }
    ],
    eventDrop: async (info) => {
        try {
            console.log('Event dropped:', info);
            // Calculer la durée en minutes entre le début et la fin
            const start = info.event.start;
            const end = info.event.end || new Date(start.getTime() + 60 * 60 * 1000);
            const durationInMinutes = Math.round((end - start) / (1000 * 60));

            await axios.put(`/schedules/${info.event.id}`, {
                start_at: format(start, 'yyyy-MM-dd HH:mm:ss'),
                estimated_duration: durationInMinutes
            });
        } catch (error) {
            console.error('Error updating schedule:', error);
            info.revert();
        }
    }
}

function formatDuration(minutes) {
    const hours = Math.floor(minutes / 60)
    const remainingMinutes = minutes % 60
    return `${hours}h${remainingMinutes.toString().padStart(2, '0')}`
}

function getStatusClass(status) {
    if (!status) return 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800'
    
    const baseClasses = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full'
    
    if (status.is_closed) {
        return `${baseClasses} bg-blue-100 text-blue-800`
    }
    
    // Utiliser les couleurs basées sur le nom du statut pour les statuts non fermés
    const colorClasses = {
        'new': 'bg-gray-100 text-gray-800',
        'in_progress': 'bg-yellow-100 text-yellow-800',
        'waiting': 'bg-orange-100 text-orange-800'
    }
    
    // Essayer de déterminer le type de statut à partir du nom
    let statusKey = 'new';
    if (status.name.toLowerCase().includes('cours')) {
        statusKey = 'in_progress';
    } else if (status.name.toLowerCase().includes('attente')) {
        statusKey = 'waiting';
    }
    
    return `${baseClasses} ${colorClasses[statusKey] || colorClasses['new']}`
}

function getPriorityClass(priority) {
    const baseClasses = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full'
    const colorClasses = {
        'low': 'bg-green-100 text-green-800',
        'medium': 'bg-yellow-100 text-yellow-800',
        'high': 'bg-red-100 text-red-800'
    }
    
    // Déterminer la priorité à partir de l'objet ou de la chaîne
    let priorityKey = 'medium';
    
    if (typeof priority === 'object') {
        // Si c'est un objet, essayer d'utiliser le nom pour déterminer la priorité
        const name = (priority.name || '').toLowerCase();
        if (name.includes('faible') || name.includes('basse')) {
            priorityKey = 'low';
        } else if (name.includes('haute') || name.includes('élevée') || name.includes('urgent')) {
            priorityKey = 'high';
        }
    } else if (typeof priority === 'string') {
        // Si c'est une chaîne, essayer de déterminer la priorité directement
        const p = priority.toLowerCase();
        if (p.includes('faible') || p.includes('basse') || p === 'low') {
            priorityKey = 'low';
        } else if (p.includes('haute') || p.includes('élevée') || p.includes('urgent') || p === 'high') {
            priorityKey = 'high';
        }
    }
    
    return `${baseClasses} ${colorClasses[priorityKey] || colorClasses['medium']}`
}

function getPriorityColor(priority) {
    if (!priority) return '#F4BF76' // Couleur par défaut si pas de priorité
    
    // Déterminer la priorité à partir de l'objet ou de la chaîne
    let priorityKey = 'medium';
    
    if (typeof priority === 'object') {
        // Si c'est un objet, essayer d'utiliser le nom pour déterminer la priorité
        const name = (priority.name || '').toLowerCase();
        if (name.includes('faible') || name.includes('basse')) {
            priorityKey = 'low';
        } else if (name.includes('haute') || name.includes('élevée') || name.includes('urgent')) {
            priorityKey = 'high';
        } else if (name.includes('critique') || name.includes('critical')) {
            priorityKey = 'critical';
        }
    } else if (typeof priority === 'string') {
        // Si c'est une chaîne, essayer de déterminer la priorité directement
        const p = priority.toLowerCase();
        if (p.includes('faible') || p.includes('basse') || p === 'low') {
            priorityKey = 'low';
        } else if (p.includes('haute') || p.includes('élevée') || p.includes('urgent') || p === 'high') {
            priorityKey = 'high';
        } else if (p.includes('critique') || p === 'critical') {
            priorityKey = 'critical';
        }
    }
    
    const colors = {
        'low': '#34D399',      // vert
        'medium': '#F4BF76',    // orange
        'high': '#F87171',      // rouge
        'critical': '#EF4444'   // rouge foncé
    }
    
    return colors[priorityKey] || '#F4BF76'
}

function getStatusColor(status) {
    const colors = {
        'Nouveau': '#6B7280',
        'En cours': '#FCD34D',
        'En attente': '#e67e22',
        'Résolu': '#34D399',
        'Fermé': '#60A5FA'
    }
    return status?.color || colors[status?.name] || '#6B7280'
}

function openScheduleModal(ticket) {
    selectedTicket.value = ticket
    scheduleForm.value = {
        start_at: format(new Date(), 'yyyy-MM-dd\'T\'HH:mm'),
        estimated_duration: 60,
        comments: '',
        ticket_id: ticket.id
    }
    scheduleModalOpen.value = true
}

function closeScheduleModal() {
    scheduleModalOpen.value = false
    selectedTicket.value = null
    scheduleForm.value = {
        start_at: '',
        estimated_duration: 60,
        comments: ''
    }
}

async function submitSchedule(data = null) {
    try {
        const scheduleData = data || {
            ticket_id: selectedTicket.value.id,
            ...scheduleForm.value
        };

        await axios.post('/schedules', scheduleData);

        if (!data) {
            closeScheduleModal();
            // Recharger la page seulement si on vient du modal
            window.location.reload();
        }
    } catch (error) {
        console.error('Error scheduling ticket:', error);
    }
}
</script>

<style>
.planning-calendar {
    height: calc(100vh - 200px);
    min-height: 700px;
    overflow: auto !important;
}

.fc-view-harness {
    height: 1200px !important;
}

/* Mint Theme */
.fc .fc-button-primary {
    background-color: #3eb489 !important;
    border-color: #3eb489 !important;
}

.fc .fc-button-primary:hover {
    background-color: #2d8b6a !important;
    border-color: #2d8b6a !important;
}

.fc .fc-button-primary:not(:disabled):active,
.fc .fc-button-primary:not(:disabled).fc-button-active {
    background-color: #2d8b6a !important;
    border-color: #2d8b6a !important;
}

.fc-col-header-cell {
    background-color: #f0faf6;
}

.fc-timegrid-slot-lane {
    background-color: #ffffff;
}

.fc-timegrid-slot-lane:nth-child(even) {
    background-color: #fafffe;
}

.fc-event {
    cursor: pointer;
    border-radius: 4px;
}

.fc-event:hover {
    opacity: 0.9;
}

.fc-toolbar-title {
    color: #2d8b6a;
}

.fc-day-today {
    background-color: #e6f7f1 !important;
}

.external-event {
    cursor: move;
    transition: opacity 0.3s ease;
}

.external-event:hover {
    opacity: 0.8;
}
</style>
