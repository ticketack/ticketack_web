<template>
    <div class="prime-tree-container">
        <h3 class="text-lg font-medium mb-2">Arborescence des équipements</h3>
        
        <!-- Composant Tree de PrimeVue -->
        <Tree 
            :value="treeData" 
            :expandedKeys="expandedKeys"
            selectionMode="single"
            :selectionKeys="selectionKeys"
            @node-select="onNodeSelect"
            @node-unselect="onNodeUnselect"
            @node-expand="onNodeExpand"
            @node-collapse="onNodeCollapse"
            class="w-full equipment-tree"
        >
            <!-- Template personnalisé pour les nœuds -->
            <template #default="{ node }">
                <div class="flex items-center space-x-2 tree-node-content">
                    <span 
                        :class="{
                            'bg-yellow-200 text-yellow-800 px-1 py-0.5 rounded': node.data.matches_search,
                            'bg-blue-100 text-blue-800 px-1 py-0.5 rounded': !node.data.matches_search && node.data.is_parent
                        }"
                    >
                        {{ node.label }}
                    </span>
                    
                    <!-- Badge pour les parents -->
                    <span v-if="node.data.is_parent && !node.data.matches_search" 
                        class="text-xs px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full">
                        Parent
                    </span>
                    
                    <!-- Actions -->
                    <div class="flex items-center space-x-1 ml-auto">
                        <button 
                            @click.stop="$emit('view', node.key)"
                            class="p-1 text-gray-600 hover:text-gray-800"
                            title="Voir les détails"
                            v-if="$page.props.permissions && ($page.props.permissions['equipment.view'] || hasAnyPermission)"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                        <button 
                            @click.stop="$emit('edit', node.key)"
                            class="p-1 text-blue-600 hover:text-blue-800"
                            title="Modifier"
                            v-if="$page.props.permissions && ($page.props.permissions['equipment.edit'] || hasAnyPermission)"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                        </button>
                        <button 
                            @click.stop="$emit('delete', node.key)"
                            class="p-1 text-red-600 hover:text-red-800"
                            title="Supprimer"
                            v-if="$page.props.permissions && ($page.props.permissions['equipment.delete'] || hasAnyPermission)"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                        </button>
                    </div>
                </div>
            </template>
        </Tree>
    </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

// Accès aux props de la page
const page = usePage();

// Vérifier si l'utilisateur a des permissions
const hasAnyPermission = computed(() => {
    if (!page.props.permissions) return false;
    return Object.values(page.props.permissions).some(permission => permission === true);
});

// Définir les props
const props = defineProps({
    treeData: {
        type: Array,
        required: true
    },
    search: {
        type: String,
        default: ''
    },
    matchingIds: {
        type: Array,
        default: () => []
    },
    parentIds: {
        type: Array,
        default: () => []
    }
});

// Définir les événements émis
const emit = defineEmits(['view', 'edit', 'delete']);

// État local pour les clés développées
const expandedKeys = ref({});
// État local pour la sélection
const selectionKeys = ref({});

// Initialiser les clés développées en fonction des données de l'arbre
onMounted(() => {
    // Parcourir les données de l'arbre et développer les nœuds marqués comme expanded
    if (props.treeData && props.treeData.length > 0) {
        // Développer tous les nœuds au démarrage
        setTimeout(() => {
            expandAll();
        }, 100);
    }
});

// Fonction pour initialiser les clés développées
const initExpandedKeys = (nodes) => {
    if (!nodes || !Array.isArray(nodes)) {
        return;
    }
    
    nodes.forEach(node => {
        if (!node || typeof node !== 'object') {
            return;
        }
        
        if (node.expanded) {
            expandedKeys.value[node.key] = true;
        }
        
        if (node.children && node.children.length > 0) {
            initExpandedKeys(node.children);
        }
    });
};

// Fonction pour développer tous les nœuds
const expandAll = () => {
    const allKeys = {};
    collectAllKeys(props.treeData, allKeys);
    expandedKeys.value = allKeys;
};

// Fonction pour réduire tous les nœuds
const collapseAll = () => {
    expandedKeys.value = {};
};

// Fonction pour collecter toutes les clés
const collectAllKeys = (nodes, keys) => {
    nodes.forEach(node => {
        keys[node.key] = true;
        
        if (node.children && node.children.length > 0) {
            collectAllKeys(node.children, keys);
        }
    });
};

// Gestionnaires d'événements
const onNodeSelect = (node) => {
    // Traitement lors de la sélection d'un nœud
};

const onNodeUnselect = (node) => {
    // Traitement lors de la désélection d'un nœud
};

const onNodeExpand = (node) => {
    // Traitement lors de l'expansion d'un nœud
};

const onNodeCollapse = (node) => {
    // Traitement lors de la réduction d'un nœud
};

// Observer les changements dans les données de l'arbre
watch(() => props.treeData, (newValue) => {
    if (newValue && newValue.length > 0) {
        // Réinitialiser les clés développées
        expandedKeys.value = {};
        // Initialiser les clés développées pour les nœuds marqués comme expanded
        initExpandedKeys(newValue);
        
        // Si une recherche est active, développer tous les nœuds
        if (props.search) {
            setTimeout(() => {
                expandAll();
            }, 100);
        }
    }
}, { deep: true });

// Observer les changements dans la recherche
watch(() => props.search, (newValue) => {
    if (newValue) {
        // Réinitialiser les clés développées pour les nœuds qui correspondent à la recherche
        initExpandedKeys(props.treeData);
    }
});

// Exposer les méthodes pour les utiliser depuis le composant parent
defineExpose({
    expandAll,
    collapseAll
});
</script>

<style scoped>
.prime-tree-container {
    width: 100%;
    padding: 1rem;
    background-color: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}

/* Correction de l'indentation et des chevrons */
:deep(.p-tree) {
    width: 100%;
    border: none;
    padding: 0;
}

:deep(.p-treenode) {
    padding: 0.25rem 0;
}

:deep(.p-treenode-content) {
    padding: 0.5rem;
    border-radius: 0.375rem;
    transition: background-color 0.2s;
    display: flex;
    align-items: center;
}

:deep(.p-tree-toggler) {
    margin-right: 0.5rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

:deep(.p-treenode-children) {
    margin-left: 1.5rem;
    padding-left: 0.5rem;
    border-left: 1px dashed #e5e7eb;
}

/* Styles pour les nœuds correspondant à la recherche */
:deep(.p-treenode.search-match .p-treenode-content) {
    background-color: rgba(250, 204, 21, 0.2);
}

/* Styles pour les nœuds parents */
:deep(.p-treenode.is-parent .p-treenode-content) {
    background-color: rgba(219, 234, 254, 0.3);
}

/* Styles pour les nœuds parents avec des enfants correspondant à la recherche */
:deep(.p-treenode.parent-with-match .p-treenode-content) {
    background-color: rgba(219, 234, 254, 0.5);
    border-left: 2px solid #3b82f6;
}
</style>

<style scoped>
/* Styles personnalisés pour les nœuds de l'arbre */
:deep(.p-highlight) {
    background-color: rgba(250, 204, 21, 0.2) !important;
}

:deep(.p-tree-parent) {
    background-color: rgba(219, 234, 254, 0.3) !important;
}

:deep(.p-tree-parent-with-match) {
    background-color: rgba(219, 234, 254, 0.5) !important;
    border-left: 2px solid #3b82f6;
}

:deep(.p-treenode-content) {
    padding: 0.5rem !important;
    border-radius: 0.375rem !important;
}

:deep(.p-tree) {
    border: none !important;
    padding: 0 !important;
}
</style>
