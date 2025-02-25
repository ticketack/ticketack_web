<template>
    <Head :title="$page.props.translations.equipment.edit.title" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $page.props.translations.equipment.edit.title }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit" class="space-y-6">
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
                                    <div v-if="props.equipement.image || imagePreview" class="relative w-64 h-64 border rounded-lg overflow-hidden">
                                        <img 
                                            :src="imagePreview || props.equipement.image" 
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
                                <Link :href="route('equipment.index')" class="text-gray-600 hover:text-gray-900">{{ $page.props.translations.equipment.edit.cancel }}</Link>
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
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, defineComponent } from 'vue';

const page = usePage();

const props = defineProps({
    equipement: {
        type: Object,
        required: true
    },
    equipements: {
        type: Array,
        required: true
    }
});

const imagePreview = ref(null);
const imageInput = ref(null);

const form = useForm({
    designation: props.equipement.designation,
    marque: props.equipement.marque,
    modele: props.equipement.modele,
    image: null,
    icone: props.equipement.icone,
    parent_id: props.equipement.parent_id
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
    if (props.equipement.image) {
        // Supprimer l'image existante via l'API
        router.delete(route('equipment.delete-image', props.equipement.id), {
            onSuccess: () => {
                props.equipement.image = null;
                imagePreview.value = null;
                if (form.image) {
                    form.image = null;
                    if (imageInput.value) {
                        imageInput.value.value = '';
                    }
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
    form.put(route('equipment.update', props.equipment.id));
};
</script>
