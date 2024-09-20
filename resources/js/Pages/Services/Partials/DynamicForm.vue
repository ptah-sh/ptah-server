<script setup>
import { computed, effect, ref } from "vue";
import TextInput from "@/Components/TextInput.vue";
import TextArea from "@/Components/TextArea.vue";
import FormField from "@/Components/FormField.vue";

const props = defineProps({
    scope: String,
    item: Object,
    form: Object,
    errors: Object,
});

const itemName = computed(() => {
    return `${props.scope}/${props.item.name}`;
});

const isCollapsibleOpen = ref(false);

const toggleCollapsible = () => {
    isCollapsibleOpen.value = !isCollapsibleOpen.value;
};
</script>

<template>
    <div v-if="item.type === 'v-stack'" class="flex flex-col gap-4">
        <template v-for="item in item.items" :key="'v-' + item.id">
            <DynamicForm
                :item="item"
                :form="form"
                :errors="errors"
                :scope="scope"
            />
        </template>
    </div>

    <div v-else-if="item.type === 'h-stack'" class="flex flex-row gap-4">
        <template v-for="item in item.items" :key="'h-' + item.id">
            <DynamicForm
                :item="item"
                :form="form"
                :errors="errors"
                :scope="scope"
            />
        </template>
    </div>

    <div
        v-else-if="item.type === 'collapsible'"
        class="w-full my-2"
        v-auto-animate
    >
        <button
            @click="toggleCollapsible"
            class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900"
        >
            <span class="w-full nowrap">{{
                item.label || "Advanced Options"
            }}</span>
            <svg
                :class="{ 'rotate-180': isCollapsibleOpen }"
                class="w-5 h-5 transition-transform duration-200"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
            >
                <path
                    fill-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                    clip-rule="evenodd"
                />
            </svg>
        </button>
        <div v-if="isCollapsibleOpen" class="mt-2 flex flex-col gap-4">
            <template
                v-for="subItem in item.items"
                :key="'collapsible-' + subItem.id"
            >
                <DynamicForm
                    :item="subItem"
                    :form="form"
                    :errors="errors"
                    :scope="scope"
                />
            </template>
        </div>
    </div>

    <template v-else>
        <FormField class="w-full" :error="errors[itemName]">
            <template #label>{{ item.label }}</template>

            <TextInput
                v-if="item.type === 'text-field' && !item.multiline"
                v-model="form[itemName]"
                class="w-full"
            />

            <TextArea
                v-else-if="item.type === 'text-field' && item.multiline"
                v-model="form[itemName]"
                class="w-full"
            />
        </FormField>
    </template>
</template>
