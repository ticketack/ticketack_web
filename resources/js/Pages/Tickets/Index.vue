<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $page.props.translations.tickets.index.title }}
                </h2>
                <div class="flex items-center space-x-4">
                    <button
                        @click="showFilters = true"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        <svg class="-ml-0.5 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        {{ $page.props.translations.tickets.index.filters }}
                    </button>
                    <Link :href="route('tickets.create')" 
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        {{ $page.props.translations.tickets.index.new_ticket }}
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $page.props.translations.tickets.index.columns.id }}</th>
                                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $page.props.translations.tickets.index.columns.title }}</th>
                                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $page.props.translations.tickets.index.columns.status }}</th>
                                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $page.props.translations.tickets.index.columns.visibility }}</th>
                                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $page.props.translations.tickets.index.columns.priority }}</th>
                                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $page.props.translations.tickets.index.columns.category }}</th>
                                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $page.props.translations.tickets.index.columns.equipment }}</th>
                                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $page.props.translations.tickets.index.columns.assigned_to }}</th>
                                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $page.props.translations.tickets.index.columns.author }}</th>
                                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $page.props.translations.tickets.index.columns.created_at }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="ticket in tickets.data" :key="ticket.id" class="hover:bg-gray-50">
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                            #{{ ticket.id }}
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap">
                                            <template v-if="!ticket.is_public && !canViewPrivateTicket(ticket)">
                                                <span class="italic text-gray-500">Privé</span>
                                            </template>
                                            <template v-else>
                                                <Link :href="route('tickets.show', ticket.id)" class="text-indigo-600 hover:text-indigo-900">
                                                    {{ ticket.title }}
                                                </Link>
                                            </template>
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap">
                                            <TicketStatus :status="ticket.status" />
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-center">
                                            <EyeIcon v-if="ticket.is_public" class="h-5 w-5 inline-block text-green-500" />
                                            <EyeSlashIcon v-else class="h-5 w-5 inline-block text-orange-500" />
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap">
                                            <TicketPriority :priority="ticket.priority" />
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap">
                                            <span v-if="ticket.category" class="px-2 py-1 text-xs font-medium rounded-full"
                                                :style="{ backgroundColor: ticket.category.color + '20', color: ticket.category.color }">
                                                {{ ticket.category.name }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                            <template v-if="!ticket.is_public && !canViewPrivateTicket(ticket)">
                                                <span class="italic text-gray-500">Privé</span>
                                            </template>
                                            <template v-else>
                                                {{ ticket.equipment?.name || '-' }}
                                            </template>
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                            {{ ticket.assignee?.name || 'Non assigné' }}
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                            {{ ticket.creator?.name || '-' }}
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDate(ticket.created_at) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <Pagination class="mt-6" :links="tickets.links" />
                    </div>
                </div>
            </div>
        </div>

        <FilterSidebar
            v-model="showFilters"
            :statuses="statuses"
            :categories="categories"
            :users="users"
            :current-filters="filters"
        />
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/outline';
import TicketStatus from '@/Components/Tickets/TicketStatus.vue';
import TicketPriority from '@/Components/Tickets/TicketPriority.vue';
import FilterSidebar from '@/Components/Tickets/FilterSidebar.vue';
import { ref } from 'vue';
import Pagination from '@/Components/Pagination.vue';
import { format } from 'date-fns';
import { fr } from 'date-fns/locale';

const showFilters = ref(false);

defineProps({
    tickets: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        required: true
    },
    statuses: {
        type: Array,
        required: true
    },
    categories: {
        type: Array,
        required: true
    },
    users: {
        type: Array,
        required: true
    }
});

const formatDate = (date) => {
    return format(new Date(date), 'd MMMM yyyy', { locale: fr });
};

const canViewPrivateTicket = (ticket) => {
    const user = usePage().props.auth.user;
    return user.id === ticket.created_by || 
           user.id === ticket.assigned_to || 
           user.roles.some(role => role.name === 'admin');
};
</script>
