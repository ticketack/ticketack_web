<template>
    <AuthenticatedLayout :breadcrumbs="[
        { name: 'Tickets', route: 'tickets.index' },
        { name: `#${props.ticket?.id}`, route: 'tickets.show', params: { id: props.ticket?.id } },
        { name: 'Modifier' }
    ]">
        <template #header>
            <div class="flex justify-between items-center mx-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Modifier le ticket #{{ props.ticket?.id }}
                </h2>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-8xl mx-auto sm:px-2 lg:px-2">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Titre -->
                            <div>
                                <InputLabel for="title" value="Titre" />
                                <TextInput
                                    id="title"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.title"
                                    required
                                    autofocus
                                />
                                <InputError class="mt-2" :message="form.errors.title" />
                            </div>

                            <!-- Description -->
                            <div>
                                <InputLabel for="description" value="Description" />
                                <TextArea
                                    id="description"
                                    class="mt-1 block w-full"
                                    v-model="form.description"
                                    rows="6"
                                />
                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Statut -->
                                <div>
                                    <InputLabel for="status_id" value="Statut" />
                                    <SelectInput
                                        id="status_id"
                                        class="mt-1 block w-full"
                                        v-model="form.status_id"
                                    >
                                        <option v-for="status in props.statuses" :key="status.id" :value="status.id">
                                            {{ status.name }}
                                        </option>
                                    </SelectInput>
                                    <InputError class="mt-2" :message="form.errors.status_id" />
                                </div>

                                <!-- Priorité -->
                                <div>
                                    <InputLabel for="priority" value="Priorité" />
                                    <SelectInput
                                        id="priority"
                                        class="mt-1 block w-full"
                                        v-model="form.priority"
                                    >
                                        <option value="low">Basse</option>
                                        <option value="medium">Moyenne</option>
                                        <option value="high">Haute</option>
                                        <option value="urgent">Urgente</option>
                                    </SelectInput>
                                    <InputError class="mt-2" :message="form.errors.priority" />
                                </div>

                                <!-- Catégorie -->
                                <div>
                                    <InputLabel for="category_id" value="Catégorie" />
                                    <SelectInput
                                        id="category_id"
                                        class="mt-1 block w-full"
                                        v-model="form.category_id"
                                    >
                                        <option value="">Aucune</option>
                                        <option v-for="category in props.categories" :key="category.id" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                    </SelectInput>
                                    <InputError class="mt-2" :message="form.errors.category_id" />
                                </div>

                                <!-- Équipement -->
                                <div>
                                    <InputLabel for="equipment_id" value="Équipement" />
                                    <SelectInput
                                        id="equipment_id"
                                        class="mt-1 block w-full"
                                        v-model="form.equipment_id"
                                    >
                                        <option value="">Aucun</option>
                                        <option v-for="equipment in props.equipments" :key="equipment.id" :value="equipment.id">
                                            {{ equipment.name }}
                                        </option>
                                    </SelectInput>
                                    <InputError class="mt-2" :message="form.errors.equipment_id" />
                                </div>

                                <!-- Visibilité -->
                                <div>
                                    <InputLabel for="is_public" value="Visibilité" />
                                    <div class="mt-2">
                                        <label class="inline-flex items-center">
                                            <input type="radio" class="form-radio" name="is_public" v-model="form.is_public" :value="true">
                                            <span class="ml-2">Public</span>
                                        </label>
                                        <label class="inline-flex items-center ml-6">
                                            <input type="radio" class="form-radio" name="is_public" v-model="form.is_public" :value="false">
                                            <span class="ml-2">Privé</span>
                                        </label>
                                    </div>
                                    <InputError class="mt-2" :message="form.errors.is_public" />
                                </div>

                                <!-- Date d'échéance -->
                                <div>
                                    <InputLabel for="due_date" value="Date d'échéance (optionnelle)" />
                                    <TextInput
                                        id="due_date"
                                        type="date"
                                        class="mt-1 block w-full"
                                        v-model="form.due_date"
                                    />
                                    <InputError class="mt-2" :message="form.errors.due_date" />
                                </div>
                            </div>

                            <!-- Documents -->
                            <div>
                                <InputLabel value="Documents actuels" />
                                <div v-if="props.ticket?.documents?.length" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 mt-2">
                                    <DocumentPreview
                                        v-for="document in props.ticket?.documents"
                                        :key="document?.id"
                                        :document="document"
                                        :ticket-id="props.ticket?.id"
                                        :can-delete="true"
                                        @delete="deleteDocument"
                                    />
                                </div>
                                <div v-else class="text-sm text-gray-500 mt-2">
                                    Aucun document attaché à ce ticket
                                </div>
                            </div>

                            <!-- Upload de nouveaux documents -->
                            <div>
                                <InputLabel value="Ajouter des documents" />
                                <DropZone
                                    ref="dropZone"
                                    class="mt-1"
                                    @files-selected="handleFileUpload"
                                />
                                <div class="mt-1 space-y-1">
                                    <p class="text-sm text-gray-500">Taille maximale : 10 MB</p>
                                    <p class="text-sm text-gray-500">Formats acceptés : JPG, JPEG, PNG, GIF, PDF</p>
                                </div>

                                <!-- Liste des fichiers sélectionnés -->
                                <div v-if="form.new_documents.length" class="mt-2 space-y-2">
                                    <div v-for="(file, index) in form.new_documents" :key="index" 
                                        class="flex items-center justify-between p-2 bg-gray-50 rounded-md">
                                        <div class="flex items-center space-x-2">
                                            <DocumentIcon class="h-4 w-4 text-gray-500" />
                                            <span class="text-sm text-gray-700">{{ file.name }}</span>
                                            <span class="text-xs text-gray-500">({{ formatFileSize(file.size) }})</span>
                                        </div>
                                        <button type="button" @click="removeFile(index)"
                                                class="text-red-600 hover:text-red-800">
                                            <TrashIcon class="h-4 w-4" />
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-end mt-6 gap-4">
                                <Link :href="route('tickets.show', props.ticket.id)" class="px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 active:bg-gray-300 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300">
                                    Annuler
                                </Link>
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Enregistrer les modifications
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import { ref, defineProps } from 'vue';
import { DocumentIcon, TrashIcon } from '@heroicons/vue/24/outline';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import SelectInput from '@/Components/SelectInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DropZone from '@/Components/Documents/DropZone.vue';
import DocumentPreview from '@/Components/Documents/DocumentPreview.vue';
import { useToast } from 'vue-toastification';

const toast = useToast();

const props = defineProps({
    ticket: {
        type: Object,
        required: true
    },
    statuses: {
        type: Array,
        required: true
    },
    categories: {
        type: Array,
        required: true
    },
    equipments: {
        type: Array,
        required: true
    }
});

const form = useForm({
    title: props.ticket?.title || '',
    description: props.ticket?.description || '',
    status_id: props.ticket?.status?.id || '',
    priority: props.ticket?.priority || 'medium',
    category_id: props.ticket?.category?.id || '',
    equipment_id: props.ticket?.equipment?.id || '',
    is_public: props.ticket?.is_public ?? true,
    due_date: props.ticket?.due_date || '',
    new_documents: []
});

const dropZone = ref(null);

const handleFileUpload = (files) => {
    if (files) {
        form.new_documents = [...form.new_documents, ...files];
    }
};

const removeFile = (index) => {
    form.new_documents.splice(index, 1);
};

const deleteDocument = (document) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce document ?')) {
        router.delete(route('tickets.documents.destroy', [props.ticket.id, document.id]), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                toast.success('Document supprimé avec succès');
            },
            onError: () => {
                toast.error('Erreur lors de la suppression du document');
            }
        });
    }
};

const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const submit = () => {
    // Créer un FormData pour tous les champs
    const formData = new FormData();
    
    // Important : spécifier la méthode PUT pour Laravel
    formData.append('_method', 'PUT');
    
    // Ajouter tous les champs de base
    formData.append('title', form.title);
    formData.append('description', form.description);
    formData.append('status_id', form.status_id);
    formData.append('priority', form.priority);
    formData.append('category_id', form.category_id);
    formData.append('equipment_id', form.equipment_id);
    formData.append('is_public', form.is_public ? '1' : '0');
    formData.append('due_date', form.due_date);
    
    // Ajouter les fichiers
    form.new_documents.forEach((file, index) => {
        formData.append(`new_documents[${index}]`, file);
    });

    // Soumettre le formulaire avec POST (mais avec _method=PUT)
    router.post(route('tickets.update', props.ticket.id), formData, {
        onStart: () => form.processing = true,
        onFinish: () => form.processing = false,
        onSuccess: () => {
            toast.success('Ticket mis à jour avec succès');
            router.visit(route('tickets.show', props.ticket.id));
        },
        onError: (errors) => {
            console.error('Erreurs:', errors);
            toast.error('Erreur lors de la mise à jour du ticket');
        }
    });
};
</script>
