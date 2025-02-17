<template>
    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-full" :style="{ backgroundColor: status.color + '20', color: status.color }">
        <i v-if="status.icon" :class="status.icon"></i>
        {{ status.name }}
    </span>
</template>

<script setup>
defineProps({
    status: {
        type: Object,
        required: true
    }
});
</script>
