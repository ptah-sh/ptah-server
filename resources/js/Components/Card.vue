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
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
        <h2 class="text-lg font-bold mb-4">{{ title }}</h2>

        <slot></slot>
    </div>
</template>
