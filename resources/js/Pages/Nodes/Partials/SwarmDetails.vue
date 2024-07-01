
<script setup>
import ActionSection from "@/Components/ActionSection.vue";
</script>

<template>
  <ActionSection>
    <template #title>
      Swarm Cluster
    </template>

    <template #description>
      Swarm Cluster has been initialized and is in a healthy state.
    </template>

    <template #content>
        <div class="text-sm text-gray-600 dark:text-gray-400 col-span-full">
          The Swarm Cluster has been initialized and is in a healthy state (not guaranteed, however: no health checks yet).
      </div>
    </template>
  </ActionSection>
</template>