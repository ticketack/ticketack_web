<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { format, parseISO } from 'date-fns';
import { fr } from 'date-fns/locale';
import { useToast } from 'vue-toastification';
import { ArrowPathIcon, EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/outline';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import FilterSidebar from '@/Components/Tickets/FilterSidebar.vue';
import TicketStatus from '@/Components/Tickets/TicketStatus.vue';
import TicketPriority from '@/Components/Tickets/TicketPriority.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    tickets: Object,
    filters: Object,
    statuses: Array,
    categories: Array,
    users: Array,
});

const toast = useToast();
const showFilters = ref(false);

// Nous n'avons plus besoin du filterForm car nous utilisons le FilterSidebar

// Fonction pour désarchiver un ticket
function unarchiveTicket(ticket) {
    if (confirm(`Êtes-vous sûr de vouloir désarchiver le ticket "${ticket.title}" ?`)) {
        useForm().post(route('tickets.unarchive', ticket.id), {
            onSuccess: () => {
                toast.success('Ticket désarchivé avec succès');
                // Recharger la page
                window.location.reload();
            },
            onError: (errors) => {
                toast.error(Object.values(errors).join('\n'));
            }
        });
    }
}

// Nous n'avons plus besoin de ces fonctions car elles sont gérées par le FilterSidebar

// Fonction pour formater la date
function formatDate(dateString) {
    if (!dateString) return '-';
    return format(parseISO(dateString), 'dd/MM/yyyy', { locale: fr });
}

// Ces fonctions ont été remplacées par les composants TicketStatus et TicketPriority
</script>

<template>
    <Head title="Tickets archivés" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Tickets archivés
                </h2>
                <div class="flex items-center space-x-4">
                    <button
                        @click="showFilters = true"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        <svg class="-ml-0.5 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Filtres
                    </button>
                    <Link :href="route('tickets.index')" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Voir tickets actifs
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-2">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-4">
                <Breadcrumbs :items="[
                    { name: 'Tickets', route: 'tickets.index' },
                    { name: 'Tickets archivés' }
                ]" />
            </div>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 table-fixed">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">ID</th>
                                    <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-64">Titre</th>
                                    <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Statut</th>
                                    <th scope="col" class="px-2 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-16">Visibilité</th>
                                    <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Priorité</th>
                                    <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Catégorie</th>
                                    <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Équipement</th>
                                    <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Assigné à</th>
                                    <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Auteur</th>
                                    <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Créé le</th>
                                    <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="ticket in tickets.data" :key="ticket.id" class="hover:bg-gray-50">
                                    <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500">
                                        #{{ ticket.id }}
                                    </td>
                                    <td class="px-2 py-2">
                                        <div class="relative group cursor-help">
                                            <Link :href="route('tickets.show', ticket.id)" class="text-indigo-600 hover:text-indigo-900 truncate block w-60">
                                                {{ ticket.title.length > 80 ? ticket.title.substring(0, 80) + '...' : ticket.title }}
                                            </Link>
                                            <div class="hidden group-hover:block absolute z-50 bg-black text-white text-xs rounded py-2 px-3 whitespace-normal max-w-md pointer-events-none" style="bottom: 100%; left: 0; margin-bottom: 5px;" v-if="ticket.title.length > 80">
                                                {{ ticket.title }}
                                            </div>
                                        </div>
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
                                        <span v-else>-</span>
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500">
                                        {{ ticket.equipment?.name || '-' }}
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
                                            <button @click="unarchiveTicket(ticket)" class="text-green-600 hover:text-green-900" title="Désarchiver ce ticket">
                                                <ArrowPathIcon class="h-5 w-5" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="tickets.data.length === 0">
                                    <td colspan="11" class="px-4 py-2 text-center text-sm text-gray-500">
                                        Aucun ticket archivé trouvé
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <Pagination class="mt-6" :links="tickets.links" />
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
