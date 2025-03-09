<template>
    <Head :title="$page.props.translations.roles.index.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $page.props.translations.roles.index.title }}
                </h2>
                <Link
                    v-if="$page.props.permissions['roles.create']"
                    :href="route('roles.create')"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors"
                >
                    {{ $page.props.translations.roles.index.new_role }}
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-8xl mx-auto sm:px-2 lg:px-2">
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
                                        {{ $page.props.translations.roles.index.name }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ $page.props.translations.roles.index.description }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ $page.props.translations.roles.index.permissions }}
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ $page.props.translations.roles.index.actions }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="role in roles" :key="role.id">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ role.name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ role.description }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-2">
                                            <span 
                                                v-for="permission in role.permissions.filter(p => p.granted)" 
                                                :key="permission.id"
                                                class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800"
                                            >
                                                {{ permission.name }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <Link
                                            v-if="$page.props.permissions['roles.edit']"
                                            :href="route('roles.edit', role.id)"
                                            class="text-indigo-600 hover:text-indigo-900 mr-4"
                                        >
                                            {{ $page.props.translations.roles.index.edit }}
                                        </Link>
                                        <button
                                            v-if="$page.props.permissions['roles.delete'] && role.name !== 'admin'"
                                            @click="deleteRole(role)"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            {{ $page.props.translations.roles.index.delete }}
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
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    roles: {
        type: Array,
        required: true
    }
});

const deleteRole = (role) => {
    if (confirm($page.props.translations.roles.confirm_delete.replace(':name', role.name))) {
        router.delete(route('roles.destroy', role.id));
    }
};
</script>
