<template>
    <Head :title="$page.props.translations.equipment.edit.title" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $page.props.translations.equipment.edit.title }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-8xl mx-auto sm:px-2 lg:px-2">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    
                    <div class="p-6 text-gray-900">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4 p-6 pb-0">
                            {{ $page.props.translations.equipment.edit.title }}
                        </h2>
                        <form @submit.prevent="submit" class="space-y-6 p-6 pt-2">
                            <div>
                                <InputLabel for="designation" :value="page.props.translations.equipment.edit.designation" />
                                <TextInput
                                    id="designation"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.designation"
                                    required
                                    autofocus
                                />
                                <InputError :message="form.errors.designation" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="marque" :value="$page.props.translations.equipment.edit.marque" />
                                <TextInput
                                    id="marque"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.marque"
                                    required
                                />
                                <InputError :message="form.errors.marque" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="modele" :value="$page.props.translations.equipment.edit.modele" />
                                <TextInput
                                    id="modele"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.modele"
                                    required
                                />
                                <InputError :message="form.errors.modele" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="image" :value="$page.props.translations.equipment.edit.image" />
                                <div class="mt-2 space-y-2">
                                    <!-- Prévisualisation de l'image existante -->
                                    <div v-if="props.equipment.image || imagePreview" class="relative w-64 h-64 border rounded-lg overflow-hidden">
                                        <img 
                                            :src="imagePreview || props.equipment.image" 
                                            class="w-full h-full object-cover"
                                            :alt="$page.props.translations.equipment.edit.image_previewge_preview"
                                        />
                                        <button 
                                            @click.prevent="deleteImage"
                                            type="button"
                                            class="absolute top-2 right-2 p-1 bg-red-600 text-white rounded-full hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Input pour sélectionner une nouvelle image -->
                                    <input
                                        type="file"
                                        id="image"
                                        class="mt-1 block w-full"
                                        @input="handleImageInput"
                                        accept="image/*"
                                        ref="imageInput"
                                    />
                                    <InputError :message="form.errors.image" class="mt-2" />
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <PrimaryButton :disabled="form.processing">{{ $page.props.translations.equipment.edit.save }}</PrimaryButton>
                                <Link :href="route('equipment.show', props.equipment.id)" class="text-gray-600 hover:text-gray-900">{{ $page.props.translations.equipment.edit.cancel }}</Link>
                            </div>
                        </form>
                        
                        <!-- Section des documents (toujours visible) -->
                        <div class="mt-6 border-t border-gray-200 pt-6 px-6">
                            <DocumentList 
                                :equipment="props.equipment" 
                                :documents="documents"
                                :can-create="can.create"
                                :can-edit="can.edit"
                                :can-delete="can.delete"
                                @document-added="refreshDocuments"
                            />
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
import DocumentList from '@/Pages/Equipment/Partials/Documents/DocumentList.vue';
import { Head, Link, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, defineComponent, onMounted } from 'vue';
import axios from 'axios';

const page = usePage();

// Fonction pour rafraîchir les documents après ajout
const refreshDocuments = () => {
    fetchDocuments();
};

// Documents de l'équipement
const documents = ref([]);

// Permissions
const can = ref({
    create: false,
    edit: false,
    delete: false
});

// Chargement des documents
onMounted(() => {
    fetchDocuments();
});

// Récupération des documents
const fetchDocuments = async () => {
    try {
        // Utiliser fetch pour faire une requête AJAX
        const response = await fetch(route('equipment.documents.index', props.equipment.id), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            // Ajouter les credentials pour s'assurer que les cookies d'authentification sont envoyés
            credentials: 'same-origin'
        });
        
        if (response.ok) {
            const data = await response.json();
            documents.value = data.documents || [];
            can.value = data.can || {
                create: false,
                edit: false,
                delete: false
            };
        } else {
            console.error(`Erreur lors de la récupération des documents: ${response.status} ${response.statusText}`);
            // Essayer de lire le message d'erreur du serveur si disponible
            try {
                const errorData = await response.json();
                console.error('Détails de l\'erreur:', errorData);
            } catch (e) {
                // Ignorer si nous ne pouvons pas lire le JSON
            }
            
            // Utiliser la méthode de secours avec Inertia si fetch échoue
            fallbackFetchDocuments();
        }
    } catch (error) {
        console.error('Erreur lors de la récupération des documents:', error);
        // Utiliser la méthode de secours avec Inertia si fetch échoue
        fallbackFetchDocuments();
    }
};

// Méthode de secours utilisant Inertia.js
const fallbackFetchDocuments = () => {
    router.get(route('equipment.documents.index', props.equipment.id), {}, {
        preserveState: true,
        preserveScroll: true,
        only: ['documents', 'can'],
        onSuccess: (page) => {
            documents.value = page.props.documents || [];
            can.value = page.props.can || {
                create: false,
                edit: false,
                delete: false
            };
        },
        onError: (errors) => {
            console.error('Erreur Inertia lors de la récupération des documents:', errors);
        }
    });
};

const props = defineProps({
    equipment: {
        type: Object,
        required: true
    },
    allEquipment: {
        type: Array,
        required: true
    }
});

const imagePreview = ref(null);
const imageInput = ref(null);

const form = useForm({
    designation: props.equipment.designation,
    marque: props.equipment.marque,
    modele: props.equipment.modele,
    image: props.equipment.image, // Conserver l'URL de l'image existante
    icone: props.equipment.icone,
    parent_id: props.equipment.parent_id
});

const handleImageInput = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.image = file;
        const reader = new FileReader();
        reader.onload = e => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const deleteImage = () => {
    if (props.equipment.image) {
        // Supprimer l'image existante via l'API
        router.delete(route('equipment.delete-image', props.equipment.id), {
            onSuccess: () => {
                props.equipment.image = null;
                imagePreview.value = null;
                form.image = null; // Important : mettre à null dans le formulaire
                if (imageInput.value) {
                    imageInput.value.value = '';
                }
            }
        });
    } else {
        // Juste réinitialiser la prévisualisation
        imagePreview.value = null;
        form.image = null;
        if (imageInput.value) {
            imageInput.value.value = '';
        }
    }
};

const submit = () => {
    // Utiliser FormData pour tous les cas pour une cohérence de traitement
    const formData = new FormData();
    formData.append('_method', 'PUT'); // Simuler une requête PUT
    formData.append('designation', form.designation);
    formData.append('marque', form.marque);
    formData.append('modele', form.modele);
    
    // Gestion de l'image selon son type
    if (form.image instanceof File) {
        formData.append('image', form.image);
    } else if (typeof form.image === 'string' && form.image) {
        // Si c'est une URL existante, on l'envoie comme chaîne
        formData.append('image', form.image);
    }
    
    // Ajouter les autres champs s'ils existent
    if (form.icone !== null) formData.append('icone', form.icone);
    if (form.parent_id !== null) formData.append('parent_id', form.parent_id);
    
    // Réinitialiser les erreurs avant l'envoi
    form.clearErrors();
    form.processing = true;
    
    // Utiliser axios directement pour plus de contrôle
    axios.post(route('equipment.update', props.equipment.id), formData)
        .then(response => {
            form.processing = false;
            router.visit(route('equipment.show', props.equipment.id));
        })
        .catch(error => {
            form.processing = false;
            if (error.response && error.response.data && error.response.data.errors) {
                // Copier les erreurs dans l'objet form.errors
                Object.keys(error.response.data.errors).forEach(key => {
                    form.setError(key, error.response.data.errors[key][0]);
                });
            } else {
                console.error('Erreur lors de la mise à jour:', error);
            }
        });
};
</script>
