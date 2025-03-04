<template>
    <div class="fixed inset-y-0 right-0 w-80 bg-white shadow-lg transform transition-transform duration-300"
        :class="{ 'translate-x-0': modelValue, 'translate-x-full': !modelValue }">
        
        <div class="h-full flex flex-col">
            <!-- Header -->
            <div class="p-4 border-b flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">{{ $page.props.translations.time_tracking?.filters || 'Filtres' }}</h3>
                <button @click="$emit('update:modelValue', false)" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="flex-1 overflow-y-auto p-4 space-y-4">
                <!-- Date de début -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $page.props.translations.time_tracking?.start_date || 'Date de début' }}</label>
                    <input 
                        type="date" 
                        v-model="filters.start_date" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />
                </div>

                <!-- Date de fin -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $page.props.translations.time_tracking?.end_date || 'Date de fin' }}</label>
                    <input 
                        type="date" 
                        v-model="filters.end_date" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />
                </div>

                <!-- Ticket -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $page.props.translations.time_tracking?.ticket || 'Ticket' }}</label>
                    <select 
                        v-model="filters.ticket_id" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">{{ $page.props.translations.time_tracking?.all_tickets || 'Tous les tickets' }}</option>
                        <option v-for="ticket in tickets" :key="ticket.id" :value="ticket.id">
                            #{{ ticket.id }} - {{ ticket.title }}
                        </option>
                    </select>
                </div>

                <!-- Facturable -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $page.props.translations.time_tracking?.billable || 'Facturable' }}</label>
                    <select 
                        v-model="filters.billable" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">Tous</option>
                        <option value="1">Oui</option>
                        <option value="0">Non</option>
                    </select>
                </div>
            </div>

            <!-- Footer -->
            <div class="p-4 border-t space-y-3">
                <button
                    @click="applyFilters"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    {{ $page.props.translations.time_tracking?.filter || 'Filtrer' }}
                </button>
                <button
                    @click="resetFilters"
                    class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    {{ $page.props.translations.time_tracking?.reset || 'Réinitialiser' }}
                </button>
                <Link 
                    v-if="filters.start_date && filters.end_date"
                    :href="route('time-entries.pdf-report', { 
                        start_date: filters.start_date, 
                        end_date: filters.end_date 
                    })"
                    class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    target="_blank"
                >
                    {{ $page.props.translations.time_tracking?.export_pdf || 'Exporter en PDF' }}
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';

const props = defineProps({
    modelValue: Boolean,
    tickets: Array,
    currentFilters: {
        type: Object,
        default: () => ({})
    }
});

defineEmits(['update:modelValue']);

const filters = ref({
    start_date: props.currentFilters.start_date || '',
    end_date: props.currentFilters.end_date || '',
    ticket_id: props.currentFilters.ticket_id || '',
    billable: props.currentFilters.billable || ''
});

const applyFilters = () => {
    router.get(route('time-entries.list'), {
        ...filters.value,
        page: 1 // Réinitialiser la pagination lors de l'application des filtres
    }, {
        preserveState: true,
        preserveScroll: true,
        only: ['timeEntries', 'statistics']
    });
};

const resetFilters = () => {
    filters.value = {
        start_date: '',
        end_date: '',
        ticket_id: '',
        billable: ''
    };
    
    router.get(route('time-entries.list'), {
        page: 1
    }, {
        preserveState: true,
        preserveScroll: true,
        only: ['timeEntries', 'statistics']
    });
};
</script>
