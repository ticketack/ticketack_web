import { createI18n } from 'vue-i18n';

const messages = {
    fr: {
        pages: window.translations?.fr?.pages || {}
    },
    en: {
        pages: window.translations?.en?.pages || {}
    },
    de: {
        pages: window.translations?.de?.pages || {}
    }
};

const i18n = createI18n({
    legacy: false,
    globalInjection: true,
    locale: document.documentElement.lang || 'fr',
    fallbackLocale: 'fr',
    messages
});

export default i18n;
