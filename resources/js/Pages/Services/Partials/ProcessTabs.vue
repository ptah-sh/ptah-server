<script setup>
import TabItem from "@/Components/Tabs/TabItem.vue";

const model = defineModel();

defineProps({
  'processes': Array,
  'block': String,
})
</script>

<template>
  <div class="inline-flex rounded-md shadow" role="group">
    <div class="z-10">
      <TabItem v-for="(process, index) in processes" :key="process.id" @click="model.selectedProcessIndex[block] = index" :active="model.selectedProcessIndex[block] === index">
        <template #label>
          {{ process.name }}
        </template>
      </TabItem>
    </div>
  </div>
</template>