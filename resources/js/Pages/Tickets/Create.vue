<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Créer un nouveau ticket
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6">
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
                                />
                                <InputError :message="form.errors.due_date" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6 gap-4">
                            <Link :href="route('tickets.index')" class="text-gray-600 hover:text-gray-900">Annuler</Link>
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Créer le ticket
                            </PrimaryButton>
                        </div>
                    </form>
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
import Autocomplete from '@/Components/Autocomplete.vue';

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
    due_date: ''
});

const submit = () => {
    form.post(route('tickets.store'));
};
</script>
