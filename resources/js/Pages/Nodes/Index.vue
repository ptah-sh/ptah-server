<script setup>
import { router } from '@inertiajs/vue3'

import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from "@/Components/PrimaryButton.vue";
import NodeStatus from "@/Components/NodeStatus.vue";
import ValueCard from "@/Components/ValueCard.vue";
import NoDataYet from "@/Components/NoDataYet.vue";

defineProps({
  'nodes': Array,
})
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Nodes
            </h2>
        </template>

        <template #actions>
            <PrimaryButton type="button" @click="router.get(route('nodes.create'))">Create</PrimaryButton>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex gap-8">
              <NoDataYet v-if="nodes.length === 0" />

                <a v-for="node in nodes" :key="node.id" :href="route('nodes.show', {node: node.id})"
                    class="w-96 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-4 flex justify-around">

                  <div class="flex">
                    <div class="font-bold text-xl">{{ node.name }}</div>
                    <NodeStatus :node="node" />
                  </div>
                  <div                       class="flex flex-col"
                  >
                  <template
                      v-if="node.online"
                      v-for="network in node.data.host.networks" :key="network.if_name"
                  >
                    <ValueCard
                        v-for="ip in network.ips" :key="ip.ip"
                        :label="network.if_name" :value="ip.ip" />
                  </template>
                  </div>
                </a>
            </div>
        </div>
    </AppLayout>
</template>
