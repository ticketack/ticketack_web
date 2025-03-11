import axios from 'axios';
window.axios = axios;
window.axios.defaults.withCredentials = true;
window.axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response && error.response.status === 401) {
            // Ne rediriger que si l'erreur ne vient pas d'une connexion Pusher
            if (!error.config.url.includes('broadcasting/auth')) {
                window.location.href = '/login';
            } else {
                console.warn('Erreur d\'authentification Pusher ignorée pour éviter une redirection');
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
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
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
