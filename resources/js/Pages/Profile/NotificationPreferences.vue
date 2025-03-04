<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, computed } from 'vue';
import { useToast } from 'vue-toastification';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputError from '@/Components/InputError.vue';
import { Switch } from '@headlessui/vue';

const props = defineProps({
    preferences: Array,
    user: Object,
});

const toast = useToast();

// Formulaire pour les préférences
const form = useForm({
    preferences: props.preferences,
    phone_number: props.user.phone_number || '',
});

// Fonction pour mettre à jour les préférences
const updatePreferences = () => {
    form.post(route('profile.notifications.preferences.update'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Préférences de notifications mises à jour avec succès');
        },
        onError: (errors) => {
            toast.error('Une erreur est survenue lors de la mise à jour des préférences');
        },
    });
};

// Fonction pour activer/désactiver toutes les notifications d'un type
const toggleAllForType = (type, value) => {
    form.preferences.forEach(pref => {
        if (pref.notification_type_key === type) {
            pref.in_app = value;
            pref.email = value;
            pref.sms = value;
        }
    });
};

// Fonction pour activer/désactiver toutes les notifications d'un canal
const toggleAllForChannel = (channel, value) => {
    form.preferences.forEach(pref => {
        pref[channel] = value;
    });
};

// Vérifier si toutes les notifications d'un canal sont activées
const allChannelEnabled = computed(() => {
    return {
        in_app: form.preferences.every(pref => pref.in_app),
        email: form.preferences.every(pref => pref.email),
        sms: form.preferences.every(pref => pref.sms),
    };
});

// Vérifier si toutes les notifications d'un type sont activées
const allTypeEnabled = computed(() => {
    const result = {};
    form.preferences.forEach(pref => {
        result[pref.notification_type_key] = pref.in_app && pref.email && pref.sms;
    });
    return result;
});

// Vérifier si au moins une notification d'un canal est activée
const someChannelEnabled = computed(() => {
    return {
        in_app: form.preferences.some(pref => pref.in_app),
        email: form.preferences.some(pref => pref.email),
        sms: form.preferences.some(pref => pref.sms),
    };
});

// Vérifier si au moins une notification d'un type est activée
const someTypeEnabled = computed(() => {
    const result = {};
    form.preferences.forEach(pref => {
        result[pref.notification_type_key] = pref.in_app || pref.email || pref.sms;
    });
    return result;
});

// Fonction pour formater le nom du type de notification
const formatTypeName = (key) => {
    const pref = form.preferences.find(p => p.notification_type_key === key);
    return pref ? pref.notification_type_name : key;
};
</script>

<template>
    <Head title="Préférences de notifications" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Préférences de notifications
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold mb-4">Informations de contact</h3>
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <InputLabel for="phone_number" value="Numéro de téléphone (pour les SMS)" />
                                    <TextInput
                                        id="phone_number"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.phone_number"
                                        placeholder="+33612345678"
                                    />
                                    <InputError class="mt-2" :message="form.errors.phone_number" />
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        Format international recommandé (ex: +33612345678)
                                    </p>
                                </div>

                            </div>
                        </div>

                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-4">Préférences de notifications</h3>
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Type de notification
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                <div class="flex items-center">
                                                    <span>In-App</span>
                                                    <Switch
                                                        v-model="allChannelEnabled.in_app"
                                                        @update:model-value="toggleAllForChannel('in_app', $event)"
                                                        :class="[
                                                            allChannelEnabled.in_app ? 'bg-indigo-600' : someChannelEnabled.in_app ? 'bg-indigo-400' : 'bg-gray-200 dark:bg-gray-600',
                                                            'relative inline-flex h-5 w-10 ml-2 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2'
                                                        ]"
                                                    >
                                                        <span
                                                            aria-hidden="true"
                                                            :class="[
                                                                allChannelEnabled.in_app ? 'translate-x-5' : 'translate-x-0',
                                                                'pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out'
                                                            ]"
                                                        />
                                                    </Switch>
                                                </div>
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                <div class="flex items-center">
                                                    <span>Email</span>
                                                    <Switch
                                                        v-model="allChannelEnabled.email"
                                                        @update:model-value="toggleAllForChannel('email', $event)"
                                                        :class="[
                                                            allChannelEnabled.email ? 'bg-indigo-600' : someChannelEnabled.email ? 'bg-indigo-400' : 'bg-gray-200 dark:bg-gray-600',
                                                            'relative inline-flex h-5 w-10 ml-2 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2'
                                                        ]"
                                                    >
                                                        <span
                                                            aria-hidden="true"
                                                            :class="[
                                                                allChannelEnabled.email ? 'translate-x-5' : 'translate-x-0',
                                                                'pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out'
                                                            ]"
                                                        />
                                                    </Switch>
                                                </div>
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                <div class="flex items-center">
                                                    <span>SMS</span>
                                                    <Switch
                                                        v-model="allChannelEnabled.sms"
                                                        @update:model-value="toggleAllForChannel('sms', $event)"
                                                        :class="[
                                                            allChannelEnabled.sms ? 'bg-indigo-600' : someChannelEnabled.sms ? 'bg-indigo-400' : 'bg-gray-200 dark:bg-gray-600',
                                                            'relative inline-flex h-5 w-10 ml-2 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2'
                                                        ]"
                                                    >
                                                        <span
                                                            aria-hidden="true"
                                                            :class="[
                                                                allChannelEnabled.sms ? 'translate-x-5' : 'translate-x-0',
                                                                'pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out'
                                                            ]"
                                                        />
                                                    </Switch>
                                                </div>
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Tout
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        <tr v-for="(preference, index) in form.preferences" :key="preference.notification_type_id">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ preference.notification_type_name }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ preference.notification_type_description }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <Switch
                                                    v-model="preference.in_app"
                                                    :class="[
                                                        preference.in_app ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-600',
                                                        'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2'
                                                    ]"
                                                >
                                                    <span
                                                        aria-hidden="true"
                                                        :class="[
                                                            preference.in_app ? 'translate-x-5' : 'translate-x-0',
                                                            'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out'
                                                        ]"
                                                    />
                                                </Switch>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <Switch
                                                    v-model="preference.email"
                                                    :class="[
                                                        preference.email ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-600',
                                                        'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2'
                                                    ]"
                                                >
                                                    <span
                                                        aria-hidden="true"
                                                        :class="[
                                                            preference.email ? 'translate-x-5' : 'translate-x-0',
                                                            'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out'
                                                        ]"
                                                    />
                                                </Switch>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <Switch
                                                    v-model="preference.sms"
                                                    :class="[
                                                        preference.sms ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-600',
                                                        'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2'
                                                    ]"
                                                >
                                                    <span
                                                        aria-hidden="true"
                                                        :class="[
                                                            preference.sms ? 'translate-x-5' : 'translate-x-0',
                                                            'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out'
                                                        ]"
                                                    />
                                                </Switch>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <Switch
                                                    :model-value="allTypeEnabled[preference.notification_type_key]"
                                                    @update:model-value="toggleAllForType(preference.notification_type_key, $event)"
                                                    :class="[
                                                        allTypeEnabled[preference.notification_type_key] ? 'bg-indigo-600' : someTypeEnabled[preference.notification_type_key] ? 'bg-indigo-400' : 'bg-gray-200 dark:bg-gray-600',
                                                        'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2'
                                                    ]"
                                                >
                                                    <span
                                                        aria-hidden="true"
                                                        :class="[
                                                            allTypeEnabled[preference.notification_type_key] ? 'translate-x-5' : 'translate-x-0',
                                                            'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out'
                                                        ]"
                                                    />
                                                </Switch>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <SecondaryButton
                                :href="route('profile.edit')"
                                class="mr-3"
                            >
                                Retour au profil
                            </SecondaryButton>
                            <PrimaryButton
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                                @click="updatePreferences"
                            >
                                Enregistrer les préférences
                            </PrimaryButton>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
