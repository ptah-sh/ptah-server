<script setup>
import { computed, reactive } from "vue";
import TaskStatus from "./TaskStatus.vue";

const props = defineProps({
    task: Object,
});

const state = reactive({
    expanded: props.task.status === "failed",
});

const classes = computed(() => {
    const modifier =
        props.task.status === "failed"
            ? "text-red-600 dark:text-red-500"
            : "text-green-600 dark:text-green-500";

    const base = "px-4 py-2 flex items-center select-none " + modifier;

    if (props.task.result) {
        return base + " hover:cursor-pointer hover:bg-gray-50";
    }

    return base;
});
</script>

<template>
    <li
        v-auto-animate="{ duration: 100 }"
        class="w-full border-b border-gray-200 first:rounded-t-lg last:rounded-b-lg dark:border-gray-600 overflow-hidden"
    >
        <div
            :class="classes"
            @click="state.expanded = !state.expanded && $props.task.result"
        >
            <TaskStatus :task-status="task.status" />

            <span v-html="task.formatted_meta" class="grow" />

            <span class="text-xs me-2 text-gray-500">#{{ task.id }}</span>

            <span v-auto-animate="{ duration: 100 }" v-if="task.result">
                <svg
                    v-if="state.expanded"
                    class="w-4 h-4 text-gray-800 dark:text-white"
                    aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg"
                    width="24"
                    height="24"
                    fill="none"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke="currentColor"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="m16 14-4-4-4 4"
                    />
                </svg>
                <svg
                    v-else
                    class="w-4 h-4 text-gray-800 dark:text-white"
                    aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg"
                    width="24"
                    height="24"
                    fill="none"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke="currentColor"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="m8 10 4 4 4-4"
                    />
                </svg>
            </span>
            <span v-else class="w-4"></span>
        </div>
        <div
            v-if="state.expanded"
            class="px-4 py-2 border-t border-t-gray-100 bg-gray-50"
            v-html="task.formatted_result"
        />
    </li>
</template>
