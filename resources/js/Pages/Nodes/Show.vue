<script setup>
import NewSwarmCluster from "@/Pages/Nodes/Partials/NewSwarmCluster.vue";
import ServerDetailsForm from "@/Pages/Nodes/Partials/ServerDetailsForm.vue";
import ShowLayout from "@/Pages/Nodes/ShowLayout.vue";
import AgentStatus from "@/Pages/Nodes/Partials/AgentStatus.vue";
import SectionBorder from "@/Components/SectionBorder.vue";
import InitSwarmProgress from "@/Pages/Nodes/Partials/InitSwarmProgress.vue";
import SwarmDetails from "@/Pages/Nodes/Partials/SwarmDetails.vue";
import AgentUpgradeStatus from "@/Pages/Nodes/Partials/AgentUpgradeStatus.vue";

defineProps([
    'node',
    'initTaskGroup',
    'lastAgentVersion',
    'agentUpgradeTaskGroup',
]);
</script>

<template>
    <ShowLayout :node="$props.node">
        <ServerDetailsForm :node="$props.node"/>

      <SectionBorder />

      <AgentUpgradeStatus v-if="$props.agentUpgradeTaskGroup" :task-group="$props.agentUpgradeTaskGroup" />
      <AgentStatus v-else :node="$props.node" :lastAgentVersion="$props.lastAgentVersion"/>

      <SectionBorder v-if="$props.node.online" />

      <template v-if="$props.node.online">
        <NewSwarmCluster v-if="$props.node.swarm_id === null" :node="$props.node"/>
        <InitSwarmProgress v-if="$props.initTaskGroup" :taskGroup="$props.initTaskGroup" />
        <SwarmDetails v-if="!$props.initTaskGroup && $props.node.swarm_id !== null" :node="$props.node"/>
      </template>
    </ShowLayout>
</template>
