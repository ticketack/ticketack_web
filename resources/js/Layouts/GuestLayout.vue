<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

// Obtenir le logo depuis les props
const page = usePage();
const logo = computed(() => page.props.app.logo);
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center pt-6 sm:justify-center sm:pt-0 relative"
        style="background-color: #131a24;"
    >
        <!-- Image d'arrière-plan -->
        <div 
            class="absolute inset-0 w-full h-full bg-cover bg-center bg-no-repeat z-0"
            style="background-image: url('/images/background.webp'); filter: brightness(0.5);"
        ></div>
        <div class="mb-8 relative z-10">
            <Link href="/">
                <ApplicationLogo class="h-24 w-auto fill-current text-white" :logo-url="logo" />
            </Link>
        </div>

        <div
            class="w-full overflow-hidden bg-white bg-opacity-95 backdrop-blur-sm px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg relative z-10"
        >
            <slot />
        </div>
    </div>
</template>
