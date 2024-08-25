<script setup>
import TextInput from "@/Components/TextInput.vue";
import TeamCard from "@/Components/TeamCard.vue";
import FormField from "@/Components/FormField.vue";

const model = defineModel();

const props = defineProps({
    team: Object,
    service: Object,
});
</script>

<template>
    <TeamCard :team="$props.team" />

    <FormField :error="model.errors.name" class="col-span-2">
        <template #label>Service Name</template>

        <TextInput v-model="model.name" class="block w-full" />
    </FormField>

    <FormField v-if="service" class="col-span-2">
        <template #label>Service Slug</template>

        <TextInput :value="props.service.slug" class="block w-full" readonly />
    </FormField>
</template>
