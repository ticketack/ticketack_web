import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { resolve } from 'path';
import { loadEnv } from 'vite';

export default defineConfig(({ mode }) => {
    // Charger les variables d'environnement en fonction du mode
    const env = loadEnv(mode, process.cwd());
    
    return {
        plugins: [
            laravel({
                input: 'resources/js/app.js',
                refresh: true,
            }),
            vue({
                template: {
                    transformAssetUrls: {
                        base: null,
                        includeAbsolute: false,
                    },
                },
            }),
        ],
        resolve: {
            alias: {
                '@fullcalendar/core': resolve(__dirname, 'node_modules/@fullcalendar/core'),
                '@fullcalendar/daygrid': resolve(__dirname, 'node_modules/@fullcalendar/daygrid'),
                '@fullcalendar/timegrid': resolve(__dirname, 'node_modules/@fullcalendar/timegrid'),
                '@fullcalendar/list': resolve(__dirname, 'node_modules/@fullcalendar/list'),
                '@fullcalendar/interaction': resolve(__dirname, 'node_modules/@fullcalendar/interaction'),
                '@fullcalendar/vue3': resolve(__dirname, 'node_modules/@fullcalendar/vue3'),
            }
        },
        optimizeDeps: {
            include: [
                '@fullcalendar/core',
                '@fullcalendar/daygrid',
                '@fullcalendar/timegrid',
                '@fullcalendar/list',
                '@fullcalendar/interaction',
                '@fullcalendar/vue3'
            ]
        },
        build: {
            commonjsOptions: {
                include: [/node_modules/]
            }
        },
        // Exposer les variables d'environnement Pusher à l'application
        define: {
            'import.meta.env.VITE_PUSHER_APP_KEY': JSON.stringify(env.VITE_PUSHER_APP_KEY || process.env.VITE_PUSHER_APP_KEY),
            'import.meta.env.VITE_PUSHER_APP_CLUSTER': JSON.stringify(env.VITE_PUSHER_APP_CLUSTER || process.env.VITE_PUSHER_APP_CLUSTER),
            'import.meta.env.VITE_PUSHER_HOST': JSON.stringify(env.VITE_PUSHER_HOST || process.env.VITE_PUSHER_HOST),
            'import.meta.env.VITE_PUSHER_PORT': JSON.stringify(env.VITE_PUSHER_PORT || process.env.VITE_PUSHER_PORT),
            'import.meta.env.VITE_PUSHER_SCHEME': JSON.stringify(env.VITE_PUSHER_SCHEME || process.env.VITE_PUSHER_SCHEME),
        }
    };
});
