<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const showSummary = ref(true);

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

function toggleSummary() {
    showSummary.value = !showSummary.value;
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        // Optionally show a success message
        console.log('Copied to clipboard');
    }).catch(err => {
        console.error('Failed to copy text: ', err);
    });
}
</script>

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
                            <h3 class="font-medium text-gray-900">{{ $page.props.translations.pages.api_doc.summary }}</h3>
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
                            {{ page.props.translations.pages.api_doc.introduction.title }}
                        </h3>
                        <p class="text-sm text-gray-600">
                            {{ page.props.translations.pages.api_doc.introduction.description }}
                        </p>
                    </section>

                    <!-- Authentication -->
                    <section class="mb-8">
                        <h3 class="mb-4 text-lg font-medium text-gray-900">
                            {{ page.props.translations.pages.api_doc.authentication.title }}
                        </h3>
                        <p class="mb-4 text-sm text-gray-600">
                            {{ page.props.translations.pages.api_doc.authentication.description }}
                        </p>
                        <div class="rounded-md bg-gray-50 p-4 ml-8">
                            <pre class="text-sm text-gray-700">Authorization: Bearer YOUR_API_TOKEN</pre>
                        </div>
                    </section>

                    <!-- Endpoints -->
                    <section id="endpoints" class="mb-8">
                        <h3 class="mb-4 text-lg font-medium text-gray-900">
                            {{ page.props.translations.pages.api_doc.endpoints.title }}
                        </h3>

                        <!-- Authentication Endpoints -->
                        <div id="auth-endpoints" class="mb-6">
                            <h4 class="mb-2 font-medium text-gray-800">
                                {{ page.props.translations.pages.api_doc.endpoints.auth.title }}
                            </h4>
                            
                            <!-- Login -->
                            <div class="mb-2 rounded-md border border-gray-200 p-4 ml-8">
                                <!-- URL avec bouton copier -->
                                <div class="mb-4 flex items-center justify-between bg-gray-50 p-2 rounded">
                                    <div class="flex items-center">
                                        <span class="mr-2 rounded bg-green-500 px-2 py-1 text-xs font-bold text-white">POST</span>
                                        <code class="text-sm font-mono">/api/login</code>
                                    </div>
                                    <button @click="copyToClipboard('/api/login')" 
                                            class="p-2 text-gray-500 hover:text-gray-700 focus:outline-none">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Description -->
                                <p class="mb-4 text-sm text-gray-600">
                                    {{ $page.props.translations.pages.api_doc.endpoints.auth.login.token_instructions }}
                                </p>

                                <div class="mb-4 p-4 bg-blue-50 rounded">
                                    <p class="text-sm text-blue-700">
                                        ℹ️ {{ $page.props.translations.pages.api_doc.endpoints.api_token_info }}
                                    </p>
                                </div>

                                <!-- Paramètres -->
                                <div class="mb-4">
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $page.props.translations.pages.api_doc.endpoints.parameters }}</h3>
                                    <div class="bg-gray-50 rounded p-4">
                                        <table class="min-w-full">
                                            <thead>
                                                <tr>
                                                    <th class="text-left text-sm font-medium text-gray-700 pb-2">{{ $page.props.translations.pages.api_doc.endpoints.table_headers.name }}</th>
                                                    <th class="text-left text-sm font-medium text-gray-700 pb-2">{{ $page.props.translations.pages.api_doc.endpoints.table_headers.type }}</th>
                                                    <th class="text-left text-sm font-medium text-gray-700 pb-2">{{ $page.props.translations.pages.api_doc.endpoints.table_headers.description }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-sm text-gray-600 py-2">email</td>
                                                    <td class="text-sm text-gray-600 py-2">string</td>
                                                    <td class="text-sm text-gray-600 py-2">{{ $page.props.translations.pages.api_doc.endpoints.form_labels.email }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-sm text-gray-600 py-2">password</td>
                                                    <td class="text-sm text-gray-600 py-2">string</td>
                                                    <td class="text-sm text-gray-600 py-2">{{ $page.props.translations.pages.api_doc.endpoints.form_labels.password }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-sm text-gray-600 py-2">device_name</td>
                                                    <td class="text-sm text-gray-600 py-2">string</td>
                                                    <td class="text-sm text-gray-600 py-2">{{ $page.props.translations.pages.api_doc.endpoints.form_labels.device_name }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>



                                <!-- Exemple d'utilisation -->
                                <div class="mb-4">
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $page.props.translations.pages.api_doc.endpoints.token_usage }}</h3>
                                    <div class="bg-gray-50 rounded p-4">
                                        <p class="text-sm text-gray-600 mb-2">{{ $page.props.translations.pages.api_doc.authentication.token_header_instruction }}</p>
                                        <pre class="text-sm text-gray-700 font-mono">Authorization: Bearer {{ $page.props.translations.pages.api_doc.endpoints.your_personal_token }}</pre>
                                    </div>
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
  "name": "{{ $page.props.translations.pages.api_doc.endpoints.example_values.name }}",
  "email": "{{ $page.props.translations.pages.api_doc.endpoints.example_values.email }}",
  "password": "{{ $page.props.translations.pages.api_doc.endpoints.example_values.password }}",
  "password_confirmation": "{{ $page.props.translations.pages.api_doc.endpoints.example_values.password }}"
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
                                        {{ $page.props.translations.pages.api_doc.endpoints.table_headers.request_parameters }}
                                    </h5>
                                    <pre class="mt-1 rounded-md bg-gray-50 p-2 text-sm text-gray-700">Authorization: Bearer {{ $page.props.translations.pages.api_doc.endpoints.your_personal_token }}</pre>
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
                                        {{ $page.props.translations.pages.api_doc.endpoints.table_headers.request_parameters }}
                                    </h5>
                                    <pre class="mt-1 rounded-md bg-gray-50 p-2 text-sm text-gray-700">Authorization: Bearer {{ $page.props.translations.pages.api_doc.endpoints.your_personal_token }}</pre>
                                </div>
                            </div>
                        </div>

                        <!-- Equipment Endpoints -->
                        <div id="equipment-endpoints" class="mb-6">
                            <h4 class="mb-2 font-medium text-gray-800">
                                {{ page.props.translations.pages.api_doc.endpoints.equipment.title }}
                            </h4>

                            <!-- List Equipment -->
                            <div class="mb-2 rounded-md border border-gray-200 p-4 ml-8">
                                <!-- URL avec bouton copier -->
                                <div class="mb-4 flex items-center justify-between bg-gray-50 p-2 rounded">
                                    <div class="flex items-center">
                                        <span class="mr-2 rounded bg-blue-500 px-2 py-1 text-xs font-bold text-white">GET</span>
                                        <code class="text-sm font-mono">/api/equipements</code>
                                    </div>
                                    <button @click="copyToClipboard('/api/equipements')" 
                                            class="p-2 text-gray-500 hover:text-gray-700 focus:outline-none">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Paramètres -->
                                <div class="mb-4">
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $page.props.translations.pages.api_doc.endpoints.parameters }}</h3>
                                    <div class="bg-gray-50 rounded p-4">
                                        <p class="text-sm text-gray-600">{{ $page.props.translations.pages.api_doc.endpoints.no_parameters }}</p>
                                    </div>
                                </div>

                                <!-- Format de réponse -->
                                <div class="mb-4">
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $page.props.translations.pages.api_doc.endpoints.response_format }}</h3>
                                    <div class="bg-gray-50 rounded p-4">
                                        <pre class="text-sm text-gray-700 font-mono">{
    "status": "success",
    "data": [
        {
            "id": 1,
            "designation": "Usine de Production",
            "marque": "ID Ingénierie",
            "modele": "Site Principal",
            "image": null,
            "icone": 1,
            "parent_id": null,
            "created_at": "2025-02-15T14:46:57.000000Z",
            "updated_at": "2025-02-15T14:46:57.000000Z",
            "all_children": [
                {
                    "id": 2,
                    "designation": "Ligne de Production 1",
                    "marque": "Siemens",
                    "modele": "LP2000",
                    "image": null,
                    "icone": 2,
                    "parent_id": 1,
                    "created_at": "2025-02-15T14:46:57.000000Z",
                    "updated_at": "2025-02-15T14:46:57.000000Z",
                    "all_children": [
                        {
                            "id": 4,
                            "designation": "Robot de Soudure",
                            "marque": "KUKA",
                            "modele": "KR-150",
                            "image": null,
                            "icone": 3,
                            "parent_id": 2,
                            "created_at": "2025-02-15T14:46:57.000000Z",
                            "updated_at": "2025-02-15T14:46:57.000000Z",
                            "all_children": []
                        }...

}</pre>
                                    </div>
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
                                    <code class="text-sm">/api/equipements/{id}?children=no</code>
                                </div>
                                <p class="mb-2 text-sm text-gray-600">
                                    {{ $page.props.translations.pages.api_doc.endpoints.equipment.show.description }}
                                </p>
                                <div class="mb-2">
                                    <h5 class="text-sm font-medium text-gray-700">
                                        {{ $page.props.translations.pages.api_doc.endpoints.table_headers.request_parameters }}
                                    </h5>
                                    <pre class="mt-1 rounded-md bg-gray-50 p-2 text-sm text-gray-700">Authorization: Bearer {{ $page.props.translations.pages.api_doc.endpoints.your_personal_token }}</pre>
                                </div>
                                <div class="mb-2">
                                    <h5 class="text-sm font-medium text-gray-700">Paramètres de requête :</h5>
                                    <div class="mt-1 rounded-md bg-gray-50 p-2">
                                        <table class="min-w-full">
                                            <thead>
                                                <tr>
                                                    <th class="text-left text-sm font-medium text-gray-700 pb-2">{{ $page.props.translations.pages.api_doc.endpoints.table_headers.name }}</th>
                                                    <th class="text-left text-sm font-medium text-gray-700 pb-2">{{ $page.props.translations.pages.api_doc.endpoints.table_headers.type }}</th>
                                                    <th class="text-left text-sm font-medium text-gray-700 pb-2">{{ $page.props.translations.pages.api_doc.endpoints.table_headers.description }}</th>
                                                    <th class="text-left text-sm font-medium text-gray-700 pb-2">{{ $page.props.translations.pages.api_doc.endpoints.table_headers.required }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-sm text-gray-600 py-2">children</td>
                                                    <td class="text-sm text-gray-600 py-2">string</td>
                                                    <td class="text-sm text-gray-600 py-2">Inclure les équipements enfants (yes/no)</td>
                                                    <td class="text-sm text-gray-600 py-2">Non</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <h5 class="text-sm font-medium text-gray-700">{{ $page.props.translations.pages.api_doc.endpoints.response_format }}</h5>
                                    <div class="mt-1 rounded-md bg-gray-50 p-2">
                                        <pre class="text-sm text-gray-700 font-mono">{
    "status": "success",
    "data": {
        "id": 3,
        "designation": "Ligne de Production 2",
        "marque": "ABB",
        "modele": "LP3000",
        "image": null,
        "icone": 2,
        "parent_id": 1,
        "created_at": "2025-02-15T14:46:57.000000Z",
        "updated_at": "2025-02-15T14:46:57.000000Z"
    }
}</pre>
                                    </div>
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
  "designation": "{{ $page.props.translations.pages.api_doc.endpoints.example_values.workstation_name }}",
  "marque": "{{ $page.props.translations.pages.api_doc.endpoints.example_values.workstation_brand }}",
  "modele": "{{ $page.props.translations.pages.api_doc.endpoints.example_values.workstation_model }}",
  "image": "{{ $page.props.translations.pages.api_doc.endpoints.example_values.workstation_image }}",
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
                                    {{ page.props.translations.pages.api_doc.endpoints.equipment.delete.description }}
                                </p>
                                <div class="mb-2">
                                    <h5 class="text-sm font-medium text-gray-700">
                                        {{ $page.props.translations.pages.api_doc.endpoints.table_headers.request_parameters }}
                                    </h5>
                                    <pre class="mt-1 rounded-md bg-gray-50 p-2 text-sm text-gray-700">Authorization: Bearer {{ $page.props.translations.pages.api_doc.endpoints.your_personal_token }}</pre>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Response Codes -->
                    <section id="response-codes">
                        <h3 class="mb-4 text-lg font-medium text-gray-900">
                            {{ page.props.translations.pages.api_doc.response_codes.title }}
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            {{ page.props.translations.pages.api_doc.response_codes.code }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            {{ page.props.translations.pages.api_doc.response_codes.meaning }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    <tr v-for="code in responseCodes" :key="code.code">
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <code class="text-sm">{{ code.code }}</code>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ page.props.translations.pages.api_doc.response_codes[code.key] }}
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
