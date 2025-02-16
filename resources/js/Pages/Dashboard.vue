<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    equipmentCount: {
        type: Number,
        required: true
    },
    latestEquipments: {
        type: Array,
        required: true
    }
});
</script>

<template>
    <Head :title="$page.props.translations.pages.dashboard.title" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                {{ $page.props.translations.pages.dashboard.title }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-3 gap-6">
                    <!-- Panneau Statistiques -->
                    <div class="overflow-hidden bg-white shadow-lg rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ $page.props.translations.pages.dashboard.statistics }}</h3>
                            <div class="flex items-center space-x-4">
                                <div class="p-3 bg-blue-100 rounded-full">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">{{ $page.props.translations.pages.dashboard.total_equipment }}</p>
                                    <p class="text-2xl font-bold text-gray-800">{{ equipmentCount }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Panneau Derniers produits -->
                    <div class="overflow-hidden bg-white shadow-lg rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ $page.props.translations.pages.dashboard.latest_products }}</h3>
                            <div class="space-y-4">
                                <div v-for="equipment in latestEquipments" :key="equipment.id" class="flex items-center space-x-4 p-3 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                                    <div class="p-2 bg-gray-100 rounded-full">
                                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-800">{{ equipment.designation }}</p>
                                        <p class="text-xs text-gray-500">{{ equipment.marque }} - {{ equipment.modele }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Troisième panneau -->
                    <div class="overflow-hidden bg-white shadow-lg rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ $page.props.translations.pages.dashboard.recent_activity }}</h3>
                            <!-- Contenu à venir -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
