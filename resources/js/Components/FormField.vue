<script setup>
import { ref, onMounted, reactive, onActivated } from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    error: String,
});

const slot = ref(null);

const state = reactive({
    domId: "",
});

onMounted(() => {
    state.domId = "f-" + Math.random().toString(36).slice(2);

    slot.value.children[0]?.setAttribute("id", state.domId);
});
</script>

<template>
    <div v-auto-animate>
        <div class="flex justify-between items-center">
            <InputLabel :for="state.domId">
                <slot name="label" />
            </InputLabel>

            <InputLabel class="text-xs">
                <slot name="counter" />
            </InputLabel>
        </div>

        <div ref="slot">
            <slot />
        </div>

        <div v-if="$slots.hint" class="text-sm text-gray-500 pt-2">
            <slot name="hint" />
        </div>

        <InputError :message="$props.error" class="mt-2" />
    </div>
</template>
