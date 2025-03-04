<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $page.props.translations.tickets.index.title }}
                </h2>
                <div class="flex items-center space-x-4">
                    <Link :href="route('tickets.archived')" 
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <ArchiveBoxIcon class="-ml-0.5 mr-2 h-4 w-4" />
                        Tickets archivés
                    </Link>
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

        <div class="py-2">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-4">
                <Breadcrumbs :items="[
                    { name: 'Tickets' }
                ]" />
            </div>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 table-fixed">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">{{ $page.props.translations.tickets.index.columns.id }}</th>
                                        <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-64">{{ $page.props.translations.tickets.index.columns.title }}</th>
                                        <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">{{ $page.props.translations.tickets.index.columns.status }}</th>
                                        <th scope="col" class="px-2 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-16">{{ $page.props.translations.tickets.index.columns.visibility }}</th>
                                        <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">{{ $page.props.translations.tickets.index.columns.priority }}</th>
                                        <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">{{ $page.props.translations.tickets.index.columns.category }}</th>
                                        <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">{{ $page.props.translations.tickets.index.columns.equipment }}</th>
                                        <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">{{ $page.props.translations.tickets.index.columns.assigned_to }}</th>
                                        <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">{{ $page.props.translations.tickets.index.columns.author }}</th>
                                        <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">{{ $page.props.translations.tickets.index.columns.created_at }}</th>
                                        <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="ticket in tickets.data" :key="ticket.id" class="hover:bg-gray-50">
                                        <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500">
                                            #{{ ticket.id }}
                                        </td>
                                        <td class="px-2 py-2">
                                            <template v-if="!ticket.is_public && !canViewPrivateTicket(ticket)">
                                                <span class="italic text-gray-500">Privé</span>
                                            </template>
                                            <template v-else>
                                                <div class="relative group cursor-help">
                                                    <Link :href="route('tickets.show', ticket.id)" class="text-indigo-600 hover:text-indigo-900 truncate block w-60">
                                                        {{ ticket.title.length > 80 ? ticket.title.substring(0, 80) + '...' : ticket.title }}
                                                    </Link>
                                                    <div class="hidden group-hover:block absolute z-50 bg-black text-white text-xs rounded py-2 px-3 whitespace-normal max-w-md pointer-events-none" style="bottom: 100%; left: 0; margin-bottom: 5px;" v-if="ticket.title.length > 80">
                                                        {{ ticket.title }}
                                                    </div>
                                                </div>
                                            </template>
                                        </td>
                                        <td class="px-2 py-2 whitespace-nowrap">
                                            <TicketStatus :status="ticket.status" />
                                        </td>
                                        <td class="px-2 py-2 whitespace-nowrap text-center">
                                            <EyeIcon v-if="ticket.is_public" class="h-5 w-5 inline-block text-green-500" />
                                            <EyeSlashIcon v-else class="h-5 w-5 inline-block text-orange-500" />
                                        </td>
                                        <td class="px-2 py-2 whitespace-nowrap">
                                            <TicketPriority :priority="ticket.priority" />
                                        </td>
                                        <td class="px-2 py-2 whitespace-nowrap">
                                            <span v-if="ticket.category" class="px-2 py-1 text-xs font-medium rounded-full"
                                                :style="{ backgroundColor: ticket.category.color + '20', color: ticket.category.color }">
                                                {{ ticket.category.name }}
                                            </span>
                                        </td>
                                        <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500">
                                            <template v-if="!ticket.is_public && !canViewPrivateTicket(ticket)">
                                                <span class="italic text-gray-500">Privé</span>
                                            </template>
                                            <template v-else>
                                                {{ ticket.equipment?.name || '-' }}
                                            </template>
                                        </td>
                                        <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500">
                                            <div class="relative group cursor-help" v-if="ticket.assignee?.name && ticket.assignee.name.length > 12">
                                                <span class="truncate block w-28">{{ ticket.assignee.name.substring(0, 12) + '...' }}</span>
                                                <div class="hidden group-hover:block absolute z-50 bg-black text-white text-xs rounded py-2 px-3 whitespace-normal pointer-events-none" style="bottom: 100%; left: 0; margin-bottom: 5px;">
                                                    {{ ticket.assignee.name }}
                                                </div>
                                            </div>
                                            <span v-else>{{ ticket.assignee?.name || 'Non assigné' }}</span>
                                        </td>
                                        <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500">
                                            <div class="relative group cursor-help" v-if="ticket.creator?.name && ticket.creator.name.length > 12">
                                                <span class="truncate block w-28">{{ ticket.creator.name.substring(0, 12) + '...' }}</span>
                                                <div class="hidden group-hover:block absolute z-50 bg-black text-white text-xs rounded py-2 px-3 whitespace-normal pointer-events-none" style="bottom: 100%; left: 0; margin-bottom: 5px;">
                                                    {{ ticket.creator.name }}
                                                </div>
                                            </div>
                                            <span v-else>{{ ticket.creator?.name || '-' }}</span>
                                        </td>
                                        <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDate(ticket.created_at) }}
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <button @click="archiveTicket(ticket)" class="text-gray-600 hover:text-gray-900" title="Archiver ce ticket">
                                                    <ArchiveBoxIcon class="h-5 w-5" />
                                                </button>
                                            </div>
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
import { Link, usePage, useForm } from '@inertiajs/vue3';
import { EyeIcon, EyeSlashIcon, ArchiveBoxIcon } from '@heroicons/vue/24/outline';
import { useToast } from 'vue-toastification';
import TicketStatus from '@/Components/Tickets/TicketStatus.vue';
import TicketPriority from '@/Components/Tickets/TicketPriority.vue';
import FilterSidebar from '@/Components/Tickets/FilterSidebar.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import { ref } from 'vue';
import Pagination from '@/Components/Pagination.vue';
import { format } from 'date-fns';
import { fr } from 'date-fns/locale';

const showFilters = ref(false);
const toast = useToast();

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

// Fonction pour archiver un ticket
const archiveTicket = (ticket) => {
    if (confirm(`Êtes-vous sûr de vouloir archiver le ticket "${ticket.title}" ?`)) {
        useForm().post(route('tickets.archive', ticket.id), {}, {
            onSuccess: () => {
                toast.success('Ticket archivé avec succès');
                // Recharger la page
                window.location.reload();
            },
            onError: (errors) => {
                toast.error(Object.values(errors).join('\n'));
            }
        });
    }
};
</script>
