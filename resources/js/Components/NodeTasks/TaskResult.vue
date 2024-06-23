<script setup>
import {computed, reactive} from "vue";

const props = defineProps({
  'task': Object,
})

const state = reactive({
  expanded: props.task.status === 'failed',
})

const classes = computed(() => {
  const modifier = props.task.status === 'failed' ? 'text-red-600 dark:text-red-500' : 'text-green-600 dark:text-green-500';

  const base = 'px-4 py-2 flex items-center select-none ' + modifier;


  if (props.task.result) {
    return base + ' hover:cursor-pointer hover:bg-gray-50';
  }

  return base;
})

</script>

<template>
  <li
      v-auto-animate="{duration: 100}"
      class="w-full border-b border-gray-200 first:rounded-t-lg last:rounded-b-lg dark:border-gray-600 overflow-hidden">
<div :class="classes"
     @click="state.expanded = ! state.expanded && $props.task.result">
    <svg
        v-if="task.status === 'pending'"
        class="w-4 h-4 me-2 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
    </svg>

    <svg
        v-if="task.status === 'running'"
        class="w-4 h-4 me-2 animate-rotate text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.651 7.65a7.131 7.131 0 0 0-12.68 3.15M18.001 4v4h-4m-7.652 8.35a7.13 7.13 0 0 0 12.68-3.15M6 20v-4h4"/>
    </svg>

  <svg
      v-if="task.status === 'completed'"
      class="w-4 h-4 me-2 text-green-600 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
    <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm13.707-1.293a1 1 0 0 0-1.414-1.414L11 12.586l-1.793-1.793a1 1 0 0 0-1.414 1.414l2.5 2.5a1 1 0 0 0 1.414 0l4-4Z" clip-rule="evenodd"/>
  </svg>

  <svg
      v-if="task.status === 'failed'"
      class="w-4 h-4 me-2 text-red-600 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
    <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm7.707-3.707a1 1 0 0 0-1.414 1.414L10.586 12l-2.293 2.293a1 1 0 1 0 1.414 1.414L12 13.414l2.293 2.293a1 1 0 0 0 1.414-1.414L13.414 12l2.293-2.293a1 1 0 0 0-1.414-1.414L12 10.586 9.707 8.293Z" clip-rule="evenodd"/>
  </svg>

  <svg v-if="task.status === 'canceled'"
      class="w-4 h-4 me-2 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
    <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m6 6 12 12m3-6a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
  </svg>



  <span v-html="task.formatted_meta" class="grow" />

    <span class="text-xs me-2 text-gray-500">#{{ task.id }}</span>

    <span v-auto-animate="{duration: 100}" v-if="task.result">
      <svg  v-if="state.expanded"
            class="w-4 h-4 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m16 14-4-4-4 4"/>
</svg>
    <svg
        v-else
        class="w-4 h-4 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 10 4 4 4-4"/>
    </svg>

</span>
  <span v-else class="w-4"></span>
</div>
    <div v-if="state.expanded" class="px-4 py-2 border-t border-t-gray-100 bg-gray-50" v-html="task.formatted_result" />
  </li>
</template>