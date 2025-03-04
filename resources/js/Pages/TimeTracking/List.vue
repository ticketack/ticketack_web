<template>
    <AuthenticatedLayout :title="$page.props.translations.time_tracking?.list_title || 'Liste des pointages'">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $page.props.translations.time_tracking?.list_title || 'Liste des pointages' }}
                </h2>
                <div class="flex items-center space-x-4">
                    <button
                        @click="showFilters = true"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        <FunnelIcon class="-ml-0.5 mr-2 h-4 w-4" />
                        {{ $page.props.translations.time_tracking?.filters || 'Filtres' }}
                    </button>
                    <Link 
                        :href="route('time-tracking.index')"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        <ArrowLeftIcon class="-ml-0.5 mr-2 h-4 w-4" />
                        {{ $page.props.translations.time_tracking?.back_to_tracking || 'Retour au pointage' }}
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Statistiques -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold mb-4">Statistiques</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Total des heures</p>
                                <p class="text-2xl font-bold">
                                    {{ statistics.total_hours }}h{{ statistics.total_minutes > 0 ? statistics.total_minutes : '' }}
                                </p>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Nombre d'entrées</p>
                                <p class="text-2xl font-bold">{{ statistics.total_entries }}</p>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Période</p>
                                <p class="text-lg font-semibold">
                                    {{ filters.start_date ? formatDate(filters.start_date) : 'Toutes dates' }}
                                    {{ filters.start_date && filters.end_date ? ' - ' + formatDate(filters.end_date) : '' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>



                <!-- Liste des entrées de temps -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durée</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Facturable</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="entry in timeEntries.data" :key="entry.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ formatDate(entry.entry_date) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <Link 
                                                v-if="entry.ticket"
                                                :href="route('tickets.show', entry.ticket.id)" 
                                                class="text-blue-600 hover:text-blue-800"
                                            >
                                                #{{ entry.ticket.id }} - {{ entry.ticket.title }}
                                            </Link>
                                            <span v-else class="text-gray-500">Ticket supprimé</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ formatDuration(entry.minutes_spent) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">
                                            {{ entry.description || '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <span 
                                                :class="entry.billable ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'" 
                                                class="px-2 py-1 rounded-full text-xs"
                                            >
                                                {{ entry.billable ? 'Oui' : 'Non' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div class="flex space-x-2">
                                                <button 
                                                    @click="openEditModal(entry)" 
                                                    class="text-blue-600 hover:text-blue-800"
                                                >
                                                    <PencilIcon class="h-5 w-5" />
                                                </button>
                                                <button 
                                                    @click="confirmDelete(entry)" 
                                                    class="text-red-600 hover:text-red-800"
                                                >
                                                    <TrashIcon class="h-5 w-5" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="timeEntries.data.length === 0">
                                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                            Aucune entrée de temps trouvée
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="mt-4">
                            <Pagination :links="timeEntries.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal d'édition -->
        <Modal :show="editModalOpen" @close="closeEditModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    Modifier l'entrée de temps
                </h2>
                <form @submit.prevent="updateTimeEntry">
                    <div class="mb-4">
                        <label for="edit_entry_date" class="block text-sm font-medium text-gray-700">Date</label>
                        <input 
                            type="date" 
                            id="edit_entry_date" 
                            v-model="editForm.entry_date" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required
                        />
                    </div>
                    <div class="mb-4">
                        <label for="edit_minutes_spent" class="block text-sm font-medium text-gray-700">Durée (minutes)</label>
                        <input 
                            type="number" 
                            id="edit_minutes_spent" 
                            v-model="editForm.minutes_spent" 
                            min="1"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required
                        />
                    </div>
                    <div class="mb-4">
                        <label for="edit_description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea 
                            id="edit_description" 
                            v-model="editForm.description" 
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        ></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="edit_billable" class="block text-sm font-medium text-gray-700">Facturable</label>
                        <div class="mt-1">
                            <label class="inline-flex items-center">
                                <input 
                                    type="checkbox" 
                                    v-model="editForm.billable" 
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                                <span class="ml-2 text-sm text-gray-600">Oui</span>
                            </label>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button 
                            type="button"
                            @click="closeEditModal"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Annuler
                        </button>
                        <button 
                            type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Modal de confirmation de suppression -->
        <Modal :show="deleteModalOpen" @close="closeDeleteModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    Confirmer la suppression
                </h2>
                <p class="text-sm text-gray-600 mb-4">
                    Êtes-vous sûr de vouloir supprimer cette entrée de temps ? Cette action est irréversible.
                </p>
                <div class="mt-6 flex justify-end space-x-3">
                    <button 
                        type="button"
                        @click="closeDeleteModal"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Annuler
                    </button>
                    <button 
                        type="button"
                        @click="deleteTimeEntry"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                    >
                        Supprimer
                    </button>
                </div>
            </div>
        </Modal>
            <FilterSidebar
            v-model="showFilters"
            :tickets="tickets"
            :current-filters="filters"
        />
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import Pagination from '@/Components/Pagination.vue';
import FilterSidebar from '@/Components/TimeTracking/FilterSidebar.vue';
import { 
    ArrowLeftIcon, 
    PencilIcon, 
    TrashIcon, 
    FunnelIcon, 
    XMarkIcon,
    DocumentArrowDownIcon
} from '@heroicons/vue/24/outline';

// Props
const props = defineProps({
    timeEntries: Object,
    tickets: Array,
    filters: Object,
    statistics: Object
});

// État pour le panneau de filtres
const showFilters = ref(false);

// État pour le modal d'édition
const editModalOpen = ref(false);
const editForm = useForm({
    id: null,
    entry_date: '',
    minutes_spent: 0,
    description: '',
    billable: false
});

// État pour le modal de suppression
const deleteModalOpen = ref(false);
const entryToDelete = ref(null);

// Méthodes pour l'édition
const openEditModal = (entry) => {
    editForm.id = entry.id;
    editForm.entry_date = entry.entry_date;
    editForm.minutes_spent = entry.minutes_spent;
    editForm.description = entry.description || '';
    editForm.billable = entry.billable;
    editModalOpen.value = true;
};

const closeEditModal = () => {
    editModalOpen.value = false;
    editForm.reset();
};

const updateTimeEntry = () => {
    editForm.put(route('time-entries.update', editForm.id), {
        onSuccess: () => {
            closeEditModal();
        }
    });
};

// Méthodes pour la suppression
const confirmDelete = (entry) => {
    entryToDelete.value = entry;
    deleteModalOpen.value = true;
};

const closeDeleteModal = () => {
    deleteModalOpen.value = false;
    entryToDelete.value = null;
};

const deleteTimeEntry = () => {
    if (entryToDelete.value) {
        useForm({}).delete(route('time-entries.destroy', entryToDelete.value.id), {
            onSuccess: () => {
                closeDeleteModal();
            }
        });
    }
};

// Méthodes utilitaires
const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString();
};

const formatDuration = (minutes) => {
    const hours = Math.floor(minutes / 60);
    const remainingMinutes = minutes % 60;
    
    if (hours > 0) {
        return `${hours}h${remainingMinutes > 0 ? remainingMinutes : ''}`;
    }
    
    return `${remainingMinutes}min`;
};
</script>
