<template>
    <Head :title="$page.props.translations.equipment.create.title" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $page.props.translations.equipment.create.title }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <InputLabel for="designation" :value="$page.props.translations.equipment.create.designation" />
                                <TextInput
                                    id="designation"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.designation"
                                    required
                                />
                                <InputError :message="form.errors.designation" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="marque" :value="$page.props.translations.equipment.create.brand" />
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
                                <InputLabel for="modele" :value="$page.props.translations.equipment.create.model" />
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
                                <InputLabel for="parent_id" :value="$page.props.translations.equipment.create.parent_equipment" />
                                <select
                                    id="parent_id"
                                    v-model="form.parent_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                >
                                    <option :value="null">{{ $page.props.translations.equipment.create.no_parent }}</option>
                                    <option v-for="item in allEquipment" :key="item.id" :value="item.id">
                                        {{ item.designation }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.parent_id" class="mt-2" />
                            </div>

                            <div class="flex items-center gap-4">
                                <PrimaryButton :disabled="form.processing">{{ $page.props.translations.equipment.create.create }}</PrimaryButton>
                                <Link :href="route('equipment.index')" class="text-gray-600 hover:text-gray-900">{{ $page.props.translations.equipment.create.cancel }}</Link>
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
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    allEquipment: {
        type: Array,
        required: true
    }
});

const form = useForm({
    designation: '',
    marque: '',
    modele: '',
    parent_id: null
});

const submit = () => {
    form.post(route('equipment.store'));
};
</script>
