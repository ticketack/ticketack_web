<template>
    <Head :title="$page.props.translations.planning.title" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $page.props.translations.planning.index.title }}
            </h2>
        </template>

        <div class="py-12">
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
    plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
    initialView: 'timeGridWeek',
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    locale: frLocale,
    events: props.events,
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
    height: 'auto',
    allDaySlot: false,
    slotMinTime: '06:00:00',
    slotMaxTime: '22:00:00',
}));
</script>

<style>
.planning-calendar {
    height: calc(100vh - 300px);
    min-height: 600px;
}

.fc-event {
    cursor: pointer;
}

.fc-event:hover {
    opacity: 0.9;
}
</style>
