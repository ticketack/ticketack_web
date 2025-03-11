<script setup>
import { ref, onMounted, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { BellIcon } from '@heroicons/vue/24/outline';
import { BellAlertIcon } from '@heroicons/vue/24/solid';
import axios from 'axios';

const props = defineProps({
    initialUnreadCount: {
        type: Number,
        default: 0
    }
});

const unreadCount = ref(props.initialUnreadCount);
const showDropdown = ref(false);
const recentNotifications = ref([]);
const loading = ref(false);

// Fermer le dropdown quand on clique ailleurs
const closeDropdown = (e) => {
    if (!e.target.closest('.notification-dropdown')) {
        showDropdown.value = false;
    }
};

// Charger les notifications récentes
const loadRecentNotifications = async () => {
    if (loading.value) return;
    
    loading.value = true;
    try {
        const response = await axios.get(route('api.notifications.recent'));
        recentNotifications.value = response.data.notifications;
    } catch (error) {
        console.error('Erreur lors du chargement des notifications récentes', error);
    } finally {
        loading.value = false;
    }
};

// Marquer toutes les notifications comme lues
const markAllAsRead = () => {
    router.post(route('profile.notifications.logs.read-all'), {}, {
        preserveScroll: true,
        onSuccess: () => {
            unreadCount.value = 0;
            recentNotifications.value = recentNotifications.value.map(notif => ({
                ...notif,
                is_read: true
            }));
        },
    });
};

// Marquer une notification comme lue
const markAsRead = (notificationId) => {
    // Fermer le dropdown pour éviter l'effet de mise en abîme
    showDropdown.value = false;
    
    router.post(route('profile.notifications.logs.read', notificationId), {}, {
        preserveScroll: true,
        onSuccess: () => {
            const notification = recentNotifications.value.find(n => n.id === notificationId);
            if (notification && !notification.is_read) {
                notification.is_read = true;
                unreadCount.value--;
            }
        },
    });
};

// Formater la date relative
const formatRelativeTime = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);
    
    if (diffInSeconds < 60) {
        return 'À l\'instant';
    } else if (diffInSeconds < 3600) {
        const minutes = Math.floor(diffInSeconds / 60);
        return `Il y a ${minutes} minute${minutes > 1 ? 's' : ''}`;
    } else if (diffInSeconds < 86400) {
        const hours = Math.floor(diffInSeconds / 3600);
        return `Il y a ${hours} heure${hours > 1 ? 's' : ''}`;
    } else {
        const days = Math.floor(diffInSeconds / 86400);
        return `Il y a ${days} jour${days > 1 ? 's' : ''}`;
    }
};

// Obtenir l'icône du canal de notification
const getChannelIcon = (channel) => {
    switch (channel) {
        case 'in_app':
            return 'bell';
        case 'email':
            return 'envelope';
        case 'sms':
            return 'phone';
        default:
            return 'bell';
    }
};

// Obtenir la couleur du canal de notification
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

// Vérifier s'il y a des notifications non lues
const hasUnread = computed(() => unreadCount.value > 0);

onMounted(async () => {
    // S'assurer d'avoir un cookie CSRF valide avant de faire des requêtes
    try {
        await axios.get('/sanctum/csrf-cookie');
        // Continuez avec l'initialisation normale...
    } catch (error) {
        console.error('Erreur lors de la récupération du cookie CSRF', error);
    }
    document.addEventListener('click', closeDropdown);
    
    // Mettre à jour le compteur de notifications toutes les 30 secondes
    const interval = setInterval(async () => {
        try {
            const response = await axios.get(route('api.notifications.count'));
            unreadCount.value = response.data.count;
        } catch (error) {
            console.error('Erreur lors de la mise à jour du compteur de notifications', error);
        }
    }, 30000);
    
    // Écouter les nouvelles notifications en temps réel
    if (window.Echo && typeof window.Echo.private === 'function') {
        const userId = window.Laravel?.user?.id || usePage().props.laravel?.user?.id;
        if (userId) {
            window.Echo.private(`notifications.${userId}`)
                .listen('.new.notification', (data) => {
                // Incrémenter le compteur de notifications non lues
                unreadCount.value++;
                
                // Ajouter la notification à la liste des notifications récentes
                if (recentNotifications.value.length > 0) {
                    recentNotifications.value = [data.notification, ...recentNotifications.value.slice(0, 9)];
                }
                
                // Afficher une notification du navigateur si autorisé
                if (Notification.permission === 'granted') {
                    const notification = new Notification('Nouvelle notification', {
                        body: data.notification.content,
                        icon: '/favicon.ico'
                    });
                    
                    notification.onclick = () => {
                        window.focus();
                        notification.close();
                    };
                }
            });
        }
    }
    
    // Demander la permission pour les notifications du navigateur
    if (Notification.permission !== 'granted' && Notification.permission !== 'denied') {
        Notification.requestPermission();
    }
    
    return () => {
        document.removeEventListener('click', closeDropdown);
        clearInterval(interval);
        
        // Se désabonner du canal de notification
        if (window.Echo && typeof window.Echo.leave === 'function') {
            const userId = window.Laravel?.user?.id || usePage().props.laravel?.user?.id;
            if (userId) {
                window.Echo.leave(`notifications.${userId}`);
            }
        }
    };
});
</script>

<template>
    <div class="notification-dropdown relative">
        <!-- Icône de notification avec badge -->
        <button 
            @click="showDropdown = !showDropdown; if (showDropdown) loadRecentNotifications();" 
            class="relative p-1 rounded-full text-gray-500 hover:text-gray-700 focus:outline-none"
        >
            <BellAlertIcon v-if="hasUnread" class="h-6 w-6 text-indigo-500" />
            <BellIcon v-else class="h-6 w-6" />
            
            <!-- Badge de notification -->
            <span 
                v-if="hasUnread" 
                class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full"
            >
                {{ unreadCount > 99 ? '99+' : unreadCount }}
            </span>
        </button>
        
        <!-- Dropdown des notifications récentes -->
        <div 
            v-if="showDropdown" 
            class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg overflow-hidden z-50"
        >
            <div class="py-2 px-3 bg-gray-100 border-b flex justify-between items-center">
                <h3 class="text-sm font-semibold">Notifications</h3>
                <div class="flex space-x-2">
                    <button 
                        v-if="hasUnread"
                        @click="markAllAsRead" 
                        class="text-xs text-indigo-600 hover:text-indigo-800"
                    >
                        Tout marquer comme lu
                    </button>
                    <Link 
                        :href="route('profile.notifications.logs')" 
                        class="text-xs text-gray-600 hover:text-gray-800"
                    >
                        Voir tout
                    </Link>
                </div>
            </div>
            
            <div class="max-h-96 overflow-y-auto">
                <div v-if="loading" class="py-4 text-center text-gray-500">
                    Chargement...
                </div>
                <div v-else-if="recentNotifications.length === 0" class="py-4 text-center text-gray-500">
                    Aucune notification récente
                </div>
                <div v-else>
                    <div 
                        v-for="notification in recentNotifications" 
                        :key="notification.id"
                        :class="[
                            'p-3 border-b last:border-b-0 hover:bg-gray-50 transition-colors cursor-pointer',
                            { 'bg-indigo-50': !notification.is_read }
                        ]"
                        @click.stop="markAsRead(notification.id)"
                    >
                        <div class="flex items-start">
                            <!-- Icône du canal -->
                            <div :class="['flex-shrink-0 mr-3', getChannelColor(notification.channel)]">
                                <i :class="['fas', `fa-${getChannelIcon(notification.channel)}`]"></i>
                            </div>
                            
                            <!-- Contenu de la notification -->
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900 font-medium truncate">
                                    {{ notification.notification_type_name }}
                                </p>
                                <p class="text-sm text-gray-700 truncate">
                                    {{ notification.content }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ formatRelativeTime(notification.sent_at) }}
                                </p>
                            </div>
                            
                            <!-- Indicateur de non-lu -->
                            <div v-if="!notification.is_read" class="ml-2 flex-shrink-0">
                                <span class="inline-block w-2 h-2 bg-indigo-500 rounded-full"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
