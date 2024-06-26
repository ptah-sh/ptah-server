<script setup>
import ShowLayout from "@/Pages/Services/ShowLayout.vue";
import TaskGroup from "@/Components/NodeTasks/TaskGroup.vue";

const props = defineProps({
  service: Object,
})
</script>

<template>
  <ShowLayout :service="$props.service">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div v-for="(deployment, index) in service.deployments" class="my-8 first-of-type:mt-0">
        <div class="ps-4 font-extrabold">Deployment #{{deployment.id}}
          <span v-if="index === 0">(latest)</span>
        </div>

        <TaskGroup :task-group="deployment.latest_task_group" class="mt-4" />
      </div>
    </div>
  </ShowLayout>
</template>
