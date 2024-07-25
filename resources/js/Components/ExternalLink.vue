<script setup>
import ExternalResourceIcon from "@/Icons/ExternalResourceIcon.vue";

defineProps({
  href: String,
})
</script>

<template>
  <a :href="href" target="_blank" class="text-sm text-blue-600 hover:text-blue-900 hover:underline flex gap-2"><slot /> <ExternalResourceIcon /></a>
</template>
