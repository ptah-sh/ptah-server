<script setup>
import { computed, onMounted, ref } from "vue";

const props = defineProps({
    modelValue: String | Number,
    disabled: Boolean,
    readonly: Boolean,
});

defineEmits(["update:modelValue"]);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute("autofocus")) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });

const classes = computed(() => {
    const base =
        "border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm";

    if (props.disabled) {
        return `${base} opacity-50 cursor-not-allowed bg-gray-200`;
    } else if (props.readonly) {
        return `${base} bg-gray-200 cursor-not-allowed`;
    } else {
        return base;
    }
});
</script>

<template>
    <input
        ref="input"
        :class="classes"
        :value="modelValue"
        :disabled="props.disabled"
        :readonly="props.readonly"
        @input="$emit('update:modelValue', $event.target.value)"
    />
</template>
