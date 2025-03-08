<template>
    <div class="tiptap-editor">
        <div v-if="showToolbar" class="tiptap-toolbar">
      <button 
        @click="editor?.chain().focus().toggleBold().run()"
        :class="{ 'is-active': editor?.isActive('bold') }"
        type="button"
        class="toolbar-button"
      >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" class="w-4 h-4">
          <path fill="none" d="M0 0h24v24H0z"/>
          <path d="M8 11h4.5a2.5 2.5 0 1 0 0-5H8v5zm10 4.5a4.5 4.5 0 0 1-4.5 4.5H6V4h6.5a4.5 4.5 0 0 1 3.256 7.606A4.498 4.498 0 0 1 18 15.5zM8 13v5h5.5a2.5 2.5 0 1 0 0-5H8z"/>
        </svg>
      </button>
      <button 
        @click="editor?.chain().focus().toggleItalic().run()"
        :class="{ 'is-active': editor?.isActive('italic') }"
        type="button"
        class="toolbar-button"
      >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" class="w-4 h-4">
          <path fill="none" d="M0 0h24v24H0z"/>
          <path d="M15 20H7v-2h2.927l2.116-12H9V4h8v2h-2.927l-2.116 12H15z"/>
        </svg>
      </button>
      <button 
        @click="editor?.chain().focus().toggleBulletList().run()"
        :class="{ 'is-active': editor?.isActive('bulletList') }"
        type="button"
        class="toolbar-button"
      >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" class="w-4 h-4">
          <path fill="none" d="M0 0h24v24H0z"/>
          <path d="M8 4h13v2H8V4zm-5-.5h3v3H3v-3zm0 7h3v3H3v-3zm0 7h3v3H3v-3zM8 11h13v2H8v-2zm0 7h13v2H8v-2z"/>
        </svg>
      </button>
      <button 
        @click="editor?.chain().focus().toggleOrderedList().run()"
        :class="{ 'is-active': editor?.isActive('orderedList') }"
        type="button"
        class="toolbar-button"
      >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" class="w-4 h-4">
          <path fill="none" d="M0 0h24v24H0z"/>
          <path d="M8 4h13v2H8V4zM5 3v3h1v1H3V6h1V4H3V3h2zM3 14v-2.5h2V11H3v-1h3v2.5H4v.5h2v1H3zm2 5.5H3v-1h2V18H3v-1h3v4H3v-1h2v-.5zM8 11h13v2H8v-2zm0 7h13v2H8v-2z"/>
        </svg>
      </button>
      <button 
        @click="editor?.chain().focus().toggleCodeBlock().run()"
        :class="{ 'is-active': editor?.isActive('codeBlock') }"
        type="button"
        class="toolbar-button"
      >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" class="w-4 h-4">
          <path fill="none" d="M0 0h24v24H0z"/>
          <path d="M16.95 8.464l1.414-1.414 4.95 4.95-4.95 4.95-1.414-1.414L20.485 12 16.95 8.464zm-9.9 0L3.515 12l3.535 3.536-1.414 1.414L.686 12l4.95-4.95L7.05 8.464z"/>
        </svg>
      </button>
    </div>
    <editor-content :editor="editor" />
  </div>
</template>

<script setup>
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import { watch, onBeforeUnmount } from 'vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  showToolbar: {
    type: Boolean,
    default: true
  },
  placeholder: {
    type: String,
    default: 'Écrivez votre commentaire ici...'
  }
})

const emit = defineEmits(['update:modelValue'])

const editor = useEditor({
  content: props.modelValue,
  extensions: [
    StarterKit,
  ],
  editorProps: {
    attributes: {
      class: 'prose prose-sm focus:outline-none min-h-[100px] p-3',
      placeholder: props.placeholder
    }
  },
  onUpdate: ({ editor }) => {
    emit('update:modelValue', editor.getHTML())
  }
})

watch(() => props.modelValue, (newValue) => {
  // Éviter les boucles infinies
  if (editor.value && newValue !== editor.value.getHTML()) {
    editor.value.commands.setContent(newValue, false)
  }
})

onBeforeUnmount(() => {
  if (editor.value) {
    editor.value.destroy()
  }
})
</script>

<style>
.tiptap-editor {
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  overflow: hidden;
}

.tiptap-toolbar {
  display: flex;
  padding: 0.5rem;
  border-bottom: 1px solid #e5e7eb;
  background-color: #f9fafb;
}

.toolbar-button {
  padding: 0.25rem;
  margin-right: 0.25rem;
  border-radius: 0.25rem;
  color: #4b5563;
}

.toolbar-button:hover {
  background-color: #e5e7eb;
}

.toolbar-button.is-active {
  color: #4f46e5;
  background-color: #e0e7ff;
}

.tiptap-editor .ProseMirror {
  outline: none;
}

.tiptap-editor .ProseMirror p.is-editor-empty:first-child::before {
  content: attr(data-placeholder);
  float: left;
  color: #9ca3af;
  pointer-events: none;
  height: 0;
}

/* Styles pour le contenu de l'éditeur */
.tiptap-editor .ProseMirror ul {
  list-style-type: disc;
  padding-left: 1.5em;
}

.tiptap-editor .ProseMirror ol {
  list-style-type: decimal;
  padding-left: 1.5em;
}

.tiptap-editor .ProseMirror pre {
  background-color: #f3f4f6;
  border-radius: 0.25rem;
  padding: 0.5em;
  font-family: monospace;
}

.tiptap-editor .ProseMirror code {
  background-color: #f3f4f6;
  border-radius: 0.25rem;
  padding: 0.125em 0.25em;
  font-family: monospace;
}
</style>