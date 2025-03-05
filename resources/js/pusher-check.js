// Script de diagnostic pour vérifier la configuration de Pusher
console.log('=== Diagnostic Pusher ===');
console.log('VITE_PUSHER_APP_KEY:', import.meta.env.VITE_PUSHER_APP_KEY || 'Non défini');
console.log('VITE_PUSHER_APP_CLUSTER:', import.meta.env.VITE_PUSHER_APP_CLUSTER || 'Non défini');
console.log('window.Echo disponible:', typeof window.Echo !== 'undefined');
if (window.Echo) {
    console.log('window.Echo.private est une fonction:', typeof window.Echo.private === 'function');
}
console.log('window.Laravel disponible:', typeof window.Laravel !== 'undefined');
if (window.Laravel) {
    console.log('window.Laravel.user:', window.Laravel.user);
}
console.log('=== Fin du diagnostic ===');
