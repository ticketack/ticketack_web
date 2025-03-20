<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import NotificationBell from '@/Components/NotificationBell.vue';
import { Link } from '@inertiajs/vue3';



const showingNavigationDropdown = ref(false);
const sidebarCollapsed = ref(false);

const toggleSidebar = () => {
    sidebarCollapsed.value = !sidebarCollapsed.value;
};

// Obtenir le logo depuis les props
const page = usePage();
const logo = computed(() => page.props.app.logo);

const props = defineProps({
    breadcrumbs: {
        type: Array,
        default: () => []
    }
});


</script>

<template>
    <div class="min-h-screen bg-gray-100 flex">
        <!-- Sidebar -->
        <div :class="{'w-64': !sidebarCollapsed, 'w-16': sidebarCollapsed}" 
             class="bg-white text-black transition-all duration-300 flex flex-col fixed h-full border-r border-gray-200">
            <div class="p-4 flex items-center justify-between">
                <ApplicationLogo class="block h-14 w-auto fill-current text-white" v-if="!sidebarCollapsed" :logo-url="logo" />
                <button @click="toggleSidebar" class="text-white hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="grey" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                    </svg>
                </button>
            </div>
            
            <!-- Navigation Links -->
            <div class="flex-1 px-2 py-4 space-y-2">
                <Link :href="route('dashboard')" :class="{'justify-center': sidebarCollapsed}" class="flex items-center px-4 py-2 text-black hover:bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span v-if="!sidebarCollapsed" class="ml-3">{{ $page.props.translations.menu.dashboard }}</span>
                </Link>
                
                <Link v-if="$page.props.permissions && $page.props.permissions['planning.view']" :href="route('planning.index')" :class="{'justify-center': sidebarCollapsed}" class="flex items-center px-4 py-2 text-black hover:bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span v-if="!sidebarCollapsed" class="ml-3">{{ $page.props.translations.menu.planning }}</span>
                </Link>

                <Link :href="route('equipment.index')" :class="{'justify-center': sidebarCollapsed}" class="flex items-center px-4 py-2 text-black hover:bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span v-if="!sidebarCollapsed" class="ml-3">{{ $page.props.translations.menu.equipment }}</span>
                </Link>

                <Link :href="route('tickets.index')" :class="{'justify-center': sidebarCollapsed}" class="flex items-center px-4 py-2 text-black hover:bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                    </svg>
                    <span v-if="!sidebarCollapsed" class="ml-3">{{ $page.props.translations.menu.tickets }}</span>
                </Link>

                <Link :href="route('tickets.create')" :class="{'justify-center': sidebarCollapsed}" class="flex items-center px-4 py-2 text-black hover:bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span v-if="!sidebarCollapsed" class="ml-3">{{ $page.props.translations.menu.create_ticket }}</span>
                </Link>

                <!-- Lien vers le panneau solver (visible uniquement pour les solvers et admins) -->
                <Link 
                    v-if="$page.props.auth.user.roles.some(role => ['solver', 'admin'].includes(role))"
                    :href="route('solver.dashboard')"
                    :class="{'justify-center': sidebarCollapsed}"
                    class="flex items-center px-4 py-2 text-black hover:bg-gray-100 rounded-lg"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span v-if="!sidebarCollapsed" class="ml-3">{{ $page.props.translations.menu.solver }}</span>
                </Link>

                <!-- Lien vers le pointage des temps -->
                <Link 
                    v-if="$page.props.permissions && $page.props.permissions['time_entries.view']"
                    :href="route('time-tracking.index')"
                    :class="{'justify-center': sidebarCollapsed}"
                    class="flex items-center px-4 py-2 text-black hover:bg-gray-100 rounded-lg"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span v-if="!sidebarCollapsed" class="ml-3">{{ $page.props.translations.menu.time_tracking }}</span>
                </Link>

            </div>
            
            <!-- API Documentation Link -->
            <div class="mt-auto mb-2 px-4">
                <Link :href="route('api.documentation')" :class="{'justify-center': sidebarCollapsed}" class="flex items-center w-full px-4 py-2 text-[#AAAAAA] hover:bg-gray-700 rounded-lg" style="font-size: 0.8em;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span v-if="!sidebarCollapsed" class="ml-3">{{ $page.props.translations.menu.api_doc }}</span>
                </Link>
            </div>
        </div>

        <!-- Main Content -->
        <div :class="{'ml-64': !sidebarCollapsed, 'ml-16': sidebarCollapsed}" class="flex-1 transition-all duration-300">
            <nav
                class="border-b border-gray-100 bg-white"
            >
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">


                            <!-- Navigation Links -->
                            <div
                                class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                            >
                                <NavLink
                                    v-if="$page.props.permissions && $page.props.permissions['roles.view']"
                                    :href="route('roles.index')"
                                    :active="route().current('roles.*')"
                                >
                                    {{ $page.props.translations.menu.roles }}
                                </NavLink>

                                <NavLink
                                    v-if="$page.props.permissions && $page.props.permissions['users.view']"
                                    :href="route('users.index')"
                                    :active="route().current('users.*')"
                                    class="ml-4"
                                >
                                    {{ $page.props.translations.menu.users }}
                                </NavLink>

                                <NavLink
                                    v-if="$page.props.permissions && $page.props.permissions['settings.view']"
                                    :href="route('settings.index')"
                                    :active="route().current('settings.*')"
                                    class="ml-4"
                                >
                                    {{ $page.props.translations.menu.settings }}
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <!-- Notification Bell -->
                            <div class="relative ms-3 mr-3">
                                <NotificationBell :initial-unread-count="$page.props.unreadNotificationsCount || 0" />
                            </div>
                            
                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="-me-0.5 ms-2 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            :href="route('profile.edit')"
                                        >
                                            {{ $page.props.translations.menu.profile }}
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            {{ $page.props.translations.menu.logout }}
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink
                            v-if="$page.props.permissions && $page.props.permissions['roles.view']"
                            :href="route('roles.index')"
                            :active="route().current('roles.*')"
                        >
                            {{ $page.props.translations.menu.roles }}
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            v-if="$page.props.permissions && $page.props.permissions['users.view']"
                            :href="route('users.index')"
                            :active="route().current('users.*')"
                        >
                            {{ $page.props.translations.menu.users }}
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            v-if="$page.props.permissions && $page.props.permissions['settings.view']"
                            :href="route('settings.index')"
                            :active="route().current('settings.*')"
                        >
                            {{ $page.props.translations.menu.settings }}
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div
                        class="border-t border-gray-200 pb-1 pt-4"
                    >
                        <div class="px-4">
                            <div
                                class="text-base font-medium text-gray-800"
                            >
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-gray-500">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                {{ $page.props.translations.menu.profile }}
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header
                class="bg-white shadow"
                v-if="$slots.header"
            >
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <!-- Breadcrumbs -->
                <div v-if="$props.breadcrumbs && $props.breadcrumbs.length > 0" class="py-2">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-4">
                        <Breadcrumbs :items="$props.breadcrumbs" />
                    </div>
                </div>
                <slot />
            </main>
        </div>
    </div>
</template>
