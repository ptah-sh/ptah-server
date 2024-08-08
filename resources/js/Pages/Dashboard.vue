<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import ValueCard from "@/Components/ValueCard.vue";

const props = defineProps(["nodesCount", "servicesCount"]);
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-6 gap-4">
                    <ValueCard
                        class="bg-white py-2 px-4 rounded shadow text-right"
                        label="Nodes"
                        :value="nodesCount"
                    />
                    <ValueCard
                        class="bg-white py-2 px-4 rounded shadow text-right"
                        label="Services"
                        :value="servicesCount"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
