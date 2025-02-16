<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

import { ref } from 'vue';

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    apiToken: {
        type: String,
        required: true,
    },
});

const copied = ref(false);

const copyToken = () => {
    navigator.clipboard.writeText(props.apiToken);
    copied.value = true;
    setTimeout(() => {
        copied.value = false;
    }, 2000);
};
</script>

<template>
    <Head :title="$page.props.translations.pages.profile.title" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                {{ $page.props.translations.pages.profile.title }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8"
                >
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8"
                >
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8"
                >
                    <DeleteUserForm class="max-w-xl" />
                </div>

                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    <section class="max-w-xl">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">{{ $page.props.translations.pages.profile.api_token.title }}</h2>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ $page.props.translations.pages.profile.api_token.description }}
                            </p>
                        </header>

                        <div class="mt-6">
                            <div class="flex items-center gap-4">
                                <div class="relative flex-grow">
                                    <input
                                        type="text"
                                        :value="apiToken"
                                        readonly
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                </div>
                                <button
                                    type="button"
                                    @click="copyToken"
                                    class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-indigo-700 focus:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-indigo-900"
                                >
                                    {{ copied ? $page.props.translations.pages.profile.api_token.copied : $page.props.translations.pages.profile.api_token.copy }}
                                </button>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
