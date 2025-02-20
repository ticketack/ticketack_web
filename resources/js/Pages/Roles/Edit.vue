<template>
    <Head :title="$page.props.translations.pages.roles.edit.title" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $page.props.translations.pages.roles.edit.title }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <InputLabel for="name" :value="$page.props.translations.pages.roles.edit.name" />
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.name"
                                    required
                                    :disabled="role.name === 'admin'"
                                />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div>
                                <InputLabel for="description" :value="$page.props.translations.pages.roles.edit.description" />
                                <TextInput
                                    id="description"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.description"
                                />
                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>

                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $page.props.translations.pages.roles.edit.permissions }}</h3>
                                <div class="space-y-6">
                                    <template v-for="group in ['equipements', 'roles', 'users', 'tickets', 'settings']" :key="group">
                                        <div v-if="hasPermissionsForGroup(group)" class="space-y-2">
                                            <h4 class="font-medium text-gray-700">{{ getGroupTranslation(group) }}</h4>
                                            <div class="ml-4 space-y-2">
                                                <div v-for="permission in getPermissionsForGroup(group)" :key="permission.id" class="flex items-center">
                                                    <input
                                                        type="checkbox"
                                                        :id="'permission-' + permission.id"
                                                        :checked="permission.granted"
                                                        @change="togglePermission(permission)"
                                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                        :disabled="role.name === 'admin'"
                                                    >
                                                    <label :for="'permission-' + permission.id" class="ml-2 block text-sm text-gray-900">
                                                        {{ getTranslatedPermission(permission) }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                <InputError class="mt-2" :message="form.errors.permissions" />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <Link
                                    :href="route('roles.index')"
                                    class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors mr-4"
                                >
                                    {{ $page.props.translations.pages.roles.edit.cancel }}
                                </Link>
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    {{ $page.props.translations.pages.roles.edit.save }}
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
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { toast } from '@/utils';

const props = defineProps({
    role: {
        type: Object,
        required: true
    },
    permissions: {
        type: Array,
        required: true
    }
});

const page = usePage();

const getPermissionsForGroup = (group) => {
    return props.permissions.filter(permission => permission.name.startsWith(group + '.'));
};

const hasPermissionsForGroup = (group) => {
    return getPermissionsForGroup(group).length > 0;
};

const getGroupTranslation = (group) => {
    // Les traductions des groupes sont maintenant dans translations.permissions.groups
    return page.props.translations?.permissions?.groups?.[group] || group;
};

const getTranslatedPermission = (permission) => {
    // Debug
    console.log('Permission:', permission);
    console.log('Translations:', page.props.translations.permissions);

    // Utiliser les traductions du fichier permissions.php
    if (permission.name in page.props.translations.permissions) {
        return page.props.translations.permissions[permission.name];
    }

    // Fallback pour les permissions qui n'ont pas de traduction
    const [group, action] = permission.name.split('.');
    if (group in page.props.translations.permissions.groups) {
        const groupTranslation = page.props.translations.permissions.groups[group];
        const actionTranslation = action.charAt(0).toUpperCase() + action.slice(1);
        return `${actionTranslation} - ${groupTranslation}`;
    }

    return permission.name;
};

const form = useForm({
    name: props.role.name,
    description: props.role.description,
    permissions: [...props.permissions]
});

const togglePermission = (permission) => {
    permission.granted = !permission.granted;
    form.permissions = [...props.permissions];
};

const submit = () => {
    form.put(route('roles.update', props.role.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Rôle mis à jour avec succès');
        },
        onError: () => {
            toast.error('Une erreur est survenue lors de la mise à jour');
        }
    });
};
</script>
