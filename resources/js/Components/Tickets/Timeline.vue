<template>
    <div class="flow-root">
        <ul role="list" class="-mb-8">
            <li v-for="(log, logIdx) in logs" :key="log.id">
                <div class="relative pb-8">
                    <span v-if="logIdx !== logs.length - 1" class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true" />
                    <div class="relative flex space-x-3">
                        <div>
                            <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white"
                                :class="getTypeClass(log.type)">
                                <component :is="getTypeIcon(log.type)" class="h-5 w-5 text-white" aria-hidden="true" />
                            </span>
                        </div>
                        <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                            <div>
                                <p class="text-sm text-gray-500">
                                    {{ log.message }}
                                    <span class="font-medium text-gray-900">{{ log.user.name }}</span>
                                </p>
                            </div>
                            <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                <time :datetime="log.created_at">{{ formatDate(log.created_at) }}</time>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</template>

<script setup>
import { format } from 'date-fns';
import { fr } from 'date-fns/locale';
import { 
    ChatBubbleLeftEllipsisIcon, 
    CheckCircleIcon,
    UserCircleIcon,
    ArrowPathIcon,
    PencilSquareIcon
} from '@heroicons/vue/24/solid';

const props = defineProps({
    logs: {
        type: Array,
        required: true
    }
});

const getTypeClass = (type) => {
    const classes = {
        comment: 'bg-gray-400',
        created: 'bg-green-500',
        status_changed: 'bg-blue-500',
        assigned: 'bg-purple-500',
        updated: 'bg-yellow-500'
    };
    return classes[type] || 'bg-gray-400';
};

const getTypeIcon = (type) => {
    const icons = {
        comment: ChatBubbleLeftEllipsisIcon,
        created: CheckCircleIcon,
        status_changed: ArrowPathIcon,
        assigned: UserCircleIcon,
        updated: PencilSquareIcon
    };
    return icons[type] || ChatBubbleLeftEllipsisIcon;
};

const formatDate = (date) => {
    return format(new Date(date), "d MMMM yyyy 'à' HH:mm", { locale: fr });
};
</script>
