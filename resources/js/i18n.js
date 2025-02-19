import { createI18n } from 'vue-i18n';

const i18n = createI18n({
    legacy: false,
    globalInjection: true,
    locale: document.documentElement.lang || 'fr',
    fallbackLocale: 'fr',
    messages: {
        fr: {
            pages: {}
        },
        en: {
            pages: {}
        },
        de: {
            pages: {}
        }
    }
});

// Mettre à jour les messages depuis les props Inertia
export function updateMessages(translations) {
    if (!translations) return;
    
    const locale = document.documentElement.lang || 'fr';
    i18n.global.setLocaleMessage(locale, {
        pages: translations.pages || {}
    });
}

export default i18n;
