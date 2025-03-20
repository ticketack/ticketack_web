<template>
  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <h1 class="text-2xl font-bold mb-4">Test de Pusher</h1>
          
          <div class="mb-4">
            <p>État de la connexion: <span class="font-semibold" :class="connectionStatusClass">{{ connectionStatus }}</span></p>
          </div>
          
          <div class="mb-4">
            <button @click="sendTestNotification" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
              Envoyer une notification de test
            </button>
          </div>
          
          <div v-if="notifications.length > 0" class="mt-6">
            <h2 class="text-xl font-semibold mb-2">Notifications reçues:</h2>
            <ul class="border rounded-lg divide-y">
              <li v-for="(notification, index) in notifications" :key="index" class="p-3 hover:bg-gray-50">
                <p class="font-medium">{{ notification.content }}</p>
                <p class="text-sm text-gray-500">Reçue à {{ formatTime(notification.received_at) }}</p>
              </li>
            </ul>
          </div>
          
          <div class="mt-6">
            <h2 class="text-xl font-semibold mb-2">Logs:</h2>
            <div class="bg-gray-100 p-4 rounded-lg h-64 overflow-y-auto font-mono text-sm">
              <div v-for="(log, index) in logs" :key="index" class="mb-1" :class="{'text-green-600': log.type === 'success', 'text-red-600': log.type === 'error', 'text-yellow-600': log.type === 'warning'}">
                [{{ formatTime(log.time) }}] {{ log.message }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { format } from 'date-fns';
import { fr } from 'date-fns/locale';
import axios from 'axios';
import { usePage } from '@inertiajs/vue3';

const connectionStatus = ref('Non connecté');
const connectionStatusClass = ref('text-yellow-500');
const notifications = ref([]);
const logs = ref([]);
const userId = ref(null);
const channel = ref(null);

// Ajouter un log
const addLog = (message, type = 'info') => {
  logs.value.unshift({
    message,
    type,
    time: new Date()
  });
};

// Formater l'heure
const formatTime = (date) => {
  return format(new Date(date), 'HH:mm:ss', { locale: fr });
};

// Envoyer une notification de test
const sendTestNotification = async () => {
  try {
    addLog('Envoi d\'une notification de test...', 'info');
    const response = await axios.get('/test-pusher');
    addLog(`Notification envoyée avec succès: ${response.data.notification.content}`, 'success');
  } catch (error) {
    addLog(`Erreur lors de l'envoi de la notification: ${error.message}`, 'error');
    console.error('Erreur:', error);
  }
};

onMounted(() => {
  // Récupérer l'ID de l'utilisateur
  const page = usePage();
  if (page.props.auth && page.props.auth.user) {
    userId.value = page.props.auth.user.id;
    addLog(`Utilisateur connecté: ID ${userId.value}`, 'info');
  } else {
    addLog('Aucun utilisateur connecté', 'warning');
    return;
  }
  
  // Vérifier si Echo est disponible
  if (!window.Echo) {
    addLog('Echo n\'est pas disponible. Vérifiez que bootstrap.js est correctement chargé.', 'error');
    connectionStatus.value = 'Non disponible';
    connectionStatusClass.value = 'text-red-500';
    return;
  }
  
  addLog('Echo est disponible', 'success');
  
  // Vérifier les configurations Pusher
  const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY;
  const pusherCluster = import.meta.env.VITE_PUSHER_APP_CLUSTER;
  
  if (!pusherKey) {
    addLog('Clé Pusher non définie dans les variables d\'environnement', 'error');
    return;
  }
  
  addLog(`Configuration Pusher - Clé: ${pusherKey}, Cluster: ${pusherCluster}`, 'info');
  
  // Vérifier l'état de la connexion Pusher
  if (window.Echo.connector && window.Echo.connector.pusher) {
    connectionStatus.value = window.Echo.connector.pusher.connection.state;
    updateConnectionClass();
    
    addLog(`État initial de la connexion Pusher: ${connectionStatus.value}`, 'info');
    
    // Écouter les événements de connexion
    window.Echo.connector.pusher.connection.bind('connected', () => {
      connectionStatus.value = 'Connecté';
      updateConnectionClass();
      addLog('Pusher connecté avec succès!', 'success');
    });
    
    window.Echo.connector.pusher.connection.bind('connecting', () => {
      connectionStatus.value = 'Connexion en cours...';
      updateConnectionClass();
      addLog('Connexion à Pusher en cours...', 'info');
    });
    
    window.Echo.connector.pusher.connection.bind('disconnected', () => {
      connectionStatus.value = 'Déconnecté';
      updateConnectionClass();
      addLog('Déconnecté de Pusher', 'warning');
    });
    
    window.Echo.connector.pusher.connection.bind('error', (err) => {
      connectionStatus.value = 'Erreur';
      updateConnectionClass();
      addLog(`Erreur de connexion Pusher: ${JSON.stringify(err)}`, 'error');
    });
  } else {
    addLog('Connecteur Pusher non disponible', 'error');
    connectionStatus.value = 'Non disponible';
    connectionStatusClass.value = 'text-red-500';
  }
  
  // S'abonner au canal de notifications
  if (userId.value && window.Echo && typeof window.Echo.private === 'function') {
    addLog(`Abonnement au canal notifications.${userId.value}`, 'info');
    
    try {
      channel.value = window.Echo.private(`notifications.${userId.value}`);
      
      channel.value.listen('.new.notification', (e) => {
        addLog(`Notification reçue via Pusher: ${e.notification.content}`, 'success');
        notifications.value.unshift({
          ...e.notification,
          received_at: new Date()
        });
      });
      
      addLog('Abonnement au canal réussi', 'success');
    } catch (error) {
      addLog(`Erreur lors de l'abonnement au canal: ${error.message}`, 'error');
    }
  } else {
    addLog('Impossible de s\'abonner au canal de notifications', 'error');
  }
});

// Mettre à jour la classe CSS de l'état de connexion
const updateConnectionClass = () => {
  switch (connectionStatus.value) {
    case 'connected':
    case 'Connecté':
      connectionStatusClass.value = 'text-green-500';
      break;
    case 'connecting':
    case 'Connexion en cours...':
      connectionStatusClass.value = 'text-yellow-500';
      break;
    case 'disconnected':
    case 'Déconnecté':
      connectionStatusClass.value = 'text-orange-500';
      break;
    case 'error':
    case 'Erreur':
    case 'Non disponible':
      connectionStatusClass.value = 'text-red-500';
      break;
    default:
      connectionStatusClass.value = 'text-gray-500';
  }
};

onUnmounted(() => {
  // Se désabonner du canal
  if (channel.value) {
    try {
      channel.value.unsubscribe();
      addLog('Désabonnement du canal', 'info');
    } catch (error) {
      addLog(`Erreur lors du désabonnement: ${error.message}`, 'error');
    }
  }
  
  // Supprimer les écouteurs d'événements
  if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
    window.Echo.connector.pusher.connection.unbind('connected');
    window.Echo.connector.pusher.connection.unbind('connecting');
    window.Echo.connector.pusher.connection.unbind('disconnected');
    window.Echo.connector.pusher.connection.unbind('error');
  }
});
</script>
