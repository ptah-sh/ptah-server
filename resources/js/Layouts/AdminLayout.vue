<script setup>
import { ref } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import ApplicationMark from "@/Components/ApplicationMark.vue";
import Banner from "@/Components/Banner.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import dayjs from "dayjs";

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);
const showingSidebar = ref(true);

const switchToTeam = (team) => {
    router.put(
        route("current-team.update"),
        {
            team_id: team.id,
        },
        {
            preserveState: false,
        },
    );
};

const logout = () => {
    router.post(route("logout"));
};

const toggleSidebar = () => {
    showingSidebar.value = !showingSidebar.value;
};
</script>

<template>
    <div>
        <Head :title="title" />

        <Banner />

        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            <!-- Top Navigation -->
            <nav
                class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700"
            >
                <!-- ... (keep the existing top navigation code) ... -->
            </nav>

            <div class="flex">
                <!-- Sidebar -->
                <aside
                    :class="{
                        'w-64': showingSidebar,
                        'w-20': !showingSidebar,
                    }"
                    class="bg-white dark:bg-gray-800 h-screen transition-all duration-300 ease-in-out shadow-lg"
                >
                    <div class="p-4 flex justify-between items-center">
                        <ApplicationMark class="block h-9 w-auto" />
                        <button
                            @click="toggleSidebar"
                            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                            </svg>
                        </button>
                    </div>
                    <nav class="mt-5 px-2 space-y-1 flex flex-col">
                        <!-- <NavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                            class="flex items-center px-2 py-2 text-sm font-medium rounded-md"
                            :class="[route().current('dashboard') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white']"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-3 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span v-if="showingSidebar">Dashboard</span>
                        </NavLink>
                        <NavLink
                            :href="route('admin.teams.index')"
                            :active="route().current('admin.teams.index')"
                            class="flex items-center px-2 py-2 text-sm font-medium rounded-md"
                            :class="[route().current('admin.teams.index') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white']"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-3 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span v-if="showingSidebar">Users</span>
                        </NavLink> -->
                        <NavLink
                            :href="route('admin.teams.index')"
                            :active="route().current('admin.teams.index')"
                            class="flex items-center px-2 py-2 text-sm font-medium rounded-md"
                            :class="[
                                route().current('admin.teams.index')
                                    ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white'
                                    : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white',
                            ]"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="mr-3 flex-shrink-0 h-6 w-6"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                                />
                            </svg>
                            <span v-if="showingSidebar">Teams</span>
                        </NavLink>
                        <!-- <NavLink
                            :href="route('nodes.index')"
                            :active="route().current('nodes.index')"
                            class="flex items-center px-2 py-2 text-sm font-medium rounded-md"
                            :class="[route().current('nodes.index') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white']"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-3 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <span v-if="showingSidebar">Nodes</span>
                        </NavLink> -->
                    </nav>
                </aside>

                <!-- Main Content -->
                <div class="flex-1 overflow-x-hidden overflow-y-auto">
                    <!-- Page Heading -->
                    <header class="bg-white dark:bg-gray-800 shadow">
                        <div
                            class="max-w-full mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center"
                        >
                            <div class="flex items-center">
                                <h1
                                    class="text-3xl font-semibold text-gray-900 dark:text-white mr-4"
                                >
                                    {{ title || "Ptah.sh" }}
                                </h1>
                            </div>
                            <div v-if="$slots.actions" class="flex gap-4">
                                <slot name="actions" />
                            </div>
                        </div>

                        <div
                            v-if="$slots.tabs"
                            class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 border-t border-gray-200 dark:border-gray-700"
                        >
                            <ul
                                class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500 dark:text-gray-400"
                            >
                                <slot name="tabs" />
                            </ul>
                        </div>
                    </header>

                    <!-- Page Content -->
                    <main class="p-4">
                        <slot />
                    </main>
                </div>
            </div>
        </div>
    </div>
</template>
