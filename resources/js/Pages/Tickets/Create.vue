<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $page.props.translations.tickets.create.title }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Étape 1 : Informations du ticket -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-medium text-gray-900">
                                {{ $page.props.translations.tickets.create.step1.title }}
                            </h3>
                            <div class="flex items-center space-x-2">
                                <svg v-if="currentStep > 1" class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span v-if="currentStep > 1" class="text-sm text-gray-500">{{ $page.props.translations.tickets.create.step1.completed }}</span>
                            </div>
                        </div>

                        <form @submit.prevent="submitStep1">
                            <div class="space-y-6">
                                <!-- Titre -->
                                <div>
                                    <InputLabel for="title" :value="$page.props.translations.tickets.create.fields.title" />
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
                                    <InputLabel for="description" :value="$page.props.translations.tickets.create.fields.description" />
                                    <TiptapEditor
                                        id="description"
                                        v-model="form.description"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        placeholder="Description détaillée du ticket..."
                                        :disabled="currentStep > 1"
                                        required
                                    />
                                    <InputError :message="form.errors.description" class="mt-2" />
                                </div>

                                <!-- Priorité -->
                                <div>
                                    <InputLabel for="priority" :value="$page.props.translations.tickets.create.fields.priority" />
                                    <select
                                        id="priority"
                                        v-model="form.priority"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        required
                                        :disabled="currentStep > 1"
                                    >
                                        <option value="low">{{ $page.props.translations.tickets.create.priority.low }}</option>
                                        <option value="medium">{{ $page.props.translations.tickets.create.priority.medium }}</option>
                                        <option value="high">{{ $page.props.translations.tickets.create.priority.high }}</option>
                                        <option value="critical">{{ $page.props.translations.tickets.create.priority.critical }}</option>
                                    </select>
                                    <InputError :message="form.errors.priority" class="mt-2" />
                                </div>

                                <!-- Catégorie -->
                                <div>
                                    <InputLabel for="category_id" :value="$page.props.translations.tickets.create.fields.category" />
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
                                    <InputLabel for="equipment_id" :value="$page.props.translations.tickets.create.fields.equipment" />
                                    <Autocomplete
                                        v-model="form.equipment_id"
                                        :search-url="route('equipment.search')"
                                        placeholder="Rechercher un équipement..."
                                        class="mt-1"
                                        :disabled="currentStep > 1"
                                    />
                                    <InputError :message="form.errors.equipment_id" class="mt-2" />
                                </div>

                                <!-- Date d'échéance -->
                                <div>
                                    <InputLabel for="due_date" :value="$page.props.translations.tickets.create.fields.due_date" />
                                    <TextInput
                                        id="due_date"
                                        type="date"
                                        class="mt-1 block w-full"
                                        v-model="form.due_date"
                                        :disabled="currentStep > 1"
                                    />
                                    <InputError :message="form.errors.due_date" class="mt-2" />
                                </div>

                                <!-- Visibilité -->
                                <div class="flex items-center space-x-2">
                                    <div class="relative">
                                        <button
                                            type="button"
                                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                            :class="form.is_public ? 'bg-indigo-600' : 'bg-gray-200'"
                                            @click="form.is_public = !form.is_public"
                                            :disabled="currentStep > 1"
                                            role="switch"
                                            aria-checked="false"
                                        >
                                            <span
                                                aria-hidden="true"
                                                :class="form.is_public ? 'translate-x-5' : 'translate-x-0'"
                                                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                            />
                                        </button>
                                    </div>
                                    <div class="group relative">
                                        <InputLabel :value="$page.props.translations.tickets.create.visibility.public_label" class="!mb-0 cursor-help border-b border-dotted border-gray-400" />
                                        <div class="absolute left-0 bottom-full mb-2 hidden group-hover:block">
                                            <div class="bg-gray-900 text-white text-sm rounded p-2 w-64">
                                                {{ $page.props.translations.tickets.create.visibility.tooltip }}
                                                <div class="absolute left-0 top-full w-3 h-3 -mt-1.5 ml-2">
                                                    <div class="bg-gray-900 w-3 h-3 transform rotate-45"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-end mt-6 gap-4">
                                <Link :href="route('tickets.index')" class="text-gray-600 hover:text-gray-900">{{ $page.props.translations.tickets.create.buttons.cancel }}</Link>
                                <PrimaryButton
                                    type="submit"
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                >
                                    {{ currentStep === 1 ? $page.props.translations.tickets.create.buttons.next : $page.props.translations.tickets.create.buttons.modify }}
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Step 2 : Documents -->
                <div v-if="currentStep > 1" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-medium text-gray-900">
                                {{ $page.props.translations.tickets.create.step2.title }}
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
                                <h4 class="text-sm font-medium text-gray-700 mb-3">{{ $page.props.translations.tickets.create.step2.drag_files }}</h4>
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
                                    {{ $page.props.translations.tickets.create.buttons.back }}
                                </SecondaryButton>
                                <Link
                                    :href="route('tickets.show', ticketId)"
                                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700"
                                >
                                    {{ $page.props.translations.tickets.create.buttons.finish }}
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
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Autocomplete from '@/Components/Autocomplete.vue';
import DropZone from '@/Components/Documents/DropZone.vue';
import DocumentPreview from '@/Components/Documents/DocumentPreview.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TiptapEditor from '@/Components/TiptapEditor.vue';


const currentStep = ref(1);
const ticketId = ref(null);
const dropZone = ref(null);
const documents = ref([]);

const props = defineProps({
    categories: {
        type: Array,
        required: true
    },
    equipment: {
        type: Array,
        required: true
    }
});

const form = useForm({
    title: '',
    description: '',
    priority: 'medium',
    category_id: '',
    equipment_id: '',
    due_date: null,
    is_public: true
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


</script>
