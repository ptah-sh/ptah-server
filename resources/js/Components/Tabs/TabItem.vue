<script setup>
import { computed } from "vue";
import CloseButton from "../CloseButton.vue";

const props = defineProps({
    label: String,
    active: Boolean,
    closable: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["close"]);

const classes = computed(() => {
    const base = "px-1 py-1 inline-block";
    if (props.active) {
        return base + " text-blue-700 border-b-2 border-blue-700";
    } else {
        return (
            base +
            " text-gray-500 border-b-2 border-transparent hover:text-blue-700 hover:border-blue-700"
        );
    }
});

const handleClose = (event) => {
    event.stopPropagation();
    emit("close");
};
</script>

<template>
    <div
        class="relative group inline-flex items-center px-3 py-1 relative rounded [&:not(:last-child)]:rounded-tr-none [&:not(:first-child)]:rounded-tl-none rounded-bl-none rounded-br-none text-xs font-medium text-gray-900 bg-white hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white"
    >
        <button type="button" class="">
            <span :class="classes" v-auto-animate>
                <template v-if="$slots.label()[0].children.length">
                    <slot name="label" />
                </template>
                <template v-else>
                    <i>unnamed</i>
                </template>
            </span>
        </button>
        <CloseButton v-if="closable" @click="handleClose" />
    </div>
</template>
