<script setup>
const props = defineProps({
  team: Object,
});
</script>

<template>
  <div class="col-span-6">
    <div class="flex items-center mt-2">
      <img class="object-cover w-12 h-12 rounded-full"
           :src="'https://ui-avatars.com/api/?name=' + encodeURIComponent($props.team.name) + '&color=7F9CF5&background=EBF4FF'"
           :alt="$props.team.name">

      <div class="ms-4 leading-tight">
        <div class="text-gray-900 dark:text-white">
          {{ $props.team.name }}</div>
        <span class="text-sm text-gray-400">Owning Team</span>
      </div>
    </div>
  </div>
</template>