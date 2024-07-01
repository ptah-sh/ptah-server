<script setup>
import ActionSection from "@/Components/ActionSection.vue";
import TaskGroup from "@/Components/NodeTasks/TaskGroup.vue";

defineProps({
  'taskGroup': Object
})
</script>

<template>
  <ActionSection>
    <template #title>
      Agent Status
    </template>

    <template #description>
      Agent on your server is currently being upgraded.
    </template>

    <template #content>
      <div class="col-span-full">
        <TaskGroup :task-group="taskGroup" />
      </div>
    </template>
  </ActionSection>
</template>