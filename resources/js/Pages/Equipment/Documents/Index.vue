<template>
    <Head :title="$page.props.translations.equipment.documents.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $page.props.translations.equipment.documents.title }}
                </h2>
                <Link :href="route('equipment.edit', equipment.id)" class="text-gray-600 hover:text-gray-900">
                    {{ $page.props.translations.equipment.documents.back_to_list }}
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <!-- Informations sur l'équipement -->
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ equipment.designation }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">{{ $page.props.translations.equipment.edit.marque }}: <span class="font-medium text-gray-900">{{ equipment.marque }}</span></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">{{ $page.props.translations.equipment.edit.modele }}: <span class="font-medium text-gray-900">{{ equipment.modele }}</span></p>
                                </div>
                            </div>
                        </div>

                        <!-- Liste des documents -->
                        <DocumentList 
                            :equipment="equipment" 
                            :documents="documents"
                            :can-create="can.create"
                            :can-edit="can.edit"
                            :can-delete="can.delete"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DocumentList from '@/Pages/Equipment/Partials/Documents/DocumentList.vue';

defineProps({
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
</script>
