<script setup>
import AppLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useForm, usePage, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { setLogo } from '@/Stores/companyLogo';

const page = usePage();


const props = defineProps({
    settings: {
        type: Object,
        required: true,
        default: () => ({
            company_name: '',
            logo: ''
        })
    },
    flash: {
        type: Object,
        default: () => ({
            success: '',
            error: ''
        })
    }
});

// Messages d'état
const successMessage = ref(props.flash?.success || '');
const errorMessage = ref(props.flash?.error || '');

// État des modales
const showDeleteModal = ref(false);
const isDeleting = ref(false);

const previewLogo = ref('');
const currentLogo = computed(() => previewLogo.value || props.settings?.logo || '');

const form = useForm({
    company_name: props.settings.company_name || '',
    logo: null,
    language: props.settings.language || 'fr'
});

const handleLanguageChange = (event) => {
    const newLanguage = event.target.value;
    
    // Créer un nouveau formulaire avec seulement la langue
    const languageForm = useForm({
        language: newLanguage
    });
    
    // Envoyer la requête POST
    languageForm.post(route('settings.store'));
};

// Les langues sont maintenant gérées directement dans le template avec les traductions

const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB en octets

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        if (file.size > MAX_FILE_SIZE) {
            errorMessage.value = page.props.translations.settings.logo.error_size;
            event.target.value = ''; // Réinitialiser l'input file
            return;
        }
        form.logo = file;
        if (file.type.startsWith('image/')) {
            previewLogo.value = URL.createObjectURL(file);
        }
        console.log('Preview logo set to:', previewLogo.value);
        errorMessage.value = ''; // Effacer le message d'erreur si présent
    }
};

const submit = () => {
    const oldLanguage = props.settings.language;
    
    console.log('Form data before submit:', {
        company_name: form.company_name,
        language: form.language,
        logo: form.logo
    });
    
    form.post('/settings', {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: (response) => {
            console.log('Response after submit:', response);
            // Si la langue a été modifiée, forcer le rechargement de la page
            if (form.language !== oldLanguage) {
                window.location.reload();
                return;
            }
            successMessage.value = page.props.translations.settings.success.updated;
            form.reset('logo');
            if (response?.props?.settings?.logo) {
                previewLogo.value = '';
            }
            setTimeout(() => {
                successMessage.value = '';
            }, 5000);
        },
        onError: (errors) => {
            errorMessage.value = Object.values(errors)[0];
            setTimeout(() => {
                errorMessage.value = '';
            }, 5000);
        }
    });
};

const deleteLogo = () => {
    isDeleting.value = true;
    router.delete('/settings/logo', {
        preserveScroll: true,
        onSuccess: (response) => {
            showDeleteModal.value = false;
            successMessage.value = response?.props?.flash?.success || page.props.translations.settings.success.logo_deleted;
            if (response?.props?.settings) {
                setLogo(response.props.settings.logo || '');
            }
            setTimeout(() => {
                successMessage.value = '';
            }, 5000);
        },
        onError: () => {
            errorMessage.value = page.props.translations.settings.error.logo_delete;
            setTimeout(() => {
                errorMessage.value = '';
            }, 5000);
        },
        onFinish: () => {
            isDeleting.value = false;
        }
    });
};
</script>

<template>
    <Head :title="$page.props.translations.settings.title" />

    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $page.props.translations.settings.title }}
            </h2>
        </template>

        <!-- Messages de notification -->
        <div class="max-w-7xl mx-auto mt-4 sm:px-6 lg:px-8 space-y-4">
            <!-- Message de succès -->
            <div v-if="successMessage" class="bg-green-50 border border-green-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ successMessage }}</p>
                    </div>
                </div>
            </div>

            <!-- Message d'erreur -->
            <div v-if="errorMessage" class="bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ errorMessage }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <!-- Formulaire -->
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <label for="company_name" class="block text-sm font-medium text-gray-700">
                                    {{ $page.props.translations.settings.company_name }}
                                </label>
                                <input
                                    id="company_name"
                                    type="text"
                                    v-model="form.company_name"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </div>

                            <div>
                                <label for="logo" class="block text-sm font-medium text-gray-700">
                                    {{ $page.props.translations.settings.logo.title }}
                                </label>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ $page.props.translations.settings.logo.requirements }}
                                </p>
                                <input
                                    id="logo"
                                    type="file"
                                    @change="handleFileUpload"
                                    accept=".png,.jpg,.jpeg,.tiff,.webp"
                                    class="mt-2 block w-full text-sm text-gray-500
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-md file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-indigo-50 file:text-indigo-700
                                        hover:file:bg-indigo-100"
                                />
                            </div>

                            <!-- Affichage du logo actuel -->
                            <div v-if="currentLogo" class="mt-4">
                                <p class="text-sm font-medium text-gray-700 mb-2">
                                    {{ $page.props.translations.settings.logo.current }}
                                </p>
                                <div class="relative group w-[250px]">
                                    <img :src="currentLogo" :alt="$page.props.translations.settings.logo.current" class="w-full h-auto rounded-lg shadow-sm">
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-300 rounded-lg flex items-center justify-center">
                                        <button
                                            @click.prevent="showDeleteModal = true"
                                            class="hidden group-hover:block p-2 bg-white rounded-full shadow-lg hover:bg-red-50 transition-colors duration-200"
                                            :title="$page.props.translations.settings.logo.delete"
                                        >
                                            <svg class="w-5 h-5 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Sélection de la langue -->
                            <div>
                                <label for="language" class="block text-sm font-medium text-gray-700">
                                    {{ $page.props.translations.settings.language.title }}
                                </label>
                                <select
                                    id="language"
                                    v-model="form.language"
                                    @change="handleLanguageChange"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="" disabled>{{ $page.props.translations.settings.language.select }}</option>
                                    <option value="fr">{{ $page.props.translations.settings.language.fr }}</option>
                                    <option value="en">{{ $page.props.translations.settings.language.en }}</option>
                                    <option value="de">{{ $page.props.translations.settings.language.de }}</option>
                                </select>
                            </div>

                            <div class="flex justify-end mt-6">
                                <button
                                    type="submit"
                                    class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                    :disabled="form.processing"
                                >
                                    {{ $page.props.translations.common.save }}
                                </button>
                            </div>
                        </form>

                        <!-- Modal de confirmation de suppression -->
                        <Modal :show="showDeleteModal" @close="showDeleteModal = false">
                            <div class="p-6">
                                <h2 class="text-lg font-medium text-gray-900">
                                    {{ $page.props.translations.settings.logo.confirm_delete }}
                                </h2>
                                <p class="mt-3 text-sm text-gray-600">
                                    {{ $page.props.translations.settings.logo.confirm_delete }}
                                </p>
                                <div class="mt-6 flex justify-end space-x-3">
                                    <SecondaryButton @click="showDeleteModal = false">
                                        {{ $page.props.translations.common.cancel }}
                                    </SecondaryButton>
                                    <DangerButton
                                        @click="deleteLogo"
                                        :disabled="isDeleting"
                                    >
                                        {{ $page.props.translations.common.delete }}
                                    </DangerButton>
                                </div>
                            </div>
                        </Modal>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>