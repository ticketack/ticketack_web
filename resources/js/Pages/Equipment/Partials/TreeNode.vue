<template>
    <div class="pl-4 border-l-2 border-gray-200">
        <div class="flex items-center space-x-2">
            <button 
                v-if="hasChildren"
                @click="isOpen = !isOpen"
                class="w-6 h-6 flex items-center justify-center rounded hover:bg-gray-100"
            >
                <svg 
                    class="w-4 h-4 transform transition-transform"
                    :class="{ 'rotate-90': isOpen }"
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24"
                >
                    <path 
                        stroke-linecap="round" 
                        stroke-linejoin="round" 
                        stroke-width="2" 
                        d="M9 5l7 7-7 7"
                    />
                </svg>
            </button>
            <div class="flex-1 py-2">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <span class="font-medium">{{ node.designation }}</span>
                        <span class="text-sm text-gray-500">{{ node.marque }} - {{ node.modele }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button
                            v-if="$page.props.permissions['equipment.edit']"
                            @click="$emit('edit', node.id)"
                            class="p-2 text-blue-600 hover:text-blue-800 transition-colors"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <button
                            v-if="$page.props.permissions['equipment.delete']"
                            @click="$emit('delete', node.id)"
                            class="p-2 text-red-600 hover:text-red-800 transition-colors"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div v-if="isOpen && hasChildren" class="ml-4">
            <TreeNode 
                v-for="child in node.all_children" 
                :key="child.id" 
                :node="child"
                @edit="$emit('edit', $event)"
                @delete="$emit('delete', $event)"
            />
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    node: {
        type: Object,
        required: true
    }
});

const isOpen = ref(false);
const hasChildren = computed(() => props.node.all_children && props.node.all_children.length > 0);
</script>
