<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import LayoutTab from "@/Components/LayoutTab.vue";
import NodeStatus from "@/Components/NodeStatus.vue";

const props = defineProps({
    node: Object
})
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex" >
                {{ $props.node.name }}


              <NodeStatus :node="$props.node" />

            </h2>
        </template>

        <template #actions>
            <!--            <PrimaryButton type="button" @click="router.get(route('nodes.create'))">Create</PrimaryButton>-->
        </template>

        <template #tabs>
            <LayoutTab :href="route('nodes.show', {'id': $props.node.id})">
                <template #icon>
                    <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12a1 1 0 0 0-1 1v4a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-4a1 1 0 0 0-1-1M5 12h14M5 12a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1m-2 3h.01M14 15h.01M17 9h.01M14 9h.01"/>
                    </svg>
                </template>

                <template #title>
                    Overview
                </template>
            </LayoutTab>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <slot />

            </div>
        </div>
    </AppLayout>
</template>
