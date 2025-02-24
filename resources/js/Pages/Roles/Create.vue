<template>
    <Head :title="$page.props.translations.roles.create.title" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $page.props.translations.roles.create.title }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <InputLabel for="name" :value="$page.props.translations.roles.create.name" />
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.name"
                                    required
                                    autofocus
                                />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div>
                                <InputLabel for="description" :value="$page.props.translations.roles.create.description" />
                                <TextInput
                                    id="description"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.description"
                                />
                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>

                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $page.props.translations.roles.create.permissions }}</h3>
                                <div class="space-y-4">
                                    <div v-for="permission in permissions" :key="permission.id" class="flex items-center">
                                        <input
                                            type="checkbox"
                                            :id="'permission-' + permission.id"
                                            v-model="form.permissions[permission.id].granted"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                        >
                                        <label :for="'permission-' + permission.id" class="ml-2 block text-sm text-gray-900">
                                            {{ permission.name }}
                                        </label>
                                    </div>
                                </div>
                                <InputError class="mt-2" :message="form.errors.permissions" />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <Link
                                    :href="route('roles.index')"
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors mr-4"
                                >
                                    {{ $page.props.translations.roles.create.cancel }}
                                </Link>
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    {{ $page.props.translations.roles.create.create }}
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
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    permissions: {
        type: Array,
        required: true
    }
});

const form = useForm({
    name: '',
    description: '',
    permissions: props.permissions.reduce((acc, permission) => {
        acc[permission.id] = { id: permission.id, granted: false };
        return acc;
    }, {})
});

const submit = () => {
    form.post(route('roles.store'), {
        onSuccess: () => {
            form.reset();
        },
        preserveScroll: true,
    });
};
</script>
