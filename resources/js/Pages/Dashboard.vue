<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { getPriorityColor } from '@/Utils/ticketPriorityColors';
import { getStatusColor } from '@/Utils/ticketStatusColors';
import TicketsChart from '@/Components/Dashboard/TicketsChart.vue';

defineProps({
    equipmentCount: {
        type: Number,
        required: true
    },
    userCount: {
        type: Number,
        required: true
    },
    avgTimePerTicket: {
        type: Number,
        required: true
    },
    mostTickets: {
        type: Array,
        required: true
    },
    leastTickets: {
        type: Array,
        required: true
    },
    ticketStats: {
        type: Object,
        required: true
    },
    chartData: {
        type: Array,
        required: true
    },
    usersWithMostAssignedTickets: {
        type: Array,
        required: true
    },
    usersWithMostCreatedTickets: {
        type: Array,
        required: true
    },
    usersWithMostResolvedTickets: {
        type: Array,
        required: true
    },
    topEquipmentsByTime: {
        type: Array,
        required: true
    }
});
</script>

<template>
    <Head :title="$page.props.translations.dashboard.title" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                {{ $page.props.translations.dashboard.title }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-8xl mx-auto sm:px-2 lg:px-2">
                <div class="grid grid-cols-4 gap-6">
                    <!-- Panneau Statistiques -->
                    <div class="overflow-hidden bg-white shadow-lg rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ $page.props.translations.dashboard.statistics }}</h3>
                            <div class="flex items-center space-x-4">
                                <div class="p-3 bg-blue-100 rounded-full">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">{{ $page.props.translations.dashboard.total_equipment }}</p>
                                    <p class="text-2xl font-bold text-gray-800">{{ equipmentCount }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4 mt-4">
                                <div class="p-3 bg-green-100 rounded-full">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">{{ $page.props.translations.dashboard.total_users }}</p>
                                    <p class="text-2xl font-bold text-gray-800">{{ userCount }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-4 mt-4">
                                <div class="p-3 bg-amber-100 rounded-full">
                                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">{{ $page.props.translations.dashboard.avg_time_per_ticket }}</p>
                                    <p class="text-2xl font-bold text-gray-800">{{ Math.floor(avgTimePerTicket / 60) }}h {{ avgTimePerTicket % 60 }}min</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistiques des tickets -->
                    <div class="overflow-hidden bg-white shadow-lg rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ $page.props.translations.dashboard.ticket_stats }}</h3>
                            
                            <!-- Total des tickets -->
                            <div class="flex items-center space-x-4 mb-6">
                                <div class="p-3 bg-purple-100 rounded-full">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">{{ $page.props.translations.dashboard.total_tickets }}</p>
                                    <p class="text-2xl font-bold text-gray-800">{{ ticketStats.total }}</p>
                                    <div class="mt-1 text-xs text-gray-500">
                                        <p>{{ $page.props.translations.dashboard.active_tickets || 'Dont tickets actifs' }}: <span class="font-medium">{{ ticketStats.active }}</span></p>
                                        <p>{{ $page.props.translations.dashboard.archived_tickets || 'Et tickets archivés' }}: <span class="font-medium">{{ ticketStats.archived }}</span></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Par statut -->
                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">{{ $page.props.translations.dashboard.by_status }}</h4>
                                <div class="space-y-2">
                                    <div v-for="(count, status) in ticketStats.byStatus" :key="status" class="flex justify-between items-center bg-gray-50 p-2 rounded">
                                        <span class="text-sm text-gray-600">{{ status }}</span>
                                        <span class="text-sm font-medium text-gray-800">{{ count }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Par priorité -->
                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">{{ $page.props.translations.dashboard.by_priority }}</h4>
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
                                <!-- Par catégorie -->
                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">{{ $page.props.translations.dashboard.by_category }}</h4>
                                <div class="space-y-2">
                                        <div v-for="(count, category) in ticketStats.byCategory" :key="category" 
                                             class="flex justify-between items-center p-2 rounded-lg bg-indigo-50">
                                            <span class="text-sm font-medium text-gray-800">{{ category }}</span>
                                            <span class="px-3 py-1 bg-indigo-100 rounded-full text-sm font-medium text-indigo-800">{{ count }}</span>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Panneau Statistiques des utilisateurs -->
                    <div class="overflow-hidden bg-white shadow-lg rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ $page.props.translations.dashboard.user_stats }}</h3>
                            
                            <!-- Utilisateurs avec le plus de tickets assignés -->
                            <div class="mb-6">
                                <div class="flex items-center space-x-4 mb-4">
                                    <div class="p-3 bg-indigo-100 rounded-full">
                                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">{{ $page.props.translations.dashboard.most_assigned_tickets }}</p>
                                    </div>
                                </div>
                                <div class="space-y-3">
                                    <div v-for="user in usersWithMostAssignedTickets" :key="user.id" 
                                         class="flex items-center justify-between p-3 bg-indigo-50 rounded-lg">
                                        <div>
                                            <p class="text-sm font-medium text-gray-800">{{ user.name }}</p>
                                        </div>
                                        <div class="px-3 py-1 bg-indigo-100 rounded-full">
                                            <span class="text-sm font-medium text-indigo-800">{{ user.ticket_count }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Utilisateurs avec le plus de tickets créés -->
                            <div>
                                <div class="flex items-center space-x-4 mb-4">
                                    <div class="p-3 bg-purple-100 rounded-full">
                                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">{{ $page.props.translations.dashboard.most_created_tickets }}</p>
                                    </div>
                                </div>
                                <div class="space-y-3">
                                    <div v-for="user in usersWithMostCreatedTickets" :key="user.id" 
                                         class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                                        <div>
                                            <p class="text-sm font-medium text-gray-800">{{ user.name }}</p>
                                        </div>
                                        <div class="px-3 py-1 bg-purple-100 rounded-full">
                                            <span class="text-sm font-medium text-purple-800">{{ user.ticket_count }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Utilisateurs avec le plus de tickets résolus -->
                            <div class="mt-6">
                                <div class="flex items-center space-x-4 mb-4">
                                    <div class="p-3 bg-green-100 rounded-full">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">{{ $page.props.translations.dashboard.most_resolved_tickets }}</p>
                                    </div>
                                </div>
                                <div class="space-y-3">
                                    <div v-for="user in usersWithMostResolvedTickets" :key="user.id" 
                                         class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                        <div>
                                            <p class="text-sm font-medium text-gray-800">{{ user.name }}</p>
                                        </div>
                                        <div class="px-3 py-1 bg-green-100 rounded-full">
                                            <span class="text-sm font-medium text-green-800">{{ user.ticket_count }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Panneau Statistiques d'équipements -->
                    <div class="overflow-hidden bg-white shadow-lg rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ $page.props.translations.dashboard.equipment_stats }}</h3>
                            
                            <!-- Équipements avec le plus de tickets -->
                            <div class="mb-6">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">{{ $page.props.translations.dashboard.most_tickets }}</h4>
                                <div class="space-y-3">
                                    <div v-for="equipment in mostTickets" :key="equipment.id" 
                                         class="flex items-center justify-between p-3 bg-orange-50 rounded-lg transition-colors duration-200">
                                        <div>
                                            <p class="text-sm font-medium text-gray-800">{{ equipment.designation }}</p>
                                            <p class="text-xs text-gray-500">{{ equipment.marque }} - {{ equipment.modele }}</p>
                                        </div>
                                        <div class="px-3 py-1 bg-orange-100 rounded-full">
                                            <span class="text-sm font-medium text-orange-800">{{ equipment.ticket_count }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Équipements avec le moins de tickets -->
                            <div class="mb-6">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">{{ $page.props.translations.dashboard.least_tickets }}</h4>
                                <div class="space-y-3">
                                    <div v-for="equipment in leastTickets" :key="equipment.id" 
                                         class="flex items-center justify-between p-3 bg-green-50 rounded-lg transition-colors duration-200">
                                        <div>
                                            <p class="text-sm font-medium text-gray-800">{{ equipment.designation }}</p>
                                            <p class="text-xs text-gray-500">{{ equipment.marque }} - {{ equipment.modele }}</p>
                                        </div>
                                        <div class="px-3 py-1 bg-green-100 rounded-full">
                                            <span class="text-sm font-medium text-green-800">{{ equipment.ticket_count }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Top 3 des équipements par temps passé -->
                            <div class="mb-6">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">{{ $page.props.translations.dashboard.top_3_equipments_by_time }}</h4>
                                <div class="space-y-3">
                                    <div v-for="equipment in topEquipmentsByTime" :key="equipment.id" 
                                        class="flex items-center justify-between p-3 bg-teal-50 rounded-lg">
                                        <div>
                                            <p class="text-sm font-medium text-gray-800">{{ equipment.name }}</p>
                                        </div>
                                        <!-- Ajouter ce code juste au-dessus de votre boucle d'affichage -->

                                        <div class="px-3 py-1 bg-teal-100 rounded-full">
                                            <span class="text-sm font-medium text-teal-800">
                                                {{ Math.floor(equipment.total_time / 60) }}h {{ equipment.total_time % 60 }}min
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>                            
                        </div>
                    </div>
                </div>
                <!-- Graphique des tickets -->
                <div class="mt-6 overflow-hidden bg-white shadow-lg rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ $page.props.translations.dashboard.tickets_over_time }}</h3>
                        <TicketsChart :data="chartData" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
