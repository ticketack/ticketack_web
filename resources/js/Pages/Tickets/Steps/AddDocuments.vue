<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">
                Ajouter des documents
            </h3>
            <Link
                :href="route('tickets.show', ticket.id)"
                class="text-sm text-gray-600 hover:text-gray-900"
            >
                Passer cette étape →
            </Link>
        </div>

        <!-- Zone de dépôt -->
        <DropZone
            ref="dropZone"
            class="mt-1"
            @files-selected="handleFiles"
        />

        <!-- Aperçu des documents -->
        <div v-if="documents.length > 0" class="mt-4">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Documents ajoutés</h4>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                <DocumentPreview
                    v-for="doc in documents"
                    :key="doc.id"
                    :document="doc"
                    :ticket-id="ticket.id"
                    :can-delete="true"
                    @delete="removeDocument"
                />
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end space-x-4">
            <Link
                :href="route('tickets.show', ticket.id)"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
            >
                Terminer
            </Link>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import DropZone from '@/Components/Documents/DropZone.vue';
import DocumentPreview from '@/Components/Documents/DocumentPreview.vue';

const props = defineProps({
    ticket: {
        type: Object,
        required: true
    }
});

const dropZone = ref(null);
const documents = ref([]);

const handleFiles = async (files) => {
    dropZone.value.startUpload();
    
    for (const file of files) {
        const formData = new FormData();
        formData.append('document', file);
        
        try {
            const response = await axios.post(route('tickets.documents.store', props.ticket.id), formData, {
                onUploadProgress: (progressEvent) => {
                    const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                    dropZone.value.updateProgress(percentCompleted);
                }
            });
            
            documents.value.push(response.data.document);
        } catch (error) {
            console.error('Erreur lors du téléversement:', error);
        }
    }
    
    dropZone.value.finishUpload();
};

const removeDocument = async (document) => {
    try {
        await axios.delete(route('tickets.documents.destroy', [props.ticket.id, document.id]));
        documents.value = documents.value.filter(doc => doc.id !== document.id);
    } catch (error) {
        console.error('Erreur lors de la suppression:', error);
    }
};
</script>
