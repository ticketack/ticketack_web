<template>
    <Head :title="$page.props.translations.pages.equipment.edit.title" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $page.props.translations.pages.equipment.edit.title }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <InputLabel for="designation" :value="$page.props.translations.pages.equipment.edit.designation" />
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
                                <InputLabel for="marque" :value="$page.props.translations.pages.equipment.edit.marque" />
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
                                <InputLabel for="modele" :value="$page.props.translations.pages.equipment.edit.modele" />
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
                                <InputLabel for="image" :value="$page.props.translations.pages.equipment.edit.image" />
                                <div class="mt-2 space-y-2">
                                    <!-- Prévisualisation de l'image existante -->
                                    <div v-if="props.equipement.image || imagePreview" class="relative w-64 h-64 border rounded-lg overflow-hidden">
                                        <img 
                                            :src="imagePreview || props.equipement.image" 
                                            class="w-full h-full object-cover"
                                            alt="Prévisualisation"
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

                            <div>
                                <InputLabel for="icone" :value="$page.props.translations.pages.equipment.edit.icone" />
                                <div class="mt-1 grid grid-cols-3 gap-3">
                                    <label 
                                        v-for="icon in icons" 
                                        :key="icon.value"
                                        class="relative flex items-center justify-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors"
                                        :class="{ 'border-indigo-500 ring-2 ring-indigo-500': form.icone === icon.value }"
                                    >
                                        <input 
                                            type="radio" 
                                            :value="icon.value" 
                                            v-model="form.icone"
                                            class="sr-only"
                                        />
                                        <div class="text-center">
                                            <component :is="icon.component" class="w-8 h-8 mx-auto" />
                                            <span class="mt-2 block text-sm font-medium text-gray-900">{{ icon.label }}</span>
                                        </div>
                                    </label>
                                </div>
                                <InputError :message="form.errors.icone" class="mt-2" />
                            </div>

                            <div class="flex items-center gap-4">
                                <PrimaryButton :disabled="form.processing">{{ $page.props.translations.pages.equipment.edit.save }}</PrimaryButton>
                                <Link :href="route('equipements.index')" class="text-gray-600 hover:text-gray-900">{{ $page.props.translations.pages.equipment.edit.cancel }}</Link>
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
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, defineComponent } from 'vue';

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

const icons = [
    { 
        value: 1, 
        label: 'Usine',
        component: defineComponent({
            template: `
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            `
        })
    },
    { 
        value: 2, 
        label: 'Ligne',
        component: defineComponent({
            template: `
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            `
        })
    },
    { 
        value: 3, 
        label: 'Robot',
        component: defineComponent({
            template: `
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                </svg>
            `
        })
    },
    { 
        value: 4, 
        label: 'Convoyeur',
        component: defineComponent({
            template: `
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            `
        })
    },
    { 
        value: 5, 
        label: 'Machine',
        component: defineComponent({
            template: `
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            `
        })
    },
    { 
        value: 6, 
        label: 'Station',
        component: defineComponent({
            template: `
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            `
        })
    },
];

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
        router.delete(route('equipements.delete-image', props.equipement.id), {
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
    form.put(route('equipements.update', props.equipement.id));
};
</script>
