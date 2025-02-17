<template>
    <div class="w-full">
        <!-- Zone de dépôt -->
        <div
            @dragover.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            @drop.prevent="handleDrop"
            :class="[
                'border-2 border-dashed rounded-lg p-6 text-center transition-colors',
                isDragging ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300 hover:border-indigo-500',
            ]"
        >
            <div class="space-y-2">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                <div class="text-sm text-gray-600">
                    <label for="file-upload" class="relative cursor-pointer rounded-md font-medium text-indigo-600 hover:text-indigo-500">
                        <span>Téléverser un fichier</span>
                        <input 
                            id="file-upload" 
                            type="file" 
                            class="sr-only" 
                            @change="handleFileSelect"
                            :accept="acceptedTypes.join(',')"
                            multiple
                        >
                    </label>
                    <p class="pl-1">ou déposer ici</p>
                </div>
                <p class="text-xs text-gray-500">
                    PDF, DOC, DOCX, TXT, JPG, JPEG, PNG jusqu'à 10MB
                </p>
            </div>
        </div>

        <!-- Messages d'erreur -->
        <div v-if="error" class="mt-2 text-sm text-red-600">
            {{ error }}
        </div>

        <!-- Barre de progression -->
        <div v-if="isUploading" class="mt-4">
            <div class="relative pt-1">
                <div class="overflow-hidden h-2 text-xs flex rounded bg-indigo-200">
                    <div
                        :style="{ width: uploadProgress + '%' }"
                        class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-indigo-500 transition-all duration-300"
                    ></div>
                </div>
                <div class="text-xs text-center mt-1 text-gray-600">
                    {{ uploadProgress }}%
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
    acceptedTypes: {
        type: Array,
        default: () => ['.pdf', '.doc', '.docx', '.txt', '.jpg', '.jpeg', '.png']
    },
    maxSize: {
        type: Number,
        default: 10 * 1024 * 1024 // 10MB
    }
});

const emit = defineEmits(['files-selected']);

const isDragging = ref(false);
const error = ref('');
const isUploading = ref(false);
const uploadProgress = ref(0);

const validateFiles = (files) => {
    error.value = '';
    
    // Vérifier les types de fichiers
    for (const file of files) {
        const extension = '.' + file.name.split('.').pop().toLowerCase();
        if (!props.acceptedTypes.includes(extension)) {
            error.value = `Le type de fichier ${extension} n'est pas supporté`;
            return false;
        }
        
        if (file.size > props.maxSize) {
            error.value = `Le fichier ${file.name} dépasse la taille maximale de 10MB`;
            return false;
        }
    }
    
    return true;
};

const handleDrop = (e) => {
    isDragging.value = false;
    const files = [...e.dataTransfer.files];
    
    if (validateFiles(files)) {
        emit('files-selected', files);
    }
};

const handleFileSelect = (e) => {
    const files = [...e.target.files];
    
    if (validateFiles(files)) {
        emit('files-selected', files);
    }
    
    // Réinitialiser l'input pour permettre la sélection du même fichier
    e.target.value = '';
};

// Exposer les méthodes pour contrôler la barre de progression
defineExpose({
    startUpload: () => {
        isUploading.value = true;
        uploadProgress.value = 0;
    },
    updateProgress: (progress) => {
        uploadProgress.value = Math.round(progress);
    },
    finishUpload: () => {
        isUploading.value = false;
        uploadProgress.value = 100;
        setTimeout(() => {
            isUploading.value = false;
            uploadProgress.value = 0;
        }, 1000);
    }
});
</script>
