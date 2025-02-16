import { defineStore } from 'pinia';

export const useCompanyStore = defineStore('company', {
    state: () => ({
        logo: null,
    }),
    actions: {
        setLogo(logo) {
            this.logo = logo;
        },
    },
});
