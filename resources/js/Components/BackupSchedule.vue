<script setup lang="ts">
import Select from "@/Components/Select.vue";
import FormField from "@/Components/FormField.vue";
import TextInput from "@/Components/TextInput.vue";
import {effect} from "vue";

const model = defineModel();

const props = defineProps({
  's3Storages': Array,
  'errors': Object,
})

effect(() => {
  if (model.value.preset === 'daily') {
    model.value.expr = '0 0 * * *';
  }
});
</script>

<template>
  <FormField class="col-span-2" :error="props.errors?.['preset']">
    <template #label>Backup Schedule</template>

    <Select v-model="model.preset">
      <option value="daily">Every Day</option>
    </Select>
  </FormField>

  <FormField v-if="model.preset !== 'cron-disabled'" class="col-span-2" :error="props.errors?.['s3StorageId']">
    <template #label>Storage</template>

    <Select v-model="model.s3StorageId">
      <option v-for="s3Storage in props.s3Storages" :value="s3Storage.id">{{ s3Storage.name }}</option>
    </Select>
  </FormField>

<!--  <input v-if="model.preset === 'daily'" type="hidden" v-model="model.expr" value="0 0 * * *" />-->

<!--  <FormField v-if="model.preset === 'custom'" class="col-span-2">-->
<!--    <template #label>Expression</template>-->

<!--    <TextInput v-model="model.expr" />-->
<!--  </FormField>-->
</template>