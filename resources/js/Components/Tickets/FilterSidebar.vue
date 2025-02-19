<template>
    <div class="fixed inset-y-0 right-0 w-80 bg-white shadow-lg transform transition-transform duration-300"
        :class="{ 'translate-x-0': modelValue, 'translate-x-full': !modelValue }">
        
        <div class="h-full flex flex-col">
            <!-- Header -->
            <div class="p-4 border-b flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Filtres</h3>
                <button @click="$emit('update:modelValue', false)" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="flex-1 overflow-y-auto p-4 space-y-4">
                <!-- Statut -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select v-model="filters.status_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Tous les statuts</option>
                        <option v-for="status in statuses" :key="status.id" :value="status.id">{{ status.name }}</option>
                    </select>
                </div>

                <!-- Priorité -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Priorité</label>
                    <select v-model="filters.priority" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Toutes les priorités</option>
                        <option value="low">Basse</option>
                        <option value="medium">Moyenne</option>
                        <option value="high">Haute</option>
                        <option value="critical">Critique</option>
                    </select>
                </div>

                <!-- Catégorie -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                    <select v-model="filters.category_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Toutes les catégories</option>
                        <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                    </select>
                </div>

                <!-- Équipement -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Équipement</label>
                    <Combobox v-model="filters.equipement_id">
                        <div class="relative">
                            <ComboboxInput
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                @change="searchEquipements"
                                :displayValue="(id) => equipements.find(e => e.id === id)?.designation || ''"
                                placeholder="Rechercher un équipement..."
                            />
                            <ComboboxOptions class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 shadow-lg">
                                <ComboboxOption
                                    v-for="equipement in equipements"
                                    :key="equipement.id"
                                    :value="equipement.id"
                                    class="relative cursor-default select-none py-2 pl-3 pr-9 hover:bg-indigo-600 hover:text-white"
                                    v-slot="{ active, selected }"
                                >
                                    <span :class="['block truncate', selected && 'font-semibold']">
                                        {{ equipement.designation }}
                                    </span>
                                </ComboboxOption>
                            </ComboboxOptions>
                        </div>
                    </Combobox>
                </div>

                <!-- Assigné à -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Assigné à</label>
                    <select v-model="filters.assigned_to" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Tous les utilisateurs</option>
                        <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                    </select>
                </div>

                <!-- Période -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Période</label>
                    <div>
                        <label class="block text-xs text-gray-500">Du</label>
                        <input
                            type="date"
                            v-model="filters.date_from"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500">Au</label>
                        <input
                            type="date"
                            v-model="filters.date_to"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="p-4 border-t">
                <button
                    @click="applyFilters"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Appliquer les filtres
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { Combobox, ComboboxInput, ComboboxOptions, ComboboxOption } from '@headlessui/vue';
import debounce from 'lodash/debounce';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    modelValue: Boolean,
    statuses: Array,
    categories: Array,
    users: Array,
    currentFilters: {
        type: Object,
        default: () => ({})
    }
});

defineEmits(['update:modelValue']);

const equipements = ref([]);
const filters = ref({
    status_id: props.currentFilters.status_id || '',
    priority: props.currentFilters.priority || '',
    category_id: props.currentFilters.category_id || '',
    equipement_id: props.currentFilters.equipement_id || '',
    assigned_to: props.currentFilters.assigned_to || '',
    date_from: props.currentFilters.date_from || '',
    date_to: props.currentFilters.date_to || ''
});

const searchEquipements = debounce(async (event) => {
    const query = event.target.value;
    if (query.length < 2) return;

    try {
        const response = await fetch(`/api/equipements/search?q=${encodeURIComponent(query)}`);
        const data = await response.json();
        equipements.value = data;
    } catch (error) {
        console.error('Erreur lors de la recherche des équipements:', error);
    }
}, 300);

const applyFilters = () => {
    router.get(route('tickets.index'), {
        ...filters.value,
        page: 1 // Réinitialiser la pagination lors de l'application des filtres
    }, {
        preserveState: true,
        preserveScroll: true,
        only: ['tickets']
    });
};
</script>