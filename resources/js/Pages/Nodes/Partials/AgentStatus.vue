
<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AgentInstall from "@/Pages/Nodes/Partials/AgentInstall.vue";
import FormSection from "@/Components/FormSection.vue";
import ValueCard from "@/Components/ValueCard.vue";

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
    <div v-if="$props.node.data" class="flex">
      <ValueCard :value="$props.node.data.version" label="Agent Version"/>
      <template v-for="network in $props.node.data.host.networks" :key="network.if_name">
        <ValueCard v-for="ip in network.ips" :key="ip" :value="ip.ip" :label="network.if_name + '/' + ip.proto" />
      </template>
      <ValueCard :value="$props.node.data.docker.platform.name" label="Docker Platform"/>
    </div>
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