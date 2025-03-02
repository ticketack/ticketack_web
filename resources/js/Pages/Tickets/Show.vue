<template>
    <AuthenticatedLayout :breadcrumbs="[
        { name: 'Tickets', route: 'tickets.index' },
        { name: `#${props.ticket?.id} - ${props.ticket?.title}` }
    ]">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Ticket #{{ props.ticket?.id }} - {{ props.ticket?.title }}
                </h2>
                <div class="flex items-center gap-4">
                    <button @click="downloadPdf" class="text-red-600 hover:text-red-700 flex items-center">
                        <DocumentIcon class="-ml-1 mr-2 h-4 w-4" />
                        PDF
                    </button>
                </div>
            </div>
        </template>

        <div class="py-2">
            
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex gap-6">
                    <!-- Colonne principale (70%) -->
                    <div class="w-[70%] space-y-6">
                        <!-- Informations du ticket -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <!-- En-tête du ticket -->
                                <div class="flex flex-col md:flex-row justify-between gap-4 mb-6">
                                    <div class="space-y-4">
                                        <h3 class="text-lg font-medium max-w-[830px]">{{ props.ticket?.title }}</h3>
                                        <div class="flex flex-wrap gap-2">
                                            <TicketStatus :status="props.ticket?.status" />
                                            <TicketPriority :priority="props.ticket?.priority" />
                                            <span v-if="props.ticket?.category" class="px-2 py-1 text-xs font-medium rounded-full"
                                                :style="{ backgroundColor: props.ticket.category.color + '20', color: props.ticket.category.color }">
                                                {{ props.ticket.category.name }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="space-y-2 text-sm text-gray-500">
                                        <p>Créé par : {{ ticket?.creator?.name }}</p>
                                        <p>Assignés : 
                                            <span v-if="ticket?.assignees?.length">
                                                {{ ticket.assignees.map(a => a.name).join(', ') }}
                                            </span>
                                            <span v-else>Non assigné</span>
                                        </p>
                                        <p>Créé le : {{ formatDate(ticket?.created_at) }}</p>
                                        <p v-if="ticket?.due_date">Échéance : {{ formatDate(ticket.due_date) }}</p>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="mt-6">
                                    <h4 class="text-sm font-medium text-gray-900">Description</h4>
                                    <div class="mt-2 text-sm text-gray-700 whitespace-pre-wrap">
                                        {{ ticket?.description }}
                                    </div>
                                </div>



                                <!-- Documents -->
                                <div class="mt-8">
                                    <h4 class="text-sm font-medium text-gray-900 mb-4">{{ $page.props.translations.tickets.documents.title }}</h4>
                                    <div v-if="ticket?.documents" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                                        <DocumentPreview
                                            v-for="document in ticket?.documents"
                                            :key="document?.id"
                                            :document="document"
                                            :ticket-id="ticket?.id"
                                            :can-delete="$page?.props?.permissions?.['tickets.edit']"
                                            @delete="deleteDocument"
                                        />
                                    </div>
                                    <div v-else class="text-sm text-gray-500">
                                        Aucun document attaché à ce ticket
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section des commentaires -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h4 class="text-sm font-medium text-gray-900 mb-4">{{ $page.props.translations.tickets.comments.title }}</h4>
                            
                            <!-- Liste des commentaires -->
                            <div v-if="props.ticket?.comments" class="space-y-4 mb-6 divide-y divide-gray-100">
                                <div v-for="comment in props.ticket?.comments" :key="comment?.id" 
                                     class="border border-gray-200 p-4 rounded-lg hover:border-gray-300 transition-colors first:mt-0 mt-4">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-start space-x-3">
                                            <!-- Avatar -->
                                            <div class="flex-shrink-0">
                                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                    <span class="text-gray-600 font-medium text-sm">
                                                        {{ comment?.user?.name?.charAt(0)?.toUpperCase() }}
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <!-- Contenu -->
                                            <div>
                                                <div class="flex items-center space-x-2">
                                                    <span class="font-medium">{{ comment?.user?.name }}</span>
                                                    <span class="text-gray-500 text-sm">
                                                        {{ formatDate(comment?.created_at) }}
                                                    </span>
                                                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded">
                                                        CM-{{ comment?.id }}
                                                    </span>
                                                </div>
                                                <p class="mt-1 text-gray-700">{{ comment?.content }}</p>
                                                
                                                <!-- Pièces jointes -->
                                                <div v-if="comment?.attachments" 
                                                     class="mt-2 space-y-1">
                                                    <div v-for="(attachment, index) in comment?.attachments" 
                                                         :key="index"
                                                         class="flex items-center space-x-2">
                                                        <DocumentIcon class="h-4 w-4 text-gray-500" />
                                                        <a :href="attachment?.url" 
                                                           target="_blank"
                                                           class="text-sm text-blue-600 hover:text-blue-800">
                                                            {{ attachment?.name }}
                                                        </a>
                                                        <span class="text-xs text-gray-500">({{ formatFileSize(attachment?.size) }})</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Actions -->
                                        <div v-if="$page.props.auth.user.id === comment.user_id || $page.props.auth.user.roles?.includes('admin')" 
                                             class="flex space-x-2">
                                            <button @click="deleteComment(comment.id)" 
                                                    class="text-red-600 hover:text-red-800">
                                                <TrashIcon class="h-5 w-5" />
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p v-else class="text-gray-500 italic">{{ $page.props.translations.tickets.comments.no_comments }}</p>

                            <!-- Formulaire d'ajout de commentaire -->
                            <form @submit.prevent="submitComment" class="mt-6 space-y-4">
                                <div>
                                    <label for="comment" class="sr-only">{{ $page.props.translations.tickets.comments.add }}</label>
                                    <textarea id="comment"
                                              v-model="commentForm.content"
                                              :placeholder="$page.props.translations.tickets.comments.placeholder"
                                              rows="3"
                                              class="shadow-sm block w-full focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300 rounded-md"
                                              required></textarea>

                                    <!-- Liste des fichiers sélectionnés -->
                                    <div v-if="commentForm?.attachments" class="mt-2 space-y-2">
                                        <div v-for="(file, index) in commentForm.attachments" :key="index" 
                                             class="flex items-center justify-between p-2 bg-gray-50 rounded-md">
                                            <div class="flex items-center space-x-2">
                                                <DocumentIcon class="h-4 w-4 text-gray-500" />
                                                <span class="text-sm text-gray-700">{{ file.name }}</span>
                                                <span class="text-xs text-gray-500">({{ formatFileSize(file.size) }})</span>
                                            </div>
                                            <button type="button" @click="removeCommentFile(index)"
                                                    class="text-red-600 hover:text-red-800">
                                                <TrashIcon class="h-4 w-4" />
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Upload de fichiers -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">{{ $page.props.translations.tickets.comments.attachments }}</label>
                                    <DropZone
                                        ref="dropZone"
                                        class="mt-1"
                                        @files-selected="handleCommentFileUpload"
                                    />
                                    <div class="mt-1 space-y-1">
                                        <p class="text-sm text-gray-500">{{ $page.props.translations.tickets.comments.max_size }} : 10 MB</p>
                                        <p class="text-sm text-gray-500">{{ $page.props.translations.tickets.comments.accepted_formats }} : JPG, JPEG, PNG, GIF, PDF</p>
                                    </div>
                                </div>
                                    


                                <div class="flex justify-end">
                                    <button type="submit"
                                            :disabled="commentForm.processing"
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                                        <svg v-if="commentForm.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        {{ commentForm.processing ? 'Envoi en cours...' : 'Envoyer' }}
                                    </button>
                                </div>
                            </form>
                            </div>
                        </div>
                    </div>

                    <!-- Colonne latérale (30%) -->
                    <div class="w-[30%]">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg sticky top-6">
                            <div class="p-6 space-y-4">
                                <div v-if="$page.props.permissions['update_ticket_status']" class="mb-4">
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                                    <select id="status"
                                            v-model="form.status_id"
                                            @change="updateStatus"
                                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                        <option v-for="status in statuses"
                                                :key="status.id"
                                                :value="status.id">
                                            {{ status.name }}
                                        </option>
                                    </select>
                                </div>

                                <div v-if="$page.props.permissions['tickets.assign']">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Assignés</label>
                                    <div class="space-y-4">
                                        <MultipleAutocomplete
                                            v-model="selectedAssigneeIds"
                                            :search-url="route('users.search')"
                                            placeholder="Rechercher des utilisateurs..."
                                            @update:modelValue="updateAssignees"
                                        />
                                        <div v-for="assignee in props.ticket?.assignees" :key="assignee.id" class="flex items-center justify-between bg-gray-50 p-2 rounded">
                                            <span>{{ assignee.name }}</span>
                                            <button @click="removeAssignee(assignee.id)" class="text-red-600 hover:text-red-800">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>

                                    </div>
                                </div>
                                <div v-else>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Assignés</label>
                                    <div class="text-sm text-gray-500">
                                        <div v-if="ticket?.assignees?.length">
                                            {{ ticket.assignees.map(a => a.name).join(', ') }}
                                        </div>
                                        <div v-else>Non assigné</div>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 mb-4">Historique</h4>
                                    <Timeline :logs="ticket?.logs" />
                                </div>
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
import MultipleAutocomplete from '@/Components/MultipleAutocomplete.vue';
import { Link, useForm, router, usePage } from '@inertiajs/vue3';
import Autocomplete from '@/Components/Autocomplete.vue';
import { reactive, defineProps, ref } from 'vue';
import DropZone from '@/Components/Documents/DropZone.vue';
import { DocumentIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import { toast } from '@/utils';
import TicketStatus from '@/Components/Tickets/TicketStatus.vue';
import TicketPriority from '@/Components/Tickets/TicketPriority.vue';
import Timeline from '@/Components/Tickets/Timeline.vue';
import InputLabel from '@/Components/InputLabel.vue';
import DocumentPreview from '@/Components/Documents/DocumentPreview.vue';
import { format } from 'date-fns';
import { fr } from 'date-fns/locale';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const page = usePage();

const props = defineProps({
    ticket: {
        type: Object,
        required: true
    },
    statuses: {
        type: Array,
        required: true
    }
});

const form = useForm({
    status_id: props.ticket?.status?.id
});

const deleteDocument = (document) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce document ?')) {
        form.delete(route('tickets.documents.destroy', [props.ticket.id, document.id]), {
            preserveScroll: true,
            preserveState: true
        });
    }
};

const formatDate = (date) => {
    return format(new Date(date), "d MMMM yyyy 'à' HH:mm", { locale: fr });
};

const downloadPdf = async () => {
    try {
        const response = await fetch(route('tickets.pdf', props.ticket.id));
        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `ticket-${props.ticket.id}.pdf`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
    } catch (error) {
        console.error('Erreur lors du téléchargement du PDF:', error);
        toast.error('Erreur lors du téléchargement du PDF');
    }
};

const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const updateStatus = () => {
    router.put(route('tickets.update', props.ticket.id), {
        status_id: form.status_id
    });
};

const selectedAssigneeIds = ref(props.ticket?.assignees?.map(a => a.id) || []);

const updateAssignees = (newValues) => {
    const currentAssigneeIds = props.ticket?.assignees?.map(a => a.id) || [];
    
    // Trouver les IDs à ajouter (présents dans newValues mais pas dans currentAssigneeIds)
    const idsToAdd = newValues.filter(id => !currentAssigneeIds.includes(id));
    
    // Trouver les IDs à supprimer (présents dans currentAssigneeIds mais pas dans newValues)
    const idsToRemove = currentAssigneeIds.filter(id => !newValues.includes(id));
    
    // Ajouter les nouvelles assignations
    idsToAdd.forEach(userId => {
        router.post(route('tickets.assign', props.ticket.id), {
            user_id: userId
        }, {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Assignation ajoutée avec succès');
            },
            onError: () => {
                toast.error('Erreur lors de l\'ajout de l\'assignation');
            }
        });
    });
    
    // Supprimer les assignations retirées
    idsToRemove.forEach(userId => {
        router.delete(route('tickets.unassign', [props.ticket.id, userId]), {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Assignation retirée avec succès');
            },
            onError: () => {
                toast.error('Erreur lors du retrait de l\'assignation');
            }
        });
    });
};

const removeAssignee = (userId) => {
    router.delete(route('tickets.unassign', [props.ticket.id, userId]), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Assignation supprimée avec succès');
        },
        onError: () => {
            toast.error('Erreur lors de la suppression de l\'assignation');
        }
    });
};

// Gestion des commentaires
const commentForm = reactive({
    content: '',
    attachments: [],
    processing: false
});

const dropZone = ref(null);

const handleCommentFileUpload = (files) => {
    if (files) {
        commentForm.attachments = [...commentForm.attachments, ...files];
    }
};

const removeCommentFile = (index) => {
    commentForm.attachments.splice(index, 1);
};

const submitComment = () => {
    if (commentForm.processing) return;

    commentForm.processing = true;
    
    // Créer un FormData pour envoyer les fichiers
    const formData = new FormData();
    formData.append('content', commentForm.content);
    
    // Ajouter les fichiers
    if (commentForm.attachments) {
        commentForm.attachments.forEach((file, index) => {
            formData.append(`attachments[${index}]`, file);
        });
    }

    // Envoyer le commentaire
    router.post(route('tickets.comments.store', props.ticket.id), formData, {
        forceFormData: true,
        onSuccess: () => {
            // Réinitialiser le formulaire
            commentForm.content = '';
            commentForm.attachments = [];
            commentForm.processing = false;
        },
        onError: (errors) => {
            commentForm.processing = false;
            if (errors.attachments) {
                toast.error(page.props.translations.pages.comments.error.file_size);
            } else {
                toast.error(page.props.translations.pages.comments.error.add);
            }
        }
    });
};

const deleteComment = (commentId) => {
    if (confirm(t('pages.comments.confirm_delete'))) {
        router.delete(route('tickets.comments.destroy', [props.ticket.id, commentId]), {
            onSuccess: () => {
                toast.success(t('pages.comments.success.deleted'));
            },
            onError: () => {
                toast.error(t('pages.comments.error.delete'));
            }
        });
    }
};
</script>
