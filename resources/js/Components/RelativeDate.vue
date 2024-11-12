<script setup>
import dayjs from "dayjs";
import { FwbTooltip } from "flowbite-vue";

const props = defineProps({
    date: {
        type: [String, Date],
        required: true,
    },
});

const dateRelative = dayjs(props.date).fromNow();
const dateAbsolute = dayjs(props.date).format("LLLL");
</script>

<template>
    <span class="inline-flex items-center">
        <span
            v-if="$slots.prefix"
            class="me-1 inline-flex mb-1 border-b-2 border-transparent items-center"
        >
            <slot name="prefix" />
        </span>

        <FwbTooltip>
            <template #trigger>
                <span class="inline-block border-b-2 border-dotted mb-1">{{
                    dateRelative
                }}</span>
            </template>

            <template #content>
                {{ dateAbsolute }}
            </template>
        </FwbTooltip>
    </span>
</template>
