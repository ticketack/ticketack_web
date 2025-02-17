<template>
    <span :class="priorityClasses" class="px-2 py-1 text-xs font-medium rounded-full">
        {{ priorityLabel }}
    </span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    priority: {
        type: String,
        required: true
    }
});

const priorityLabel = computed(() => {
    const labels = {
        low: 'Basse',
        medium: 'Moyenne',
        high: 'Haute',
        critical: 'Critique'
    };
    return labels[props.priority] || props.priority;
});

const priorityClasses = computed(() => {
    const classes = {
        low: 'bg-green-100 text-green-800',
        medium: 'bg-blue-100 text-blue-800',
        high: 'bg-orange-100 text-orange-800',
        critical: 'bg-red-100 text-red-800'
    };
    return classes[props.priority] || '';
});
</script>
