import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import i18n, { updateMessages } from './i18n';


const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        // Configuration de base
        app.use(plugin);
        app.use(ZiggyVue);
        app.use(i18n);

        // Initialiser les traductions
        if (props.translations) {
            updateMessages(props.translations);
        }

        // Monter l'application
        return app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
