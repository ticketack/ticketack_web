<template>
    <Head :title="page.props.translations.pages.api_doc.title" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ page.props.translations.pages.api_doc.title }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8 relative">
                <!-- Sommaire repliable -->
                <div class="fixed right-0 top-4 flex transition-transform duration-300"
                     :class="{ 'translate-x-[calc(100%-2rem)]': !showSummary }">
                    <!-- Bouton de contrôle -->
                    <button @click="toggleSummary" 
                            class="z-50 h-10 px-2 bg-white shadow-lg rounded-l-lg text-gray-500 hover:text-gray-700 flex items-center justify-center">
                        <svg :class="{ 'rotate-180': !showSummary }" 
                             class="w-5 h-5 transform transition-transform duration-300" 
                             fill="none" 
                             stroke="currentColor" 
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>

                    <!-- Contenu du sommaire -->
                    <div class="w-64 bg-white shadow-lg rounded-l-lg overflow-hidden">
                        <div class="p-4 border-b border-gray-200">
                            <h3 class="font-medium text-gray-900">{{ page.props.translations.pages.api_doc.summary }}</h3>
                        </div>
                        <nav class="p-4 space-y-2">
                            <a v-for="item in tableOfContents" 
                               :key="item.id"
                               :href="`#${item.id}`"
                               class="block text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded px-2 py-1"
                               :class="{ 'pl-4': item.level === 2 }">
                                {{ item.title }}
                            </a>
                        </nav>
                    </div>
                </div>

                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    <!-- Introduction -->
                    <section class="mb-8">
                        <h3 class="mb-4 text-lg font-medium text-gray-900">
                            {{ $page.props.translations.pages.api_doc.introduction.title }}
                        </h3>
                        <p class="text-sm text-gray-600">
                            {{ $page.props.translations.pages.api_doc.introduction.description }}
                        </p>
                    </section>

                    <!-- Authentication -->
                    <section class="mb-8">
                        <h3 class="mb-4 text-lg font-medium text-gray-900">
                            {{ $page.props.translations.pages.api_doc.authentication.title }}
                        </h3>
                        <p class="mb-4 text-sm text-gray-600">
                            {{ $page.props.translations.pages.api_doc.authentication.description }}
                        </p>
                        <div class="rounded-md bg-gray-50 p-4 ml-8">
                            <pre class="text-sm text-gray-700">Authorization: Bearer YOUR_API_TOKEN</pre>
                        </div>
                    </section>

                    <!-- Endpoints -->
                    <section id="endpoints" class="mb-8">
                        <h3 class="mb-4 text-lg font-medium text-gray-900">
                            {{ $page.props.translations.pages.api_doc.endpoints.title }}
                        </h3>

                        <!-- Authentication Endpoints -->
                        <div id="auth-endpoints" class="mb-6">
                            <h4 class="mb-2 font-medium text-gray-800">
                                {{ $page.props.translations.pages.api_doc.endpoints.auth.title }}
                            </h4>
                            
                            <!-- Login -->
                            <div class="mb-2 rounded-md border border-gray-200 p-4 ml-8">
                                <div class="mb-2 flex items-center">
                                    <span class="mr-2 rounded bg-green-500 px-2 py-1 text-xs font-bold text-white">POST</span>
                                    <code class="text-sm">/api/login</code>
                                </div>
                                <p class="mb-2 text-sm text-gray-600">
                                    {{ $page.props.translations.pages.api_doc.endpoints.auth.login.description }}
                                </p>
                                <div class="mb-2">
                                    <h5 class="text-sm font-medium text-gray-700">
                                        {{ $page.props.translations.pages.api_doc.endpoints.body }}:
                                    </h5>
                                    <pre class="mt-1 rounded-md bg-gray-50 p-2 text-sm text-gray-700">{
  "email": "user@example.com",
  "password": "password",
  "device_name": "my_device"
}</pre>
                                </div>
                            </div>

                            <!-- Register -->
                            <div class="mb-2 rounded-md border border-gray-200 p-4 ml-8">
                                <div class="mb-2 flex items-center">
                                    <span class="mr-2 rounded bg-green-500 px-2 py-1 text-xs font-bold text-white">POST</span>
                                    <code class="text-sm">/api/register</code>
                                </div>
                                <p class="mb-2 text-sm text-gray-600">
                                    {{ $page.props.translations.pages.api_doc.endpoints.auth.register.description }}
                                </p>
                                <div class="mb-2">
                                    <h5 class="text-sm font-medium text-gray-700">
                                        {{ $page.props.translations.pages.api_doc.endpoints.body }}:
                                    </h5>
                                    <pre class="mt-1 rounded-md bg-gray-50 p-2 text-sm text-gray-700">{
  "name": "John Doe",
  "email": "user@example.com",
  "password": "password",
  "password_confirmation": "password"
}</pre>
                                </div>
                            </div>

                            <!-- Logout -->
                            <div class="mb-2 rounded-md border border-gray-200 p-4 ml-8">
                                <div class="mb-2 flex items-center">
                                    <span class="mr-2 rounded bg-green-500 px-2 py-1 text-xs font-bold text-white">POST</span>
                                    <code class="text-sm">/api/logout</code>
                                </div>
                                <p class="mb-2 text-sm text-gray-600">
                                    {{ $page.props.translations.pages.api_doc.endpoints.auth.logout.description }}
                                </p>
                                <div class="mb-2">
                                    <h5 class="text-sm font-medium text-gray-700">
                                        {{ $page.props.translations.pages.api_doc.endpoints.headers }}:
                                    </h5>
                                    <pre class="mt-1 rounded-md bg-gray-50 p-2 text-sm text-gray-700">Authorization: Bearer YOUR_API_TOKEN</pre>
                                </div>
                            </div>

                            <!-- User Info -->
                            <div class="rounded-md border border-gray-200 p-4 ml-8">
                                <div class="mb-2 flex items-center">
                                    <span class="mr-2 rounded bg-blue-500 px-2 py-1 text-xs font-bold text-white">GET</span>
                                    <code class="text-sm">/api/user</code>
                                </div>
                                <p class="mb-2 text-sm text-gray-600">
                                    {{ $page.props.translations.pages.api_doc.endpoints.auth.user.description }}
                                </p>
                                <div class="mb-2">
                                    <h5 class="text-sm font-medium text-gray-700">
                                        {{ $page.props.translations.pages.api_doc.endpoints.headers }}:
                                    </h5>
                                    <pre class="mt-1 rounded-md bg-gray-50 p-2 text-sm text-gray-700">Authorization: Bearer YOUR_API_TOKEN</pre>
                                </div>
                            </div>
                        </div>

                        <!-- Equipment Endpoints -->
                        <div id="equipment-endpoints" class="mb-6">
                            <h4 class="mb-2 font-medium text-gray-800">
                                {{ $page.props.translations.pages.api_doc.endpoints.equipment.title }}
                            </h4>

                            <!-- List Equipment -->
                            <div class="mb-2 rounded-md border border-gray-200 p-4 ml-8">
                                <div class="mb-2 flex items-center">
                                    <span class="mr-2 rounded bg-blue-500 px-2 py-1 text-xs font-bold text-white">GET</span>
                                    <code class="text-sm">/api/equipements</code>
                                </div>
                                <p class="mb-2 text-sm text-gray-600">
                                    {{ $page.props.translations.pages.api_doc.endpoints.equipment.list.description }}
                                </p>
                                <div class="mb-2">
                                    <h5 class="text-sm font-medium text-gray-700">
                                        {{ $page.props.translations.pages.api_doc.endpoints.headers }}:
                                    </h5>
                                    <pre class="mt-1 rounded-md bg-gray-50 p-2 text-sm text-gray-700">Authorization: Bearer YOUR_API_TOKEN</pre>
                                </div>
                            </div>

                            <!-- Create Equipment -->
                            <div class="mb-2 rounded-md border border-gray-200 p-4 ml-8">
                                <div class="mb-2 flex items-center">
                                    <span class="mr-2 rounded bg-green-500 px-2 py-1 text-xs font-bold text-white">POST</span>
                                    <code class="text-sm">/api/equipements</code>
                                </div>
                                <p class="mb-2 text-sm text-gray-600">
                                    {{ $page.props.translations.pages.api_doc.endpoints.equipment.create.description }}
                                </p>
                                <div class="mb-2">
                                    <h5 class="text-sm font-medium text-gray-700">
                                        {{ $page.props.translations.pages.api_doc.endpoints.body }}:
                                    </h5>
                                    <pre class="mt-1 rounded-md bg-gray-50 p-2 text-sm text-gray-700">{
  "designation": "Station de travail",
  "marque": "HP",
  "modele": "Z4 G4",
  "image": "workstation.jpg",
  "icone": 1,
  "parent_id": null
}</pre>
                                </div>
                            </div>

                            <!-- Show Equipment -->
                            <div class="mb-2 rounded-md border border-gray-200 p-4 ml-8">
                                <div class="mb-2 flex items-center">
                                    <span class="mr-2 rounded bg-blue-500 px-2 py-1 text-xs font-bold text-white">GET</span>
                                    <code class="text-sm">/api/equipements/{id}</code>
                                </div>
                                <p class="mb-2 text-sm text-gray-600">
                                    {{ $page.props.translations.pages.api_doc.endpoints.equipment.show.description }}
                                </p>
                                <div class="mb-2">
                                    <h5 class="text-sm font-medium text-gray-700">
                                        {{ $page.props.translations.pages.api_doc.endpoints.headers }}:
                                    </h5>
                                    <pre class="mt-1 rounded-md bg-gray-50 p-2 text-sm text-gray-700">Authorization: Bearer YOUR_API_TOKEN</pre>
                                </div>
                            </div>

                            <!-- Update Equipment -->
                            <div class="mb-2 rounded-md border border-gray-200 p-4 ml-8">
                                <div class="mb-2 flex items-center">
                                    <span class="mr-2 rounded bg-yellow-500 px-2 py-1 text-xs font-bold text-white">PATCH</span>
                                    <code class="text-sm">/api/equipements/{id}</code>
                                </div>
                                <p class="mb-2 text-sm text-gray-600">
                                    {{ $page.props.translations.pages.api_doc.endpoints.equipment.update.description }}
                                </p>
                                <div class="mb-2">
                                    <h5 class="text-sm font-medium text-gray-700">
                                        {{ $page.props.translations.pages.api_doc.endpoints.body }}:
                                    </h5>
                                    <pre class="mt-1 rounded-md bg-gray-50 p-2 text-sm text-gray-700">{
  "designation": "Station de travail principale",
  "marque": "HP",
  "modele": "Z4 G4",
  "image": "workstation.jpg",
  "icone": 1,
  "parent_id": 1
}</pre>
                                </div>
                            </div>

                            <!-- Delete Equipment -->
                            <div class="rounded-md border border-gray-200 p-4 ml-8">
                                <div class="mb-2 flex items-center">
                                    <span class="mr-2 rounded bg-red-500 px-2 py-1 text-xs font-bold text-white">DELETE</span>
                                    <code class="text-sm">/api/equipements/{id}</code>
                                </div>
                                <p class="mb-2 text-sm text-gray-600">
                                    {{ $page.props.translations.pages.api_doc.endpoints.equipment.delete.description }}
                                </p>
                                <div class="mb-2">
                                    <h5 class="text-sm font-medium text-gray-700">
                                        {{ $page.props.translations.pages.api_doc.endpoints.headers }}:
                                    </h5>
                                    <pre class="mt-1 rounded-md bg-gray-50 p-2 text-sm text-gray-700">Authorization: Bearer YOUR_API_TOKEN</pre>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Response Codes -->
                    <section id="response-codes">
                        <h3 class="mb-4 text-lg font-medium text-gray-900">
                            {{ $page.props.translations.pages.api_doc.response_codes.title }}
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            {{ $page.props.translations.pages.api_doc.response_codes.code }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            {{ $page.props.translations.pages.api_doc.response_codes.meaning }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    <tr v-for="code in responseCodes" :key="code.code">
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <code class="text-sm">{{ code.code }}</code>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $page.props.translations.pages.api_doc.response_codes[code.key] }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

import { ref, computed } from 'vue';

const page = usePage();

const showSummary = ref(true);
const toggleSummary = () => {
    showSummary.value = !showSummary.value;
};

const tableOfContents = computed(() => [
    { 
        id: 'introduction', 
        title: page.props.translations.pages.api_doc.introduction.title, 
        level: 1 
    },
    { 
        id: 'authentication', 
        title: page.props.translations.pages.api_doc.authentication.title, 
        level: 1 
    },
    { 
        id: 'endpoints', 
        title: page.props.translations.pages.api_doc.endpoints.title, 
        level: 1 
    },
    { 
        id: 'auth-endpoints', 
        title: page.props.translations.pages.api_doc.endpoints.auth.title, 
        level: 2 
    },
    { 
        id: 'equipment-endpoints', 
        title: page.props.translations.pages.api_doc.endpoints.equipment.title, 
        level: 2 
    },
    { 
        id: 'response-codes', 
        title: page.props.translations.pages.api_doc.response_codes.title, 
        level: 1 
    },
]);

const responseCodes = [
    { code: '200', key: 'success' },
    { code: '201', key: 'created' },
    { code: '400', key: 'bad_request' },
    { code: '401', key: 'unauthorized' },
    { code: '403', key: 'forbidden' },
    { code: '404', key: 'not_found' },
    { code: '422', key: 'validation_error' },
    { code: '500', key: 'server_error' }
];
</script>
