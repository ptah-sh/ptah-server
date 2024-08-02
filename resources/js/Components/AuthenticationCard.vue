<script setup>
import LegalNotice from "@/Components/LegalNotice.vue";
</script>

<template>
    <div
        class="min-h-screen flex flex-col items-center justify-around pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900"
    >
        <div>
            <div>
                <slot name="logo" />
            </div>

            <div
                class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg"
            >
                <slot />
            </div>
        </div>

        <LegalNotice />
    </div>
</template>
