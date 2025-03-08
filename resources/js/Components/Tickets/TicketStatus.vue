<template>
    <div class="relative group cursor-help">
        <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-full" 
              :style="{ backgroundColor: status.color + '20', color: status.color }">
            <i v-if="status.icon" :class="status.icon"></i>
            <template v-if="compact">
                {{ getShortName(status.name) }}
            </template>
            <template v-else>
                {{ status.name }}
            </template>
        </span>
        <div v-if="compact" class="hidden group-hover:block absolute z-50 bg-black text-white text-xs rounded py-1 px-2 whitespace-normal pointer-events-none" style="bottom: 100%; left: 50%; transform: translateX(-50%); margin-bottom: 5px;">
            {{ status.name }}
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    status: {
        type: Object,
        required: true
    },
    compact: {
        type: Boolean,
        default: false
    }
});

const getShortName = (name) => {
    // Prendre la première lettre ou les initiales pour les statuts à plusieurs mots
    if (!name) return '';
    
    if (name.includes(' ')) {
        return name.split(' ')
            .map(word => word.charAt(0))
            .join('');
    }
    
    return name.charAt(0);
};
</script>