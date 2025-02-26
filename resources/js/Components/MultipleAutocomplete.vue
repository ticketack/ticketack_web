<template>
    <div class="relative">
        <input
            type="text"
            :placeholder="placeholder"
            v-model="query"
            @input="debouncedSearch"
            @focus="showResults = true"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
        />
        <div v-if="showResults && results.length > 0" class="absolute z-10 w-full mt-1 bg-white rounded-md shadow-lg">
            <ul class="py-1 overflow-auto text-base leading-6 rounded-md shadow-xs max-h-60 focus:outline-none sm:text-sm sm:leading-5">
                <li
                    v-for="result in results"
                    :key="result.id"
                    @click="selectResult(result)"
                    class="relative py-2 pl-3 text-gray-900 cursor-pointer select-none hover:bg-indigo-600 hover:text-white"
                    :class="{ 'bg-gray-100': selectedIds.includes(result.id) }"
                >
                    {{ result.name }}
                    <span v-if="selectedIds.includes(result.id)" class="absolute inset-y-0 right-0 flex items-center pr-4">
                        <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </span>
                </li>
            </ul>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue';
import debounce from 'lodash/debounce';
import axios from 'axios';

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => []
    },
    searchUrl: {
        type: String,
        required: true
    },
    placeholder: {
        type: String,
        default: 'Rechercher...'
    }
});

const emit = defineEmits(['update:modelValue']);

const query = ref('');
const results = ref([]);
const showResults = ref(false);
const selectedIds = ref(props.modelValue || []);

watch(() => props.modelValue, (newValue) => {
    selectedIds.value = newValue;
}, { deep: true });

const search = async () => {
    if (!query.value) {
        results.value = [];
        return;
    }

    try {
        const response = await axios.get(props.searchUrl, {
            params: { q: query.value }
        });
        results.value = response.data.results;
    } catch (error) {
        console.error('Erreur lors de la recherche:', error);
        results.value = [];
    }
};

const debouncedSearch = debounce(search, 300);

const selectResult = (result) => {
    const index = selectedIds.value.indexOf(result.id);
    if (index === -1) {
        selectedIds.value.push(result.id);
    } else {
        selectedIds.value.splice(index, 1);
    }
    emit('update:modelValue', selectedIds.value);
    query.value = '';
    showResults.value = false;
};

// Réinitialiser les résultats quand on clique en dehors
const handleClickOutside = (event) => {
    if (!event.target.closest('.relative')) {
        showResults.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>
