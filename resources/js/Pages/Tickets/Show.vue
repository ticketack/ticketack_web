<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Ticket #{{ ticket.id }} - {{ ticket.title }}
                </h2>
                <Link :href="route('tickets.index')" class="text-gray-600 hover:text-gray-900">
                    Retour à la liste
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- En-tête du ticket -->
                        <div class="flex flex-col md:flex-row justify-between gap-4 mb-6">
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium">{{ ticket.title }}</h3>
                                <div class="flex flex-wrap gap-2">
                                    <TicketStatus :status="ticket.status" />
                                    <TicketPriority :priority="ticket.priority" />
                                    <span class="px-2 py-1 text-xs font-medium rounded-full"
                                        :style="{ backgroundColor: ticket.category.color + '20', color: ticket.category.color }">
                                        {{ ticket.category.name }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="space-y-2 text-sm text-gray-500">
                                <p>Créé par : {{ ticket.creator.name }}</p>
                                <p>Assigné à : {{ ticket.assignee?.name || 'Non assigné' }}</p>
                                <p>Créé le : {{ formatDate(ticket.created_at) }}</p>
                                <p v-if="ticket.due_date">Échéance : {{ formatDate(ticket.due_date) }}</p>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mt-6">
                            <h4 class="text-sm font-medium text-gray-900">Description</h4>
                            <div class="mt-2 text-sm text-gray-700 whitespace-pre-wrap">
                                {{ ticket.description }}
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="mt-6 flex flex-wrap gap-4">
                            <div v-if="$page.props.permissions['tickets.edit']">
                                <InputLabel for="status" value="Changer le statut" class="mb-1" />
                                <select
                                    id="status"
                                    v-model="form.status_id"
                                    @change="updateStatus"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                >
                                    <option v-for="status in statuses" :key="status.id" :value="status.id">
                                        {{ status.name }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Timeline -->
                        <div class="mt-8">
                            <h4 class="text-sm font-medium text-gray-900 mb-4">Historique</h4>
                            <Timeline :logs="ticket.logs" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import TicketStatus from '@/Components/Tickets/TicketStatus.vue';
import TicketPriority from '@/Components/Tickets/TicketPriority.vue';
import Timeline from '@/Components/Tickets/Timeline.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { format } from 'date-fns';
import { fr } from 'date-fns/locale';

const props = defineProps({
    ticket: {
        type: Object,
        required: true
    },
    statuses: {
        type: Array,
        required: true
    }
});

const form = useForm({
    status_id: props.ticket.status.id
});

const formatDate = (date) => {
    return format(new Date(date), "d MMMM yyyy 'à' HH:mm", { locale: fr });
};

const updateStatus = () => {
    form.put(route('tickets.update', props.ticket.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Recharger la page pour voir les changements
            window.location.reload();
        }
    });
};
</script>
