<script setup>
import InitSwarmCluster from "@/Pages/Nodes/Partials/InitSwarmCluster.vue";
import JoinSwarmCluster from "@/Pages/Nodes/Partials/JoinSwarmCluster.vue";
import ServerDetailsForm from "@/Pages/Nodes/Partials/ServerDetailsForm.vue";
import ShowLayout from "@/Pages/Nodes/ShowLayout.vue";
import AgentStatus from "@/Pages/Nodes/Partials/AgentStatus.vue";
import SectionBorder from "@/Components/SectionBorder.vue";
import InitSwarmProgress from "@/Pages/Nodes/Partials/InitSwarmProgress.vue";
import SwarmDetails from "@/Pages/Nodes/Partials/SwarmDetails.vue";
import AgentUpgradeStatus from "@/Pages/Nodes/Partials/AgentUpgradeStatus.vue";
import DockerRegistries from "@/Pages/Nodes/Partials/DockerRegistries.vue";
import S3Storages from "@/Pages/Nodes/Partials/S3Storages.vue";
import DeleteResourceSection from "@/Components/DeleteResourceSection.vue";
import { router } from "@inertiajs/vue3";

const props = defineProps([
    "node",
    "isLastNode",
    "initTaskGroup",
    "lastAgentVersion",
    "agentUpgradeTaskGroup",
    "registryUpdateTaskGroup",
]);

const destroyNode = () => router.delete(route("nodes.destroy", props.node.id));
</script>

<template>
    <ShowLayout :node="$props.node">
        <ServerDetailsForm :node="$props.node" />

        <SectionBorder />

        <AgentUpgradeStatus
            v-if="$props.agentUpgradeTaskGroup"
            :task-group="$props.agentUpgradeTaskGroup"
        />
        <AgentStatus
            v-else
            :node="$props.node"
            :lastAgentVersion="$props.lastAgentVersion"
        />

        <SectionBorder v-if="$props.node.online" />

        <template v-if="$props.node.online">
            <InitSwarmCluster
                v-if="isLastNode && node.swarm_id === null"
                :node="$props.node"
            />

            <JoinSwarmCluster
                v-if="!isLastNode && node.swarm_id === null"
                :node="$props.node"
            />

            <InitSwarmProgress
                v-if="$props.initTaskGroup"
                :taskGroup="$props.initTaskGroup"
            />
            <template
                v-if="!$props.initTaskGroup && $props.node.swarm_id !== null"
            >
                <SwarmDetails :node="$props.node" />

                <SectionBorder />

                <S3Storages
                    :swarm="$props.node.swarm"
                    :task-group="$props.s3StoragesTaskGroup"
                />

                <SectionBorder />

                <DockerRegistries
                    :swarm="$props.node.swarm"
                    :task-group="$props.registryUpdateTaskGroup"
                />
            </template>
        </template>

        <SectionBorder />

        <DeleteResourceSection
            resource-kind="Node"
            :resource-name="node.name"
            :destroy="destroyNode"
        />
    </ShowLayout>
</template>
