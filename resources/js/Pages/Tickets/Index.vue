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
            <div class="max-w-7xl mx-auto sm:px-2 lg:px-2">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-3 text-gray-900">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-1 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10">#</th>
                                        <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $page.props.translations.tickets.index.columns.title }}</th>
                                        <th scope="col" class="px-1 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-16" title="Statut">Statut</th>
                                        <th scope="col" class="px-1 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-12" title="Visibilité">Vis.</th>
                                        <th scope="col" class="px-1 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-20" title="Priorité">Priorité</th>
                                        <th scope="col" class="px-1 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12" title="Catégorie">Cat.</th>
                                        <th scope="col" class="px-1 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24" title="Équipement">Éq.</th>
                                        <th scope="col" class="px-1 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24" title="Assigné à">Assigné</th>
                                        <th scope="col" class="px-1 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24" title="Auteur">Auteur</th>
                                        <th scope="col" class="px-1 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24" title="Créé le">Date</th>
                                        <th scope="col" class="px-1 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-10">Act.</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="ticket in tickets.data" :key="ticket.id" class="hover:bg-gray-50">
                                        <td class="px-1 py-2 text-sm text-gray-500">
                                            #{{ ticket.id }}
                                        </td>
                                        <td class="px-2 py-2">
                                            <template v-if="!ticket.is_public && !canViewPrivateTicket(ticket)">
                                                <span class="italic text-gray-500">Privé</span>
                                            </template>
                                            <template v-else>
                                                <Link :href="route('tickets.show', ticket.id)" class="text-indigo-600 hover:text-indigo-900 block">
                                                    <div class="break-words" style="max-width: 100%; word-wrap: break-word; white-space: normal;">
                                                        {{ ticket.title.length > 300 ? ticket.title.substring(0, 300) + '...' : ticket.title }}
                                                    </div>
                                                </Link>
                                            </template>
                                        </td>
                                        <td class="px-1 py-2 text-center">
                                            <div class="flex justify-center">
                                                <TicketStatus :status="ticket.status" compact />
                                            </div>
                                        </td>
                                        <td class="px-1 py-2 text-center">
                                            <EyeIcon v-if="ticket.is_public" class="h-4 w-4 inline-block text-green-500" title="Public" />
                                            <EyeSlashIcon v-else class="h-4 w-4 inline-block text-orange-500" title="Privé" />
                                        </td>
                                        <td class="px-1 py-2 text-center">
                                            <div class="flex justify-center">
                                                <PriorityGauge :priority="ticket.priority" />
                                            </div>
                                        </td>
                                        <td class="px-1 py-2">
                                            <div v-if="ticket.category" class="relative group cursor-help">
                                                <span class="inline-flex items-center justify-center w-4 h-4 rounded-full text-xs font-bold"
                                                    :style="{ backgroundColor: ticket.category.color + '20', color: ticket.category.color }">
                                                    {{ ticket.category.name.charAt(0).toUpperCase() }}
                                                </span>
                                                <div class="hidden group-hover:block absolute z-50 bg-black text-white text-xs rounded py-1 px-2 whitespace-normal pointer-events-none" style="bottom: 100%; left: 0; margin-bottom: 5px;">
                                                    {{ ticket.category.name }}
                                                </div>
                                            </div>
                                            <span v-else class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-gray-200 text-gray-500 text-xs font-bold">
                                                -
                                            </span>
                                        </td>
                                        <td class="px-1 py-2 text-xs text-gray-500">
                                            <template v-if="!ticket.is_public && !canViewPrivateTicket(ticket)">
                                                <span class="italic text-gray-500">Privé</span>
                                            </template>
                                            <template v-else>  
                                                <div v-if="ticket.equipment?.designation && ticket.equipment.designation.length > 10" class="relative group cursor-help">
                                                    <span class="truncate block">
                                                        {{ ticket.equipment.designation.substring(0, 10) + '...' }}
                                                    </span>
                                                    <div class="hidden group-hover:block absolute z-50 bg-black text-white text-xs rounded py-1 px-2 whitespace-normal pointer-events-none" style="bottom: 100%; left: 0; margin-bottom: 5px;">
                                                        {{ ticket.equipment.designation }}
                                                    </div>
                                                </div>
                                                <span v-else class="truncate block">
                                                    {{ ticket.equipment?.designation || '-' }}
                                                </span>
                                            </template>

                                        </td>
                                        <td class="px-1 py-2 text-xs text-gray-500">
                                            <div v-if="ticket.assignees && ticket.assignees.length > 0" class="relative group cursor-help">
                                                <span v-if="ticket.assignees.length === 1" class="truncate block">
                                                    {{ ticket.assignees[0].name.length > 8 ? ticket.assignees[0].name.substring(0, 8) + '...' : ticket.assignees[0].name }}
                                                </span>
                                                <span v-else>
                                                    {{ ticket.assignees.length }} pers.
                                                </span>
                                                <div class="hidden group-hover:block absolute z-50 bg-black text-white text-xs rounded py-1 px-2 whitespace-normal pointer-events-none" style="bottom: 100%; left: 0; margin-bottom: 5px;">
                                                    <div v-for="assignee in ticket.assignees" :key="assignee.id">
                                                        {{ assignee.name }}
                                                    </div>
                                                </div>
                                            </div>
                                            <span v-else>-</span>
                                        </td>
                                        <td class="px-1 py-2 text-xs text-gray-500">
                                            <div class="relative group cursor-help" v-if="ticket.creator?.name">
                                                <span class="truncate block" :title="ticket.creator.name">
                                                    {{ ticket.creator.name.length > 8 ? ticket.creator.name.substring(0, 8) + '...' : ticket.creator.name }}
                                                </span>
                                                <div v-if="ticket.creator.name.length > 8" class="hidden group-hover:block absolute z-50 bg-black text-white text-xs rounded py-1 px-2 whitespace-normal pointer-events-none" style="bottom: 100%; left: 0; margin-bottom: 5px;">
                                                    {{ ticket.creator.name }}
                                                </div>
                                            </div>
                                            <span v-else>-</span>
                                        </td>
                                        <td class="px-1 py-2 text-xs text-gray-500">
                                            {{ formatDateCompact(ticket.created_at) }}
                                        </td>
                                        <td class="px-1 py-2 text-center">
                                            <button @click="archiveTicket(ticket)" class="text-gray-600 hover:text-gray-900" title="Archiver ce ticket">
                                                <ArchiveBoxIcon class="h-4 w-4" />
                                            </button>
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
import PriorityGauge from '@/Components/Tickets/PriorityGauge.vue'; // Nouveau composant
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

// Format de date plus compact
const formatDateCompact = (date) => {
    return format(new Date(date), 'dd/MM/yy', { locale: fr });
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

<style>
/* Assurer que le texte des titres de tickets s'enroule correctement */
.break-words {
  word-break: break-word;
  overflow-wrap: break-word;
  max-width: 100%;
}
</style>