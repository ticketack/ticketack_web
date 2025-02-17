<template>
    <div class="relative group">
        <!-- Aperçu du document -->
        <div class="w-24 h-24 border rounded-lg overflow-hidden bg-gray-50 flex items-center justify-center">
            <!-- Image pour les types image -->
            <img v-if="isImage" :src="document.url" class="w-full h-full object-cover" :alt="document.original_name">
            
            <!-- Icône pour les autres types de fichiers -->
            <div v-else class="text-gray-400">
                <svg v-if="isPDF" class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9v-4h2v4zm4 0h-2v-4h2v4zm4-9H5v2h14V7z"/>
                </svg>
                <svg v-else-if="isDoc" class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zM6 20V4h7v5h5v11H6z"/>
                </svg>
                <svg v-else class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm-1 7V3.5L18.5 9H13z"/>
                </svg>
            </div>
        </div>

        <!-- Nom du fichier -->
        <div class="mt-1 text-xs text-center text-gray-600 truncate" :title="document.original_name">
            {{ document.original_name }}
        </div>

        <!-- Actions -->
        <div class="absolute top-0 right-0 p-1 hidden group-hover:block">
            <!-- Bouton de téléchargement -->
            <a :href="downloadUrl" 
               class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-500 text-white hover:bg-blue-600 mr-1"
               title="Télécharger">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
            </a>

            <!-- Bouton de suppression -->
            <button v-if="canDelete"
                    @click="$emit('delete', document)"
                    class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-500 text-white hover:bg-red-600"
                    title="Supprimer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    document: {
        type: Object,
        required: true
    },
    ticketId: {
        type: [Number, String],
        required: true
    },
    canDelete: {
        type: Boolean,
        default: false
    }
});

const isImage = computed(() => {
    return props.document.mime_type.startsWith('image/');
});

const isPDF = computed(() => {
    return props.document.mime_type === 'application/pdf';
});

const isDoc = computed(() => {
    return props.document.mime_type.includes('word') || 
           props.document.mime_type.includes('officedocument');
});

const downloadUrl = computed(() => {
    return route('tickets.documents.download', [props.ticketId, props.document.id]);
});
</script>
