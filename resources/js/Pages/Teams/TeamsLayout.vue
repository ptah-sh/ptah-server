<script setup lang="ts">
import AppLayout from "@/Layouts/AppLayout.vue";
import LayoutTab from "@/Components/LayoutTab.vue";

const props = defineProps({
    team: Object,
});
</script>

<template>
    <AppLayout :title="props.team.name">
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                {{ props.team.name }}
            </h2>
        </template>

        <template #tabs>
            <LayoutTab :href="route('teams.show', { id: props.team.id })">
                <template #icon>
                    <svg
                        class="w-6 h-6"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke="currentColor"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 12a1 1 0 0 0-1 1v4a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-4a1 1 0 0 0-1-1M5 12h14M5 12a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1m-2 3h.01M14 15h.01M17 9h.01M14 9h.01"
                        />
                    </svg>
                </template>

                <template #title> Overview </template>
            </LayoutTab>

            <LayoutTab
                :href="route('teams.billing.show', { team: props.team.id })"
            >
                <template #icon>
                    <svg
                        class="w-6 h-6"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke="currentColor"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                </template>

                <template #title> Billing </template>
            </LayoutTab>

            <LayoutTab
                :href="route('teams.billing.quotas', { team: props.team.id })"
            >
                <template #icon>
                    <svg
                        class="w-6 h-6"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke="currentColor"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                        />
                    </svg>
                </template>

                <template #title> Quotas </template>
            </LayoutTab>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <slot />
            </div>
        </div>
    </AppLayout>
</template>
