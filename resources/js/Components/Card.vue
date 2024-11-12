<script setup>
import { defineProps } from "vue";

defineProps({
    title: {
        type: String,
        default: "",
    },
});
</script>

<template>
    <div
        class="bg-white overflow-hidden shadow sm:rounded-lg p-6 hover:shadow-lg transition-shadow duration-200"
    >
        <h2 v-if="title" class="text-lg font-bold mb-4">{{ title }}</h2>

        <slot></slot>
    </div>
</template>
