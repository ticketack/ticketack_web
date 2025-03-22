<template>
    <Head :title="$page.props.translations.equipment.index.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $page.props.translations.equipment.index.title }}
                </h2>
                <Link
                    v-if="$page.props.permissions && $page.props.permissions['equipment.create']"
                    :href="route('equipment.create')"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors"
                >
                    {{ $page.props.translations.equipment.index.new_equipment }}
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-8xl mx-auto sm:px-2 lg:px-2">
                <div v-if="$page.props.flash?.message" class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
                    {{ $page.props.flash.message }}
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="mb-4">
                            <h3 class="text-lg font-medium">{{ $page.props.translations.equipment.index.list }}</h3>
                        </div>
                        
                        <!-- Barre de recherche -->
                        <div class="mb-6">
                            <div class="flex items-center">
                                <div class="relative flex-grow">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                        </svg>
                                    </div>
                                    <input
                                        v-model="searchQuery"
                                        type="text"
                                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        :placeholder="$page.props.translations.equipment.index.search_placeholder || 'Rechercher un équipement...'"
                                        @keyup.enter="performSearch"
                                    />
                                </div>
                                <button
                                    @click="performSearch"
                                    :disabled="isSearching"
                                    class="ml-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white"
                                    :class="isSearching ? 'bg-indigo-400 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500'"
                                >
                                    <svg v-if="isSearching" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ $page.props.translations.equipment.index.search || 'Rechercher' }}
                                </button>
                            </div>
                        </div>
                        <!-- Boutons de contrôle de l'arborescence -->
                        <div class="mb-4 flex justify-end space-x-2">
                            <button 
                                type="button" 
                                @click="toggleAllNodes(true)" 
                                class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" />
                                </svg>
                                Déployer tout
                            </button>
                            <button 
                                type="button" 
                                @click="toggleAllNodes(false)" 
                                class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 9V4.5M9 9H4.5M9 9L3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5l5.25 5.25" />
                                </svg>
                                Replier tout
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <PrimeTreeComponent 
                                :treeData="treeData"
                                :search="searchQuery"
                                :matchingIds="matchingIds"
                                :parentIds="parentIds"
                                @view="viewEquipment"
                                @edit="editEquipment"
                                @delete="deleteEquipment"
                                ref="treeComponent"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';
import PrimeTreeComponent from './Partials/PrimeTreeComponent.vue';
import { ref } from 'vue';

defineProps({
    treeData: {
        type: Array,
        required: true
    },
    allEquipment: {
        type: Array,
        required: true
    },
    matchingIds: {
        type: Array,
        default: () => []
    },
    parentIds: {
        type: Array,
        default: () => []
    }
});

const viewEquipment = (id) => {
    router.visit(route('equipment.show', id));
};

const editEquipment = (id) => {
    router.visit(route('equipment.edit', id));
};

const page = usePage();
const toast = useToast();

// Initialiser la recherche avec la valeur provenant des props
const searchQuery = ref(page.props.search || '');
const isSearching = ref(false);
let searchTimeout = null;

// Fonction pour effectuer la recherche
function performSearch() {
    // Prévenir les erreurs en désactivant temporairement le bouton de recherche
    isSearching.value = true;
    
    // Utiliser un délai pour éviter les requêtes trop fréquentes
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('equipment.index'), { search: searchQuery.value }, {
            preserveState: true,
            replace: true,
            onSuccess: () => {
                isSearching.value = false;
                
                // Attendre que les composants soient mis à jour avec les nouveaux résultats
                setTimeout(() => {
                    // Si la recherche n'est pas vide, envoyer un événement pour ouvrir uniquement les nœuds pertinents
                    if (searchQuery.value && searchQuery.value.trim() !== '') {
                        // Créer un événement personnalisé pour ouvrir uniquement les nœuds pertinents
                        const event = new CustomEvent('open-relevant-nodes');
                        document.dispatchEvent(event);
                    }
                }, 200);
            },
            onError: () => {
                isSearching.value = false;
                toast.error('Erreur lors de la recherche. Veuillez réessayer.');
            }
        });
    }, 300);
}

const deleteEquipment = (id) => {
    if (confirm(page.props.translations.equipment.index.confirm_delete)) {
        router.delete(route('equipment.destroy', id), {
            onSuccess: () => {
                toast.success(page.props.translations.equipment.index.deleted_success || 'Équipement supprimé avec succès');
            },
            onError: (errors) => {
                toast.error(Object.values(errors).join('\n') || 'Erreur lors de la suppression de l\'équipement');
            }
        });
    }
};

// Fonction pour forcer un rafraîchissement de l'arborescence (conservée mais non utilisée dans l'interface)
const forceRefresh = () => {
    router.visit(route('equipment.index'), {
        data: { search: searchQuery.value },
        preserveState: false,  // Ne pas préserver l'état pour forcer un rechargement complet
        replace: true,
        onSuccess: () => {
            // Forcer l'ouverture des nœuds après un court délai
            setTimeout(() => {
                toggleAllNodes(true);
            }, 200);
        }
    });
};

// Fonction pour ouvrir ou fermer tous les nœuds
const treeComponent = ref(null);
const toggleAllNodes = (open) => {
    if (treeComponent.value) {
        if (open) {
            treeComponent.value.expandAll();
        } else {
            treeComponent.value.collapseAll();
        }
    }
};
</script>
