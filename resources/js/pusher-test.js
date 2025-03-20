// Script de test pour vérifier la connexion Pusher
console.log('Démarrage du test Pusher...');

// Vérifier si Echo est disponible
if (window.Echo) {
    console.log('Echo est disponible');
    
    // Vérifier les configurations Pusher
    console.log('Pusher Key:', import.meta.env.VITE_PUSHER_APP_KEY);
    console.log('Pusher Cluster:', import.meta.env.VITE_PUSHER_APP_CLUSTER);
    
    // Vérifier l'état de la connexion Pusher
    if (window.Echo.connector && window.Echo.connector.pusher) {
        console.log('État de la connexion Pusher:', window.Echo.connector.pusher.connection.state);
        
        // Écouter les événements de connexion
        window.Echo.connector.pusher.connection.bind('connected', () => {
            console.log('Pusher connecté avec succès!');
        });
        
        window.Echo.connector.pusher.connection.bind('error', (err) => {
            console.error('Erreur de connexion Pusher:', err);
        });
    } else {
        console.error('Connecteur Pusher non disponible');
    }
} else {
    console.error('Echo n\'est pas disponible. Vérifiez que bootstrap.js est correctement chargé.');
}

// Tester l'écoute d'un canal
if (window.Echo && typeof window.Echo.private === 'function') {
    const userId = document.querySelector('meta[name="user-id"]')?.getAttribute('content');
    
    if (userId) {
        console.log('Test d\'écoute sur le canal notifications.' + userId);
        
        window.Echo.private(`notifications.${userId}`)
            .listen('.new.notification', (e) => {
                console.log('Notification reçue via Pusher:', e);
            });
    } else {
        console.warn('ID utilisateur non disponible. Impossible de s\'abonner au canal de notifications.');
    }
} else {
    console.error('Echo.private n\'est pas une fonction');
}
