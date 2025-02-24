<template>
    <Head :title="$page.props.translations.solver.title" />
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $page.props.translations.solver.title }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Stats -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
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

                <!-- Vue Liste/Calendrier -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex">
                            <button
                                @click="currentView = 'list'"
                                :class="[
                                    currentView === 'list'
                                        ? 'border-indigo-500 text-indigo-600'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                    'w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm'
                                ]"
                            >
                                {{ $page.props.translations.solver.assigned_tickets }}
                            </button>
                            <button
                                @click="currentView = 'calendar'"
                                :class="[
                                    currentView === 'calendar'
                                        ? 'border-indigo-500 text-indigo-600'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                    'w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm'
                                ]"
                            >
                                {{ $page.props.translations.solver.calendar }}
                            </button>
                        </nav>
                    </div>

                    <!-- Vue Liste -->
                    <div v-if="currentView === 'list'" class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titre</th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priorité</th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="ticket in assignedTickets" :key="ticket.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">#{{ ticket.id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <Link :href="`/tickets/${ticket.id}`" class="text-indigo-600 hover:text-indigo-900">
                                                {{ ticket.title }}
                                            </Link>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="getStatusClass(ticket.status)">
                                                {{ ticket.status.name }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="getPriorityClass(ticket.priority)">
                                                {{ ticket.priority.name }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <button
                                                @click="openScheduleModal(ticket)"
                                                class="text-indigo-600 hover:text-indigo-900"
                                            >
                                                {{ $page.props.translations.solver.schedule.button }}
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Vue Calendrier -->
                    <div v-else class="p-6">
                        <FullCalendar
                            :options="calendarOptions"
                            class="fc-theme-standard calendar-compact"
                        />
                    </div>

<style>
.calendar-compact .fc-timegrid-slot {
    height: 15px !important;
}

.calendar-compact .fc-timegrid-slot-lane {
    height: 15px !important;
}

.calendar-compact .fc-timegrid-slots tr {
    height: 15px !important;
}

.calendar-compact td.fc-timegrid-slot {
    height: 15px !important;
}
</style>
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
import { ref, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AuthenticatedLayout.vue'
import Modal from '@/Components/Modal.vue'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import { format } from 'date-fns'
import { fr } from 'date-fns/locale'

import { usePage, Link } from '@inertiajs/vue3'

const page = usePage()
const props = defineProps({
    assignedTickets: Array,
    schedules: Array,
    stats: Object,
})

const currentView = ref('list')
const scheduleModalOpen = ref(false)
const selectedTicket = ref(null)
const scheduleForm = ref({
    start_at: '',
    estimated_duration: 60,
    comments: ''
})

const averageTime = computed(() => {
    if (props.assignedTickets.length === 0) return 0
    return Math.round(props.stats.totalEstimatedTime / props.assignedTickets.length)
})

const calendarOptions = {
    plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
    initialView: 'timeGridWeek',
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'timeGridWeek,dayGridMonth'
    },
    locale: 'fr',
    buttonText: {
        today: page.props.translations.solver.calendar_buttons.today,
        month: page.props.translations.solver.calendar_buttons.month,
        week: page.props.translations.solver.calendar_buttons.week,
        day: page.props.translations.solver.calendar_buttons.day
    },
    allDayText: page.props.translations.solver.calendar_buttons.all_day,
    editable: true,
    selectable: true,
    slotMinTime: '08:00:00',
    slotMaxTime: '20:00:00',
    nowIndicator: true,
    height: '710px',
    slotDuration: '00:30:00',
    allDaySlot: false,
    initialDate: new Date(),
    eventClick: (info) => {
        const ticketId = info.event.extendedProps?.ticket?.id
        if (ticketId) {
            window.location.href = `/tickets/${ticketId}`
        }
    },
    eventDidMount: (info) => {
        console.log('Ticket data:', {
            ticket: info.event.extendedProps.ticket,
            priority: info.event.extendedProps.ticket?.priority,
            status: info.event.extendedProps.ticket?.status
        })
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
            await axios.put(`/schedules/${info.event.id}`, {
                start_at: format(info.event.start, 'yyyy-MM-dd HH:mm:ss'),
                estimated_duration: scheduleForm.value.estimated_duration
            })
        } catch (error) {
            info.revert()
        }
    }
}

function formatDuration(minutes) {
    const hours = Math.floor(minutes / 60)
    const remainingMinutes = minutes % 60
    return `${hours}h${remainingMinutes.toString().padStart(2, '0')}`
}

function getStatusClass(status) {
    const baseClasses = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full'
    const colorClasses = {
        'new': 'bg-gray-100 text-gray-800',
        'in_progress': 'bg-yellow-100 text-yellow-800',
        'resolved': 'bg-green-100 text-green-800',
        'closed': 'bg-blue-100 text-blue-800'
    }
    return `${baseClasses} ${colorClasses[status.slug] || colorClasses['new']}`
}

function getPriorityClass(priority) {
    const baseClasses = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full'
    const colorClasses = {
        'low': 'bg-green-100 text-green-800',
        'medium': 'bg-yellow-100 text-yellow-800',
        'high': 'bg-red-100 text-red-800'
    }
    return `${baseClasses} ${colorClasses[priority.slug] || colorClasses['medium']}`
}

function getPriorityColor(priority) {
    const colors = {
        'low': '#34D399',      // vert
        'medium': '#F4BF76',    // orange
        'high': '#F87171',      // rouge
        'critical': '#EF4444'   // rouge foncé
    }
    return colors[priority] || '#F4BF76'
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

async function submitSchedule() {
    try {
        await axios.post('/schedules', {
            ticket_id: selectedTicket.value.id,
            ...scheduleForm.value
        })
        closeScheduleModal()
        // Recharger la page pour mettre à jour les données
        window.location.reload()
    } catch (error) {
        console.error('Error scheduling ticket:', error)
    }
}
</script>
