<template>
    <Head :title="$page.props.translations.planning.title" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $page.props.translations.planning.index.title }}
            </h2>
        </template>

        <div class="py-2">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div v-if="loading" class="text-center py-4">
                            {{ $page.props.translations.planning.index.loading }}
                        </div>
                        <div v-else>
                            <FullCalendar 
                                :options="calendarOptions"
                                class="planning-calendar" 
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import interactionPlugin from '@fullcalendar/interaction';
import frLocale from '@fullcalendar/core/locales/fr';

const props = defineProps({
    events: {
        type: Array,
        required: true
    }
});

const loading = ref(false);

const calendarOptions = computed(() => ({
    plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
    initialView: 'timeGridWeek',
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
    },
    locale: frLocale,
    events: props.events,
    // Désactiver complètement la conversion de fuseau horaire
    // Cela garantit que les dates sont utilisées telles quelles, sans aucune conversion
    timeZone: 'local',
    eventClick: (info) => {
        if (info.event.url) {
            window.location.href = info.event.url;
        }
    },
    eventContent: (arg) => {
        return {
            html: `
                <div class="fc-content">
                    <div class="fc-title">${arg.event.title}</div>
                    ${arg.event.extendedProps.assignedTo ? 
                        `<div class="fc-description text-xs">${arg.event.extendedProps.assignedTo.name}</div>` : 
                        ''}
                </div>
            `
        }
    },
    eventDidMount: (info) => {
        if (info.event.extendedProps.assignedTo?.color) {
            info.el.style.backgroundColor = info.event.extendedProps.assignedTo.color;
            info.el.style.borderColor = info.event.extendedProps.assignedTo.color;
        }
    },
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
    ]
}));
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
</style>
