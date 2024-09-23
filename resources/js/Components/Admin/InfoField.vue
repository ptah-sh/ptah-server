<script setup>
import { defineProps } from "vue";
import InputLabel from "@/Components/InputLabel.vue";

const props = defineProps({
    label: String,
    value: String,
});
</script>

<template>
    <div class="bg-gray-50 rounded-lg p-4">
        <InputLabel :value="label" class="text-sm font-medium text-gray-500" />
        <div class="mt-1 text-lg font-semibold text-gray-900">{{ value }}</div>
    </div>
</template>
