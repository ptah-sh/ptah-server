<script setup>
import {reactive} from "vue";
import TaskResult from "@/Components/NodeTasks/TaskResult.vue";

defineProps({
  'taskGroup': Object,
});

</script>

<template>
  <ul class="col-span-6 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
    <TaskResult v-for="task in taskGroup.tasks" :key="task.id" :task="task" />
  </ul>
</template>