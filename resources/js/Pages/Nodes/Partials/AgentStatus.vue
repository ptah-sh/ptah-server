
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

    <template #description>
      Agent on your server is up and running. You can see the agent's version, available IP interfaces and upgrade the agent, if a new version is available.
    </template>

    <template #form>
      <div v-if="$props.node.online && $props.node.data" class="col-span-full">
        <ValueCard :value="$props.node.data.version" label="Agent Version"/>
        <template v-for="network in $props.node.data.host.networks" :key="network.if_name">
          <ValueCard v-for="ip in network.ips" :key="ip" :value="ip.ip" :label="network.if_name" />
        </template>
        <ValueCard :value="$props.node.data.docker.platform.name" label="Docker Platform"/>
      </div>
      <div v-else class="col-span-10">
        <AgentInstall :node="$props.node" />
      </div>
    </template>

    <template #actions>
      <PrimaryButton type="button">Upgrade to 1.1.1</PrimaryButton>
    </template>
  </FormSection>
</template>