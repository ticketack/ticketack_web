import axios from 'axios';
window.axios = axios;
window.axios.defaults.withCredentials = true;

// Fonction pour obtenir la valeur d'un cookie
function getCookie(name) {
    const match = document.cookie.match(new RegExp('(^|;\\s*)(' + name + ')=([^;]*)'));
    return match ? match[3] : null;
}

// Ajouter les jetons CSRF à chaque requête
window.axios.interceptors.request.use(config => {
    // Ajouter le jeton CSRF aux headers
    const token = document.querySelector('meta[name="csrf-token"]');
    if (token) {
        config.headers['X-CSRF-TOKEN'] = token.content;
        
        // Ajouter également le jeton XSRF pour Sanctum
        const xsrfToken = getCookie('XSRF-TOKEN');
        if (xsrfToken) {
            config.headers['X-XSRF-TOKEN'] = decodeURIComponent(xsrfToken);
        }
    }
    return config;
});

// Gérer les réponses et intercepter les erreurs 401
window.axios.interceptors.response.use(
    response => response,
    error => {
        // Log pour déboguer en production
        console.error(`Axios error: ${error.config?.url} - Status: ${error.response?.status}`);
        
        if (error.response && error.response.status === 401) {
            // Ignorer les erreurs 401 de ces endpoints spécifiques
            if (error.config.url.includes('broadcasting/auth') || 
                error.config.url.includes('notifications/count') ||
                error.config.url.includes('api/notifications')) {
                console.warn(`Erreur 401 ignorée pour: ${error.config.url}`);
            } else {
                // Pour toutes les autres requêtes, rediriger vers login
                window.location.href = '/login';
            }
        }
        return Promise.reject(error);
    }
);

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Import Echo
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Vérifier si les variables d'environnement Pusher sont disponibles
const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY;
const pusherCluster = import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'eu';

// Récupérer préventivement un cookie CSRF au démarrage pour Sanctum
(async function() {
    try {
        await axios.get('/sanctum/csrf-cookie');
        console.log('Cookie CSRF actualisé pour Sanctum');
    } catch (error) {
        console.warn('Impossible d\'actualiser le cookie CSRF:', error);
    }
})();

// Ne créer Echo que si la clé Pusher est définie
if (pusherKey) {
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: pusherKey,
        cluster: pusherCluster,
        wsHost: import.meta.env.VITE_PUSHER_HOST ? import.meta.env.VITE_PUSHER_HOST : `ws-${pusherCluster}.pusher.com`,
        wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
        wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
        forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
        // Ajouter les informations d'authentification CSRF
        auth: {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'X-XSRF-TOKEN': decodeURIComponent(getCookie('XSRF-TOKEN') || '')
            }
        },
    });
    
    // Ajouter une gestion d'erreur explicite pour Pusher
    try {
        window.Echo.connector.pusher.connection.bind('error', (err) => {
            console.error('Erreur de connexion Pusher interceptée:', err);
            // Ne pas laisser cette erreur déclencher d'autres actions
        });
    } catch (e) {
        console.warn('Impossible d\'ajouter un gestionnaire d\'erreur à Pusher:', e);
    }
} else {
    console.warn('Pusher configuration is missing. Real-time notifications will not work.');
    // Créer un Echo factice pour éviter les erreurs
    window.Echo = {
        private: () => ({
            listen: () => {}
        }),
        leave: () => {},
        channel: () => ({
            listen: () => {}
        }),
        join: () => ({
            listen: () => {}
        })
    };
}