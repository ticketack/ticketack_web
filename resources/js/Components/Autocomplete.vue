<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: null
    },
    placeholder: {
        type: String,
        default: ''
    },
    searchUrl: {
        type: String,
        required: true
    },
    minChars: {
        type: Number,
        default: 3
    }
});

const emit = defineEmits(['update:modelValue']);

const query = ref('');
const selectedItem = ref(null);
const suggestions = ref([]);
const isLoading = ref(false);
const showDropdown = ref(false);
const blurTimeout = ref(null);

// Initialiser la valeur si modelValue est défini
if (props.modelValue) {
    fetch(`${props.searchUrl}?id=${props.modelValue}`)
        .then(response => response.json())
        .then(data => {
            if (data.result) {
                selectedItem.value = data.result;
                query.value = data.result.text;
            }
        })
        .catch(error => console.error("Erreur lors de la récupération de l'utilisateur:", error));
}

const handleFocus = () => {
    if (blurTimeout.value) {
        window.clearTimeout(blurTimeout.value);
        blurTimeout.value = null;
    }
    showDropdown.value = true;
};

const handleBlur = () => {
    blurTimeout.value = window.setTimeout(() => {
        showDropdown.value = false;
        blurTimeout.value = null;
    }, 200);
};

const debouncedSearch = debounce(async (searchQuery) => {
    if (!searchQuery || searchQuery.length < props.minChars) {
        suggestions.value = [];
        showDropdown.value = false;
        return;
    }

    isLoading.value = true;
    try {
        const response = await fetch(`${props.searchUrl}?query=${encodeURIComponent(searchQuery)}`);
        const data = await response.json();
        suggestions.value = data.results || [];
        showDropdown.value = suggestions.value.length > 0;
    } catch (error) {
        console.error('Erreur lors de la recherche:', error);
        suggestions.value = [];
        showDropdown.value = false;
    } finally {
        isLoading.value = false;
    }
}, 300);

const selectItem = (item) => {
    selectedItem.value = item;
    query.value = item.text;
    emit('update:modelValue', item.id);
    showDropdown.value = false;
};

const clearSelection = () => {
    selectedItem.value = null;
    query.value = '';
    emit('update:modelValue', null);
    showDropdown.value = false;
};

watch(() => props.modelValue, (newValue) => {
    if (!newValue) {
        clearSelection();
    } else if (!selectedItem.value || selectedItem.value.id !== newValue) {
        // Charger les données de l'élément sélectionné
        fetch(`${props.searchUrl}?id=${newValue}`)
            .then(response => response.json())
            .then(data => {
                if (data.result) {
                    selectedItem.value = data.result;
                    query.value = data.result.text;
                }
            })
            .catch(error => console.error("Erreur lors de la récupération de l'utilisateur:", error));
    }
});

watch(query, (newValue) => {
    if (!selectedItem.value || newValue !== selectedItem.value.text) {
        debouncedSearch(newValue);
    }
});
</script>

<template>
    <div class="relative">
        <div class="relative">
            <input
                type="text"
                v-model="query"
                :placeholder="placeholder"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                @focus="handleFocus"
                @blur="handleBlur"
            >
            <div v-if="selectedItem || query" class="absolute inset-y-0 right-0 flex items-center pr-2">
                <button
                    @click="clearSelection"
                    type="button"
                    class="text-gray-400 hover:text-gray-500 focus:outline-none"
                >
                    <span class="sr-only">Effacer</span>
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>

        <div v-if="showDropdown && suggestions.length > 0" class="absolute z-10 mt-1 w-full rounded-md bg-white shadow-lg">
            <ul class="max-h-60 overflow-auto py-1" role="listbox">
                <li
                    v-for="item in suggestions"
                    :key="item.id"
                    class="relative cursor-pointer select-none py-2 pl-3 pr-9 hover:bg-indigo-600 hover:text-white"
                    @mousedown="selectItem(item)"
                    role="option"
                >
                    {{ item.text }}
                </li>
            </ul>
        </div>

        <div v-if="isLoading" class="absolute right-0 top-0 mr-3 mt-2">
            <svg class="h-5 w-5 animate-spin text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    </div>
</template>
