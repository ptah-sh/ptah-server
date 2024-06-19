
<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AgentInstall from "@/Pages/Nodes/Partials/AgentInstall.vue";
import FormSection from "@/Components/FormSection.vue";

defineProps([
    'node'
])
</script>
<template>
<FormSection>
<template #title>
  Agent Status

</template>

<template #form>
  <div v-if="$props.node.online">
  </div>
  <div v-else class="col-span-10">
    <AgentInstall />
  </div>
</template>

<template #actions>
  <PrimaryButton type="button">Upgrade to 1.1.1</PrimaryButton>
</template>
</FormSection>
</template>