<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { getPriorityColor } from '@/Utils/ticketPriorityColors';

defineProps({
    equipmentCount: {
        type: Number,
        required: true
    },
    latestEquipments: {
        type: Array,
        required: true
    },
    ticketStats: {
        type: Object,
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

                    <!-- Statistiques des tickets -->
                    <div class="overflow-hidden bg-white shadow-lg rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ $page.props.translations.pages.dashboard.ticket_stats }}</h3>
                            
                            <!-- Total des tickets -->
                            <div class="flex items-center space-x-4 mb-6">
                                <div class="p-3 bg-purple-100 rounded-full">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">{{ $page.props.translations.pages.dashboard.total_tickets }}</p>
                                    <p class="text-2xl font-bold text-gray-800">{{ ticketStats.total }}</p>
                                </div>
                            </div>

                            <!-- Par statut -->
                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">{{ $page.props.translations.pages.dashboard.by_status }}</h4>
                                <div class="space-y-2">
                                    <div v-for="(count, status) in ticketStats.byStatus" :key="status" class="flex justify-between items-center bg-gray-50 p-2 rounded">
                                        <span class="text-sm text-gray-600">{{ status }}</span>
                                        <span class="text-sm font-medium text-gray-800">{{ count }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Par priorité -->
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-2">{{ $page.props.translations.pages.dashboard.by_priority }}</h4>
                                <div class="space-y-2">
                                    <div
                                        v-for="(count, priority) in ticketStats.byPriority"
                                        :key="priority"
                                        class="flex justify-between items-center p-2 rounded transition-colors"
                                        :class="[getPriorityColor(priority)?.light || 'bg-gray-50']"
                                        @click="() => console.log('Priority:', priority, 'Color:', getPriorityColor(priority))"
                                    >
                                        <span :class="[getPriorityColor(priority)?.text || 'text-gray-600']" class="text-sm font-medium">
                                            {{ priority.charAt(0).toUpperCase() + priority.slice(1) }}
                                        </span>
                                        <span class="text-sm font-medium px-2 py-1 rounded-full" :class="[getPriorityColor(priority)?.bg || 'bg-gray-100', getPriorityColor(priority)?.text || 'text-gray-600']">
                                            {{ count }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
