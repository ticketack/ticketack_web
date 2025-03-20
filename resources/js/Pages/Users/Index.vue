<template>
    <Head :title="$page.props.translations.users.index.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $page.props.translations.users.index.title }}
                </h2>
                <Link
                    v-if="$page.props.permissions['users.create']"
                    :href="route('users.create')"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors"
                >
                    {{ $page.props.translations.users.index.new_user }}
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="$page.props.flash?.message" class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
                    {{ $page.props.flash.message }}
                </div>

                <div v-if="$page.props.flash?.error" class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">
                    {{ $page.props.flash.error }}
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ $page.props.translations.users.index.name }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ $page.props.translations.users.index.email }}

                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ $page.props.translations.users.index.roles }}
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ $page.props.translations.users.index.actions }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="user in users" :key="user.id">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ user.name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ user.email }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-2">
                                            <span 
                                                v-for="role in user.roles" 
                                                :key="role.id"
                                                class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800"
                                            >
                                                {{ role.name }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <Link
                                            v-if="$page.props.permissions['users.edit'] && user.email !== 'admin@example.com'"
                                            :href="route('users.edit', user.id)"
                                            class="text-indigo-600 hover:text-indigo-900 mr-4"
                                        >
                                        {{ $page.props.translations.users.index.edit }}
                                        </Link>
                                        <button
                                            v-if="$page.props.permissions['users.delete'] && user.email !== 'admin@example.com'"
                                            @click="deleteUser(user)"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                        {{ $page.props.translations.users.index.delete }}
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';

defineProps({
    users: {
        type: Array,
        required: true
    }
});

const toast = useToast();

const deleteUser = (user) => {
    const page = usePage();
    const confirmMessage = page.props.translations.users.index.confirm_delete.replace('{name}', user.name);
    if (confirm(confirmMessage)) {
        router.delete(route('users.destroy', user.id), {
            onSuccess: () => {
                toast.success(page.props.translations.users.index.deleted_success || 'Utilisateur supprimé avec succès');
            },
            onError: (errors) => {
                toast.error(Object.values(errors).join('\n') || 'Erreur lors de la suppression de l\'utilisateur');
            }
        });
    }
};
</script>
