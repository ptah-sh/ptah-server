<script setup>
import {defineProps} from 'vue';

defineProps({
  'value': String,
  'label': String
})
</script>

<template>
  <div class="min-w-40 inline-block">
    <div class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $props.value }}</div>
  <div class="text-xs text-gray-500">{{ $props.label }}</div>

  </div>
</template>