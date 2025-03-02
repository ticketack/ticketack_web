<template>
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2">
            <li class="inline-flex items-center">
                <Link :href="route('dashboard')" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">
                    <HomeIcon class="w-4 h-4 mr-2" />
                    Accueil
                </Link>
            </li>
            <li v-for="(item, index) in items" :key="index">
                <div class="flex items-center">
                    <ChevronRightIcon class="w-4 h-4 text-gray-400" />
                    <Link 
                        v-if="item.route && index < items.length - 1" 
                        :href="route(item.route, item.params || {})" 
                        class="ml-1 text-sm font-medium text-gray-700 hover:text-indigo-600 md:ml-2"
                    >
                        {{ item.name }}
                    </Link>
                    <span 
                        v-else 
                        class="ml-1 text-sm font-medium text-gray-500 md:ml-2"
                    >
                        {{ item.name }}
                    </span>
                </div>
            </li>
        </ol>
    </nav>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { HomeIcon, ChevronRightIcon } from '@heroicons/vue/24/solid';

const props = defineProps({
    items: {
        type: Array,
        required: true,
        // Each item should have: { name: 'Name', route: 'route.name', params: {} (optional) }
    }
});
</script>
