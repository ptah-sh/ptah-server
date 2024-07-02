<script setup>
import { computed, useSlots } from 'vue';
import SectionTitle from './SectionTitle.vue';
import TaskGroup from "@/Components/NodeTasks/TaskGroup.vue";

defineEmits(['submitted']);

defineProps({
  'taskGroup': Object
})

const hasActions = computed(() => !! useSlots().actions || !! useSlots().submit);
</script>

<template>
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <SectionTitle>
            <template #title>
                <slot name="title" />
            </template>
            <template #description>
                <slot name="description" />
            </template>
        </SectionTitle>

        <div class="mt-5 md:mt-0 md:col-span-2">
            <form @submit.prevent="$emit('submitted')">
                <div
                    class="px-4 py-5 bg-white dark:bg-gray-800 sm:p-6 shadow"
                    :class="hasActions ? 'sm:rounded-tl-md sm:rounded-tr-md' : 'sm:rounded-md'"
                >
                    <div class="grid grid-cols-6 gap-6" v-auto-animate>
                        <slot name="form" />
                    </div>
                </div>

                <div v-if="hasActions" class="flex items-center justify-between py-3 bg-gray-50 dark:bg-gray-800 text-end sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md">
                  <div>
                    <slot name="actions" />
                  </div>
                  <div>
                    <slot name="submit" />
                  </div>
                </div>
            </form>

          <div v-if="$props.taskGroup" class="mt-4">
            <TaskGroup :task-group="$props.taskGroup" />
          </div>
        </div>
    </div>
</template>
