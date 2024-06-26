<script setup>
import {reactive} from "vue";
import TaskResult from "@/Components/NodeTasks/TaskResult.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
  'taskGroup': Object,
});

const retry = () => {
  router.post(route('node-task-groups.retry', {taskGroup: props.taskGroup.id, node_id: props.taskGroup.node_id}));
}

</script>

<template>
  <div>
    <ul class="col-span-6 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
      <TaskResult v-for="task in taskGroup.tasks" :key="task.id" :task="task" />
    </ul>
    <div class="flex">
      <div class="col-span-6 ms-4 mt-1 text-xs text-gray-900 dark:text-white grow">#{{ taskGroup.id }} Invoked by {{ taskGroup.invoker.name }} on {{ taskGroup.created_at }}</div>
      <SecondaryButton v-if="taskGroup.status === 'failed' || taskGroup.status === 'canceled'"
                       @click="retry"
              class="col-span-6 ms-4 mt-2 text-xs text-gray-900 dark:text-white">
        Retry Failed Tasks
      </SecondaryButton>
    </div>
  </div>
</template>