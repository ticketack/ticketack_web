import { ref } from 'vue';

const logo = ref(null);

export function setLogo(newLogo) {
    logo.value = newLogo || null;
}

export function useLogo() {
    return { logo };
}

// Initialiser le logo avec la valeur des props Inertia
export function initLogo(initialLogo) {
    logo.value = initialLogo || null;
}
