<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Créer un nouveau ticket
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Étape 1 : Informations du ticket -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-medium text-gray-900">
                                Étape 1 : Informations du ticket
                            </h3>
                            <div class="flex items-center space-x-2">
                                <svg v-if="currentStep > 1" class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span v-if="currentStep > 1" class="text-sm text-gray-500">Complété</span>
                            </div>
                        </div>

                        <form @submit.prevent="submitStep1">
                            <div class="space-y-6">
                                <!-- Titre -->
                                <div>
                                    <InputLabel for="title" value="Titre" />
                                    <TextInput
                                        id="title"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.title"
                                        required
                                        :disabled="currentStep > 1"
                                    />
                                    <InputError :message="form.errors.title" class="mt-2" />
                                </div>

                                <!-- Description -->
                                <div>
                                    <InputLabel for="description" value="Description" />
                                    <textarea
                                        id="description"
                                        v-model="form.description"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        rows="4"
                                        required
                                        :disabled="currentStep > 1"
                                    ></textarea>
                                    <InputError :message="form.errors.description" class="mt-2" />
                                </div>

                                <!-- Priorité -->
                                <div>
                                    <InputLabel for="priority" value="Priorité" />
                                    <select
                                        id="priority"
                                        v-model="form.priority"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        required
                                        :disabled="currentStep > 1"
                                    >
                                        <option value="low">Basse</option>
                                        <option value="medium">Moyenne</option>
                                        <option value="high">Haute</option>
                                        <option value="critical">Critique</option>
                                    </select>
                                    <InputError :message="form.errors.priority" class="mt-2" />
                                </div>

                                <!-- Catégorie -->
                                <div>
                                    <InputLabel for="category_id" value="Catégorie" />
                                    <select
                                        id="category_id"
                                        v-model="form.category_id"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        required
                                        :disabled="currentStep > 1"
                                    >
                                        <option v-for="category in categories" :key="category.id" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                    </select>
                                    <InputError :message="form.errors.category_id" class="mt-2" />
                                </div>

                                <!-- Équipement -->
                                <div>
                                    <InputLabel for="equipement_id" value="Équipement concerné" />
                                    <Autocomplete
                                        v-model="form.equipement_id"
                                        :search-url="route('equipements.search')"
                                        placeholder="Rechercher un équipement..."
                                        class="mt-1"
                                        :disabled="currentStep > 1"
                                    />
                                    <InputError :message="form.errors.equipement_id" class="mt-2" />
                                </div>

                                <!-- Date d'échéance -->
                                <div>
                                    <InputLabel for="due_date" value="Date d'échéance (optionnelle)" />
                                    <TextInput
                                        id="due_date"
                                        type="date"
                                        class="mt-1 block w-full"
                                        v-model="form.due_date"
                                        :disabled="currentStep > 1"
                                    />
                                    <InputError :message="form.errors.due_date" class="mt-2" />
                                </div>
                            </div>

                            <div class="flex items-center justify-end mt-6 gap-4">
                                <Link :href="route('tickets.index')" class="text-gray-600 hover:text-gray-900">Annuler</Link>
                                <PrimaryButton
                                    type="submit"
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                >
                                    {{ buttonText }}
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Étape 2 : Documents -->
                <div v-if="currentStep > 1" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-medium text-gray-900">
                                Étape 2 : Ajouter des documents
                            </h3>
                        </div>

                        <div class="space-y-6">
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
                                        :ticket-id="ticketId"
                                        :can-delete="true"
                                        @delete="removeDocument"
                                    />
                                </div>
                            </div>

                            <div class="flex items-center justify-end mt-6 space-x-4">
                                <SecondaryButton @click="goBackToStep1">
                                    Revenir à l'étape 1
                                </SecondaryButton>
                                <Link
                                    :href="route('tickets.show', ticketId)"
                                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700"
                                >
                                    Terminer
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';

import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Autocomplete from '@/Components/Autocomplete.vue';
import DropZone from '@/Components/Documents/DropZone.vue';
import DocumentPreview from '@/Components/Documents/DocumentPreview.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const currentStep = ref(1);
const ticketId = ref(null);
const dropZone = ref(null);
const documents = ref([]);

const props = defineProps({
    categories: {
        type: Array,
        required: true
    },
    equipements: {
        type: Array,
        required: true
    }
});

const form = useForm({
    title: '',
    description: '',
    priority: 'medium',
    category_id: '',
    equipement_id: '',
    due_date: null
});



const submitStep1 = () => {
    console.log('Submitting form, current step:', currentStep.value);
    console.log('Form data:', form);

    if (currentStep.value === 1) {
        form.post(route('tickets.store'), {
            preserveScroll: true,
            onSuccess: (page) => {
                if (page?.props?.ticket?.id) {
                    ticketId.value = page.props.ticket.id;
                    currentStep.value = 2;
                }
            },
            onError: (errors) => {
                console.error('Form errors:', errors);
            }
        });
    } else {
        form.put(route('tickets.update', ticketId.value), {
            preserveScroll: true,
            onSuccess: () => {
                console.log('Update successful');
            },
            onError: (errors) => {
                console.error('Update errors:', errors);
            }
        });
    }
};

const goBackToStep1 = () => {
    currentStep.value = 1;
};

const handleFiles = async (files) => {
    if (!ticketId.value) return;

    dropZone.value.startUpload();
    
    for (const file of files) {
        const formData = new FormData();
        formData.append('document', file);
        
        try {
            const response = await axios.post(route('tickets.documents.store', ticketId.value), formData, {
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
        await axios.delete(route('tickets.documents.destroy', [ticketId.value, document.id]));
        documents.value = documents.value.filter(doc => doc.id !== document.id);
    } catch (error) {
        console.error('Erreur lors de la suppression:', error);
    }
};

const buttonText = computed(() => {
    return currentStep.value === 1 ? 'Suite' : 'Modifier';
});
</script>
