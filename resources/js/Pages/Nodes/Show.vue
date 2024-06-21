<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from "@/Components/PrimaryButton.vue";
import FormSection from "@/Components/FormSection.vue";
import NewSwarmCluster from "@/Pages/Nodes/Partials/NewSwarmCluster.vue";
import ServerDetailsForm from "@/Pages/Nodes/Partials/ServerDetailsForm.vue";
import LayoutTab from "@/Components/LayoutTab.vue";
import ShowLayout from "@/Pages/Nodes/ShowLayout.vue";
import { CopyClipboard } from 'flowbite';
import {onMounted, ref} from "vue";
import AgentInstall from "@/Pages/Nodes/Partials/AgentInstall.vue";
import AgentStatus from "@/Pages/Nodes/Partials/AgentStatus.vue";
import SectionBorder from "@/Components/SectionBorder.vue";
import InitSwarmProgress from "@/Pages/Nodes/Partials/InitSwarmProgress.vue";

defineProps([
    'node',
    'initTaskGroup',
]);



</script>

<template>
    <ShowLayout :node="$props.node">
        <ServerDetailsForm :node="$props.node"/>

      <SectionBorder />

        <AgentStatus :node="$props.node"/>

      <SectionBorder v-if="$props.node.online" />

        <NewSwarmCluster v-if="$props.node.online && $props.node.swarm_id === null" :node="$props.node"/>

      <SectionBorder v-if="$props.initTaskGroup" />
      <InitSwarmProgress v-if="$props.initTaskGroup" :taskGroup="$props.initTaskGroup" />
    </ShowLayout>
</template>
