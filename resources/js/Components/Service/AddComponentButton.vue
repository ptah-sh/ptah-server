<script setup>
import SecondaryButton from "@/Components/SecondaryButton.vue";
</script>

<template>
  <SecondaryButton>
    <svg class="w-4 h-4 me-2 -ms-1 text-gray-800 dark:text-white" aria-hidden="true"
         xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M5 12h14m-7 7V5"/>
    </svg>

    <slot />
  </SecondaryButton>
</template>