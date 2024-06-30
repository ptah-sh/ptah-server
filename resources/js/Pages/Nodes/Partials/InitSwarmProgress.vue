<script setup>

import ActionSection from "@/Components/ActionSection.vue";
import TaskGroup from "@/Components/NodeTasks/TaskGroup.vue";

defineProps({
  'taskGroup': Object
});
</script>
<template>
  <ActionSection>

    <template #title>
      Swarm Cluster
    </template>

    <template #description>
      Node is currently initializing the Swarm Cluster.
    </template>

    <template #content>
      <div class="col-span-full">
        <TaskGroup :taskGroup="taskGroup"/>
      </div>
    </template>
  </ActionSection>
</template>