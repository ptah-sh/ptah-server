<script setup>
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    title: String,
    link: Object,
});
</script>

<template>
    <div
        class="mb-4 p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700"
    >
        <p class="font-bold">{{ title }}</p>
        <p>
            <slot />
        </p>
        <Link
            v-if="link"
            :href="link.href"
            class="text-blue-600 hover:text-blue-800 underline"
        >
            {{ link.text }}
        </Link>
    </div>
</template>
