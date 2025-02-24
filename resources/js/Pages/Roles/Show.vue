<template>
    <Head :title="$page.props.translations.roles.show.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $page.props.translations.roles.show.title }}
                </h2>
                <Link
                    v-if="$page.props.can.roles.edit"
                    :href="route('roles.edit', role.id)"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                >
                    {{ $page.props.translations.roles.show.edit }}
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="space-y-6">
                            <!-- Nom -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">
                                    {{ $page.props.translations.roles.show.name }}
                                </h3>
                                <p class="mt-1 text-sm text-gray-600">
                                    {{ role.name }}
                                </p>
                            </div>

                            <!-- Description -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">
                                    {{ $page.props.translations.roles.show.description }}
                                </h3>
                                <p class="mt-1 text-sm text-gray-600">
                                    {{ role.description || $page.props.translations.roles.show.no_description }}
                                </p>
                            </div>

                            <!-- Permissions -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">
                                    {{ $page.props.translations.roles.show.permissions }}
                                </h3>
                                <div class="space-y-6">
                                    <template v-for="group in ['equipment', 'roles', 'users', 'tickets', 'settings']" :key="group">
                                        <div v-if="hasPermissionsForGroup(group)" class="space-y-2">
                                            <h4 class="font-medium text-gray-700">{{ getGroupTranslation(group) }}</h4>
                                            <div class="ml-4 space-y-2">
                                                <div v-for="permission in getPermissionsForGroup(group)" :key="permission.id" class="flex items-center">
                                                    <svg 
                                                        class="h-5 w-5 text-green-500" 
                                                        xmlns="http://www.w3.org/2000/svg" 
                                                        viewBox="0 0 20 20" 
                                                        fill="currentColor"
                                                    >
                                                        <path 
                                                            fill-rule="evenodd" 
                                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" 
                                                            clip-rule="evenodd" 
                                                        />
                                                    </svg>
                                                    <span class="ml-2 text-sm text-gray-900">
                                                        {{ getTranslatedPermission(permission) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <Link
                                    :href="route('roles.index')"
                                    class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors"
                                >
                                    {{ $page.props.translations.roles.show.back }}
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

const props = defineProps({
    role: {
        type: Object,
        required: true
    }
});

const page = usePage();

const getPermissionsForGroup = (group) => {
    return props.role.permissions.filter(permission => permission.name.startsWith(group + '.'));
};

const hasPermissionsForGroup = (group) => {
    return getPermissionsForGroup(group).length > 0;
};

const getGroupTranslation = (group) => {
    return page.props.translations?.permissions?.groups?.[group] || group;
};

const getTranslatedPermission = (permission) => {
    if (permission.name in page.props.translations.permissions) {
        return page.props.translations.permissions[permission.name];
    }

    const [group, action] = permission.name.split('.');
    if (group in page.props.translations.permissions.groups) {
        const groupTranslation = page.props.translations.permissions.groups[group];
        const actionTranslation = action.charAt(0).toUpperCase() + action.slice(1);
        return `${actionTranslation} - ${groupTranslation}`;
    }

    return permission.name;
};
</script>
