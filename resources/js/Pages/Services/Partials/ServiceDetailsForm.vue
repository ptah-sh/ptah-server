<script setup>
import TextInput from "@/Components/TextInput.vue";
import TeamCard from "@/Components/TeamCard.vue";
import FormField from "@/Components/FormField.vue";
import Select from "@/Components/Select.vue";
import {effect, reactive} from "vue";

const model = defineModel();

const props = defineProps({
  team: Object,
  swarms: Array,
})
</script>

<template>
  <TeamCard :team="$props.team" />

  <FormField :error="model.errors.name" class="col-span-2">
    <template #label>Service Name</template>

    <TextInput v-model="model.name" class="block w-full"/>
  </FormField>

  <FormField :error="model.errors.swarm_id" class="col-span-2">
    <template #label>Swarm</template>

    <Select v-model="model.swarm_id" :disabled="model.id">
      <option v-for="swarm in $props.swarms" :value="swarm.id">{{ swarm.name }}</option>
    </Select>
  </FormField>
</template>