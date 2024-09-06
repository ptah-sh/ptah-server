<script setup>
import { computed } from "vue";
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
    if (props.scope) {
        return `${props.scope}/${props.item.name}`;
    }

    return props.item.name;
});
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
