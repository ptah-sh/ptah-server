<script setup>
import { router } from '@inertiajs/vue3'

import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from "@/Components/PrimaryButton.vue";
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Nodes
            </h2>
        </template>

        <template #actions>
            <PrimaryButton type="button" @click="router.get(route('nodes.create'))">Create</PrimaryButton>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    list nodes here
                </div>
            </div>
        </div>
    </AppLayout>
</template>
