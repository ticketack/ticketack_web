<template>
    <Head title="Modifier l'utilisateur" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Modifier l'utilisateur
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <InputLabel for="name" value="Nom" />
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.name"
                                    required
                                    :disabled="user.email === 'admin@example.com'"
                                />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div>
                                <InputLabel for="email" value="Email" />
                                <TextInput
                                    id="email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    v-model="form.email"
                                    required
                                    :disabled="user.email === 'admin@example.com'"
                                />
                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>

                            <div>
                                <InputLabel for="password" value="Nouveau mot de passe (optionnel)" />
                                <TextInput
                                    id="password"
                                    type="password"
                                    class="mt-1 block w-full"
                                    v-model="form.password"
                                    :disabled="user.email === 'admin@example.com'"
                                />
                                <InputError class="mt-2" :message="form.errors.password" />
                            </div>

                            <div>
                                <InputLabel for="password_confirmation" value="Confirmer le nouveau mot de passe" />
                                <TextInput
                                    id="password_confirmation"
                                    type="password"
                                    class="mt-1 block w-full"
                                    v-model="form.password_confirmation"
                                    :disabled="user.email === 'admin@example.com'"
                                />
                            </div>

                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Rôles</h3>
                                <div class="space-y-4">
                                    <div v-for="role in roles" :key="role.id" class="flex items-center">
                                        <input
                                            type="checkbox"
                                            :id="'role-' + role.id"
                                            v-model="form.roles"
                                            :value="role.id"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                            :disabled="user.email === 'admin@example.com'"
                                        >
                                        <label :for="'role-' + role.id" class="ml-2 block text-sm text-gray-900">
                                            {{ role.name }}
                                        </label>
                                    </div>
                                </div>
                                <InputError class="mt-2" :message="form.errors.roles" />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <Link
                                    :href="route('users.index')"
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors mr-4"
                                >
                                    Annuler
                                </Link>
                                <PrimaryButton 
                                    :class="{ 'opacity-25': form.processing }" 
                                    :disabled="form.processing || user.email === 'admin@example.com'"
                                >
                                    Mettre à jour
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
    user: {
        type: Object,
        required: true
    },
    roles: {
        type: Array,
        required: true
    }
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    password_confirmation: '',
    roles: props.user.roles.map(role => role.id),
});

const submit = () => {
    form.put(route('users.update', props.user.id), {
        preserveScroll: true,
    });
};
</script>
