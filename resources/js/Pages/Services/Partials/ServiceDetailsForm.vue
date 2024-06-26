<script setup>
import TextInput from "@/Components/TextInput.vue";
import TeamCard from "@/Components/TeamCard.vue";
import FormField from "@/Components/FormField.vue";
import Select from "@/Components/Select.vue";

const model = defineModel();

const props = defineProps({
  team: Object,
  swarms: Array,
})
</script>

<template>
  <div class="col-span-6">
    <TeamCard :team="$props.team" />

    <FormField :error="model.errors.swarm_id">
      <template #label>Swarm</template>

      <Select v-model="model.swarm_id" :disabled="model.id">
        <option v-for="swarm in $props.swarms" :value="swarm.id">{{ swarm.name }}</option>
      </Select>
    </FormField>

    <FormField :error="model.errors.name">
      <template #label>Service Name</template>

      <TextInput v-model="model.name" class="block w-full"/>
    </FormField>
  </div>
</template>