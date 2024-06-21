<script setup>
defineProps({
  'node': Object,
});
</script>

<template>
  <div class="mx-2">
  <span v-if="$props.node.online"
        class="bg-green-300 text-green-950 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Online</span>
  <span v-else
        class="bg-red-300 text-red-950 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Offline</span>
  </div>
</template>