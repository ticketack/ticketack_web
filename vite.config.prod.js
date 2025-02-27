import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { resolve } from 'path';

export default defineConfig({
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
        },
        rollupOptions: {
            external: [
                // Lister ici les modules qui posent problème
            ],
            output: {
                manualChunks: {
                    'fullcalendar': [
                        '@fullcalendar/core',
                        '@fullcalendar/daygrid',
                        '@fullcalendar/timegrid',
                        '@fullcalendar/list',
                        '@fullcalendar/interaction',
                        '@fullcalendar/vue3'
                    ]
                }
            }
        }
    }
});
