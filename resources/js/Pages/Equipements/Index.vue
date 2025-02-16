<template>
    <Head :title="$page.props.translations.pages.equipements.index.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $page.props.translations.pages.equipements.index.title }}
                </h2>
                <Link
                    v-if="$page.props.permissions['equipements.create']"
                    :href="route('equipements.create')"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors"
                >
                    {{ $page.props.translations.pages.equipements.index.new_equipment }}
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="$page.props.flash?.message" class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
                    {{ $page.props.flash.message }}
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="mb-4">
                            <h3 class="text-lg font-medium">{{ $page.props.translations.pages.equipements.index.list }}</h3>
                        </div>
                        <div class="space-y-4">
                            <TreeNode 
                                v-for="equipement in equipements" 
                                :key="equipement.id" 
                                :node="equipement"
                                @edit="editEquipement"
                                @delete="deleteEquipement"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import TreeNode from './Partials/TreeNode.vue';

defineProps({
    equipements: {
        type: Array,
        required: true
    },
    allEquipements: {
        type: Array,
        required: true
    }
});

const editEquipement = (id) => {
    router.visit(route('equipements.edit', id));
};

const deleteEquipement = (id) => {
    if (confirm($page.props.translations.pages.equipements.index.confirm_delete)) {
        router.delete(route('equipements.destroy', id));
    }
};
</script>
