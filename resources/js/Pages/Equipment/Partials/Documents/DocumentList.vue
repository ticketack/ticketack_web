<template>
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">{{ $page.props.translations.equipment.documents.title }}</h3>
        </div>

        <div class="p-4">
            <!-- Bouton d'ajout de document -->
            <div class="mb-4 flex justify-end" v-if="canCreate">
                <PrimaryButton @click="showUploadModal = true">
                    {{ $page.props.translations.equipment.documents.add }}
                </PrimaryButton>
            </div>

            <!-- Liste des documents -->
            <div v-if="documents.length > 0" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ $page.props.translations.equipment.documents.file }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ $page.props.translations.equipment.documents.description }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ $page.props.translations.equipment.documents.file_type }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ $page.props.translations.equipment.documents.file_size }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ $page.props.translations.equipment.documents.upload_date }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ $page.props.translations.equipment.documents.actions }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="document in documents" :key="document.id">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <!-- Icône selon le type de fichier -->
                                    <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center">
                                        <svg v-if="isImage(document)" class="h-8 w-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <svg v-else-if="isPdf(document)" class="h-8 w-8 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        <svg v-else-if="isOfficeDocument(document)" class="h-8 w-8 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <svg v-else class="h-8 w-8 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ document.original_filename }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ document.description || '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ document.file_type }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ formatFileSize(document.file_size) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ formatDate(document.created_at) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <!-- Bouton de prévisualisation -->
                                    <button v-if="isImage(document) || isPdf(document)" 
                                            @click="previewDocument(document)" 
                                            class="text-indigo-600 hover:text-indigo-900">
                                        {{ $page.props.translations.equipment.documents.view }}
                                    </button>
                                    <!-- Bouton de téléchargement -->
                                    <a :href="route('equipment.documents.download', document.id)" 
                                       class="text-blue-600 hover:text-blue-900">
                                        {{ $page.props.translations.equipment.documents.download }}
                                    </a>
                                    <!-- Bouton de modification -->
                                    <button v-if="canEdit" 
                                            @click="editDocument(document)" 
                                            class="text-yellow-600 hover:text-yellow-900">
                                        {{ $page.props.translations.equipment.documents.edit }}
                                    </button>
                                    <!-- Bouton de suppression -->
                                    <button v-if="canDelete" 
                                            @click="confirmDeleteDocument(document)" 
                                            class="text-red-600 hover:text-red-900">
                                        {{ $page.props.translations.equipment.documents.delete }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else class="text-center py-4 text-gray-500">
                {{ $page.props.translations.equipment.documents.empty }}
            </div>
        </div>

        <!-- Modal d'upload de document -->
        <Modal :show="showUploadModal" @close="showUploadModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    {{ $page.props.translations.equipment.documents.upload }}
                </h2>
                <form @submit.prevent="uploadDocument" class="mt-6 space-y-6">
                    <div>
                        <InputLabel for="file" :value="$page.props.translations.equipment.documents.file" />
                        <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                        <span>{{ $page.props.translations.equipment.documents.drop_files }}</span>
                                        <input id="file-upload" name="file-upload" type="file" class="sr-only" @change="handleFileInput" ref="fileInput" />
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500">
                                    {{ $page.props.translations.equipment.documents.max_size }}
                                </p>
                            </div>
                        </div>
                        <div v-if="selectedFile" class="mt-2 text-sm text-gray-600">
                            {{ selectedFile.name }} ({{ formatFileSize(selectedFile.size) }})
                        </div>
                        <InputError :message="form.errors.file" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="description" :value="$page.props.translations.equipment.documents.description" />
                        <TextArea
                            id="description"
                            class="mt-1 block w-full"
                            v-model="form.description"
                            :placeholder="$page.props.translations.equipment.documents.description_placeholder"
                        />
                        <InputError :message="form.errors.description" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <SecondaryButton @click="cancelUpload" class="mr-2">
                            {{ $page.props.translations.equipment.documents.cancel }}
                        </SecondaryButton>
                        <PrimaryButton :disabled="form.processing || !selectedFile">
                            {{ $page.props.translations.equipment.documents.save }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Modal de prévisualisation de document -->
        <Modal :show="showPreviewModal" @close="showPreviewModal = false" :max-width="'5xl'">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    {{ $page.props.translations.equipment.documents.preview }}
                </h2>
                <div v-if="previewDocumentData && isImage(previewDocumentData)" class="flex justify-center">
                    <img :src="previewDocumentData.file_path" class="max-w-full max-h-[70vh]" :alt="previewDocumentData.original_filename" />
                </div>
                <div v-else-if="previewDocumentData && isPdf(previewDocumentData)" class="h-[70vh]">
                    <iframe :src="previewDocumentData.file_path" class="w-full h-full" frameborder="0"></iframe>
                </div>
                <div v-else class="text-center py-4 text-gray-500">
                    {{ $page.props.translations.equipment.documents.no_preview }}
                </div>
                <div class="flex justify-end mt-4">
                    <SecondaryButton @click="showPreviewModal = false">
                        {{ $page.props.translations.equipment.documents.cancel }}
                    </SecondaryButton>
                </div>
            </div>
        </Modal>

        <!-- Modal de modification de document -->
        <Modal :show="showEditModal" @close="showEditModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    {{ $page.props.translations.equipment.documents.edit }}
                </h2>
                <form @submit.prevent="updateDocument" class="mt-6 space-y-6">
                    <div>
                        <InputLabel for="edit-description" :value="$page.props.translations.equipment.documents.description" />
                        <TextArea
                            id="edit-description"
                            class="mt-1 block w-full"
                            v-model="editForm.description"
                            :placeholder="$page.props.translations.equipment.documents.description_placeholder"
                        />
                        <InputError :message="editForm.errors.description" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <SecondaryButton @click="cancelEdit" class="mr-2">
                            {{ $page.props.translations.equipment.documents.cancel }}
                        </SecondaryButton>
                        <PrimaryButton :disabled="editForm.processing">
                            {{ $page.props.translations.equipment.documents.save }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Modal de confirmation de suppression -->
        <ConfirmationModal :show="showDeleteModal" @close="showDeleteModal = false">
            <template #title>
                {{ $page.props.translations.equipment.documents.delete }}
            </template>
            <template #content>
                {{ $page.props.translations.equipment.documents.confirm_delete }}
            </template>
            <template #footer>
                <SecondaryButton @click="showDeleteModal = false">
                    {{ $page.props.translations.equipment.documents.cancel }}
                </SecondaryButton>
                <DangerButton class="ml-2" @click="deleteDocument" :disabled="deleteForm.processing">
                    {{ $page.props.translations.equipment.documents.delete }}
                </DangerButton>
            </template>
        </ConfirmationModal>
    </div>
</template>

<script setup>
import { ref, defineProps, defineEmits } from 'vue';
import { useForm, usePage, router } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { fr, enUS, de } from 'date-fns/locale';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextArea from '@/Components/TextArea.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import ConfirmationModal from '@/Components/Modal/ConfirmationModal.vue';

const props = defineProps({
    equipment: {
        type: Object,
        required: true
    },
    documents: {
        type: Array,
        required: true
    },
    canCreate: {
        type: Boolean,
        default: false
    },
    canEdit: {
        type: Boolean,
        default: false
    },
    canDelete: {
        type: Boolean,
        default: false
    }
});

const page = usePage();
const locale = page.props.locale || 'fr';
const locales = { fr, en: enUS, de };

// États pour les modals
const showUploadModal = ref(false);
const showPreviewModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);

// Référence au fichier sélectionné
const fileInput = ref(null);
const selectedFile = ref(null);

// Document en cours de prévisualisation
const previewDocumentData = ref(null);

// Document en cours d'édition ou de suppression
const currentDocument = ref(null);

// Formulaire d'upload
const form = useForm({
    file: null,
    description: '',
});

// Formulaire d'édition
const editForm = useForm({
    description: '',
});

// Formulaire de suppression
const deleteForm = useForm({});

// Gestionnaire de sélection de fichier
const handleFileInput = (e) => {
    const file = e.target.files[0];
    if (file) {
        selectedFile.value = file;
        form.file = file;
    }
};

// Définir les émetteurs d'événements
const emit = defineEmits(['document-added']);

// Fonction d'upload de document
const uploadDocument = () => {
    form.post(route('equipment.documents.store', props.equipment.id), {
        onSuccess: () => {
            showUploadModal.value = false;
            resetUploadForm();
            // Émettre un événement pour indiquer qu'un document a été ajouté
            emit('document-added');
            // Forcer le rafraîchissement de la page
            window.location.reload();
        }
    });
};

// Réinitialisation du formulaire d'upload
const resetUploadForm = () => {
    form.reset();
    selectedFile.value = null;
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

// Annulation de l'upload
const cancelUpload = () => {
    showUploadModal.value = false;
    resetUploadForm();
};

// Prévisualisation d'un document
const previewDocument = (document) => {
    previewDocumentData.value = document;
    showPreviewModal.value = true;
};

// Édition d'un document
const editDocument = (document) => {
    currentDocument.value = document;
    editForm.description = document.description || '';
    showEditModal.value = true;
};

// Mise à jour d'un document
const updateDocument = () => {
    editForm.put(route('equipment.documents.update', currentDocument.value.id), {
        onSuccess: () => {
            showEditModal.value = false;
            currentDocument.value = null;
            // Émettre un événement pour indiquer qu'un document a été mis à jour
            emit('document-added');
            // Forcer le rafraîchissement de la page
            window.location.reload();
        }
    });
};

// Annulation de l'édition
const cancelEdit = () => {
    showEditModal.value = false;
    editForm.reset();
    currentDocument.value = null;
};

// Confirmation de suppression d'un document
const confirmDeleteDocument = (document) => {
    currentDocument.value = document;
    showDeleteModal.value = true;
};

// Suppression d'un document
const deleteDocument = () => {
    deleteForm.delete(route('equipment.documents.destroy', currentDocument.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            currentDocument.value = null;
            // Émettre un événement pour indiquer qu'un document a été supprimé
            emit('document-added');
            // Forcer le rafraîchissement de la page
            window.location.reload();
        }
    });
};

// Formatage de la taille de fichier
const formatFileSize = (size) => {
    if (size < 1024) {
        return size + ' B';
    } else if (size < 1024 * 1024) {
        return (size / 1024).toFixed(2) + ' KB';
    } else if (size < 1024 * 1024 * 1024) {
        return (size / (1024 * 1024)).toFixed(2) + ' MB';
    } else {
        return (size / (1024 * 1024 * 1024)).toFixed(2) + ' GB';
    }
};

// Formatage de la date
const formatDate = (dateString) => {
    const date = new Date(dateString);
    return format(date, 'PPP', { locale: locales[locale] });
};

// Vérifier si le document est une image
const isImage = (document) => {
    const imageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
    return imageTypes.includes(document.file_type);
};

// Vérifier si le document est un PDF
const isPdf = (document) => {
    return document.file_type === 'application/pdf';
};

// Vérifier si le document est un document Office
const isOfficeDocument = (document) => {
    const officeTypes = [
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation'
    ];
    return officeTypes.includes(document.file_type);
};
</script>
