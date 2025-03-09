<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, computed, watch } from 'vue';
import { useToast } from 'vue-toastification';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Pagination from '@/Components/Pagination.vue';
import { 
    BellIcon, 
    EnvelopeIcon, 
    DevicePhoneMobileIcon, 
    CheckCircleIcon, 
    XCircleIcon,
    EyeIcon,
    EyeSlashIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    logs: Object,
    filters: Object,
});

const toast = useToast();
const selectedChannel = ref(props.filters.channel || '');
const selectedNotificationType = ref(props.filters.notification_type_id || '');
const selectedReadStatus = ref(props.filters.is_read !== undefined ? props.filters.is_read : '');

// Surveiller les changements de filtres et mettre à jour l'URL
watch([selectedChannel, selectedNotificationType, selectedReadStatus], () => {
    router.get(route('profile.notifications.logs'), {
        channel: selectedChannel.value || undefined,
        notification_type_id: selectedNotificationType.value || undefined,
        is_read: selectedReadStatus.value !== '' ? selectedReadStatus.value : undefined,
    }, {
        preserveState: true,
        replace: true,
    });
});

// Marquer une notification comme lue
const markAsRead = (notificationId) => {
    router.post(route('profile.notifications.logs.read', notificationId), {}, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Notification marquée comme lue');
        },
    });
};

// Marquer toutes les notifications comme lues
const markAllAsRead = () => {
    router.post(route('profile.notifications.logs.read-all'), {
        channel: selectedChannel.value || undefined,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Toutes les notifications ont été marquées comme lues');
        },
    });
};

// Fonction pour obtenir l'icône du canal de notification
const getChannelIcon = (channel) => {
    switch (channel) {
        case 'in_app':
            return BellIcon;
        case 'email':
            return EnvelopeIcon;
        case 'sms':
            return DevicePhoneMobileIcon;
        default:
            return BellIcon;
    }
};

// Fonction pour obtenir la couleur du canal de notification
const getChannelColor = (channel) => {
    switch (channel) {
        case 'in_app':
            return 'text-blue-500';
        case 'email':
            return 'text-green-500';
        case 'sms':
            return 'text-purple-500';
        default:
            return 'text-gray-500';
    }
};

// Fonction pour obtenir l'icône du statut de la notification
const getStatusIcon = (status) => {
    return status === 'sent' ? CheckCircleIcon : XCircleIcon;
};

// Fonction pour obtenir la couleur du statut de la notification
const getStatusColor = (status) => {
    return status === 'sent' ? 'text-green-500' : 'text-red-500';
};

// Fonction pour formater la date
const formatDate = (dateString) => {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(date);
};

// Calculer le nombre de notifications non lues
const unreadCount = computed(() => {
    return props.logs.data.filter(log => !log.is_read).length;
});

// Vérifier s'il y a des notifications non lues
const hasUnread = computed(() => {
    return unreadCount.value > 0;
});
</script>

<template>
    <Head title="Journal des notifications" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Journal des notifications
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-8xl mx-auto sm:px-2 lg:px-2">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                            <div class="flex flex-col md:flex-row gap-4 mb-4 md:mb-0 w-full md:w-auto">
                                <!-- Filtre par canal -->
                                <div class="w-full md:w-auto">
                                    <select
                                        v-model="selectedChannel"
                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    >
                                        <option value="">Tous les canaux</option>
                                        <option value="in_app">In-App</option>
                                        <option value="email">Email</option>
                                        <option value="sms">SMS</option>
                                    </select>
                                </div>
                                
                                <!-- Filtre par statut de lecture -->
                                <div class="w-full md:w-auto">
                                    <select
                                        v-model="selectedReadStatus"
                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    >
                                        <option value="">Tous les statuts</option>
                                        <option :value="false">Non lues</option>
                                        <option :value="true">Lues</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="flex gap-2">
                                <SecondaryButton
                                    :href="route('profile.notifications.preferences')"
                                >
                                    Préférences
                                </SecondaryButton>
                                <PrimaryButton
                                    v-if="hasUnread"
                                    @click="markAllAsRead"
                                    class="whitespace-nowrap"
                                >
                                    Tout marquer comme lu
                                </PrimaryButton>
                            </div>
                        </div>

                        <div v-if="logs.data.length === 0" class="text-center py-8">
                            <p class="text-gray-500 dark:text-gray-400">Aucune notification trouvée</p>
                        </div>

                        <div v-else class="space-y-4">
                            <div v-for="log in logs.data" :key="log.id" 
                                :class="[
                                    'border rounded-lg p-4 transition-all',
                                    log.is_read 
                                        ? 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800' 
                                        : 'border-indigo-200 dark:border-indigo-800 bg-indigo-50 dark:bg-indigo-900/20'
                                ]"
                            >
                                <div class="flex justify-between items-start">
                                    <div class="flex items-start space-x-3">
                                        <component 
                                            :is="getChannelIcon(log.channel)" 
                                            class="h-6 w-6" 
                                            :class="getChannelColor(log.channel)" 
                                        />
                                        <div>
                                            <div class="flex items-center space-x-2">
                                                <h3 class="font-medium">{{ log.notification_type.name }}</h3>
                                                <span 
                                                    v-if="!log.is_read" 
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200"
                                                >
                                                    Nouveau
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ log.content }}</p>
                                            <div class="flex items-center mt-2 text-xs text-gray-500 dark:text-gray-400">
                                                <span>{{ formatDate(log.sent_at) }}</span>
                                                <span class="mx-2">•</span>
                                                <span class="capitalize">{{ log.channel }}</span>
                                                <span class="mx-2">•</span>
                                                <div class="flex items-center">
                                                    <component 
                                                        :is="getStatusIcon(log.status)" 
                                                        class="h-4 w-4 mr-1" 
                                                        :class="getStatusColor(log.status)" 
                                                    />
                                                    <span class="capitalize">{{ log.status }}</span>
                                                </div>
                                                <span v-if="log.error" class="ml-2 text-red-500">{{ log.error }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <button 
                                        v-if="!log.is_read"
                                        @click="markAsRead(log.id)" 
                                        class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300"
                                        title="Marquer comme lu"
                                    >
                                        <EyeIcon class="h-5 w-5" />
                                    </button>
                                    <EyeSlashIcon 
                                        v-else
                                        class="h-5 w-5 text-gray-400 dark:text-gray-600" 
                                        title="Déjà lu"
                                    />
                                </div>
                            </div>
                        </div>

                        <Pagination class="mt-6" :links="logs.links" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
