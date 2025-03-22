<template>
    <Head :title="$page.props.translations.equipment.show.title" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $page.props.translations.equipment.show.title }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-8xl mx-auto sm:px-2 lg:px-2">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <!-- Détails de l'équipement -->
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-lg font-semibold text-gray-900">
                                {{ equipment.designation }}
                            </h2>
                            <div class="flex space-x-2">
                                <Link v-if="can.edit" :href="route('equipment.edit', equipment.id)" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ $page.props.translations.equipment.show.edit }}
                                </Link>
                                <Link :href="route('equipment.index')" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ $page.props.translations.equipment.show.back }}
                                </Link>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Informations de l'équipement -->
                            <div class="space-y-4">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">{{ $page.props.translations.equipment.show.designation }}</h3>
                                    <p class="mt-1 text-sm text-gray-900">{{ equipment.designation }}</p>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">{{ $page.props.translations.equipment.show.marque }}</h3>
                                    <p class="mt-1 text-sm text-gray-900">{{ equipment.marque }}</p>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">{{ $page.props.translations.equipment.show.modele }}</h3>
                                    <p class="mt-1 text-sm text-gray-900">{{ equipment.modele }}</p>
                                </div>
                                <div v-if="equipment.parent_id">
                                    <h3 class="text-sm font-medium text-gray-500">{{ $page.props.translations.equipment.show.parent }}</h3>
                                    <p class="mt-1 text-sm text-gray-900">
                                        <Link :href="route('equipment.show', equipment.parent_id)" class="text-blue-600 hover:underline">
                                            {{ getParentName() }}
                                        </Link>
                                    </p>
                                </div>
                            </div>

                            <!-- Image de l'équipement -->
                            <div class="flex justify-center items-start">
                                <div v-if="equipment.image" class="mt-2 relative">
                                    <img :src="equipment.image" alt="Image de l'équipement" class="max-h-64 rounded-lg shadow-md">
                                </div>
                                <div v-else class="mt-2 flex items-center justify-center w-full h-64 bg-gray-100 rounded-lg border-2 border-dashed border-gray-300">
                                    <div class="text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p class="mt-1 text-sm text-gray-500">{{ $page.props.translations.equipment.show.no_image }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section des documents -->
                    <div class="mt-6 border-t border-gray-200 pt-6 px-6">
                        <DocumentList 
                            :equipment="equipment" 
                            :documents="documents"
                            :can-create="can.create_document"
                            :can-edit="can.edit_document"
                            :can-delete="can.delete_document"
                            @document-added="refreshDocuments"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DocumentList from '@/Pages/Equipment/Partials/Documents/DocumentList.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const page = usePage();

const props = defineProps({
    equipment: {
        type: Object,
        required: true
    },
    documents: {
        type: Array,
        required: true
    },
    can: {
        type: Object,
        required: true
    }
});

// Fonction pour rafraîchir les documents après ajout
const refreshDocuments = () => {
    router.reload({ only: ['documents'] });
};

// Fonction pour obtenir le nom du parent
const getParentName = () => {
    // Cette fonction devrait idéalement récupérer le nom du parent depuis le store ou via une requête API
    // Pour l'instant, on retourne simplement un texte générique
    return "Équipement parent";
};
</script>
