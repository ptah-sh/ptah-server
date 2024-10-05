<script setup>
import { router } from "@inertiajs/vue3";
import { ref, computed, onMounted, onUnmounted } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import NodeStatus from "@/Components/NodeStatus.vue";
import ValueCard from "@/Components/ValueCard.vue";
import NoDataYet from "@/Components/NoDataYet.vue";
import { FwbTooltip } from "flowbite-vue";

const props = defineProps({
    nodes: Array,
    nodesLimitReached: Boolean,
    metrics: Object,
});

const metrics = ref(props.metrics);

function getMetric(metric) {
    return metrics.value.data.result
        .filter((m) => m.metric.__name__ === metric)
        .map((m) => m.value[1])[0];
}

const avgLoad = computed(() => {
    const metric1m = getMetric("ptah_node_load_avg_1m") ?? "...";
    const metric5m = getMetric("ptah_node_load_avg_5m") ?? "...";
    const metric15m = getMetric("ptah_node_load_avg_15m") ?? "...";

    return `${metric1m} / ${metric5m} / ${metric15m}`;
});

const cpuUsage = computed(() => {
    const metric = getMetric("cpu_usage") ?? "...";

    return metric + "%";
});

const memoryUsage = computed(() => {
    const metric = getMetric("memory_usage") ?? "...";

    return metric + "%";
});

const diskUsage = computed(() => {
    const metric = getMetric("disk_usage") ?? "...";

    return metric + "%";
});

const refreshTimeoutId = ref(0);
onMounted(() => {
    refreshTimeoutId.value = setInterval(() => {
        router.visit(route("nodes.index"), {
            method: "get",
            preserveState: true,
            preserveScroll: true,
            only: ["metrics"],
            onSuccess: (page) => {
                metrics.value = page.props.metrics;
            },
            onError: (errors) => {
                // Handle errors if needed
            },
        });
    }, 5000);
});

onUnmounted(() => {
    clearTimeout(refreshTimeoutId.value);
});
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Nodes
            </h2>
        </template>

        <template #actions>
            <fwb-tooltip v-if="nodesLimitReached">
                <template #trigger>
                    <PrimaryButton
                        type="button"
                        @click="router.get(route('nodes.create'))"
                        disabled="disabled"
                        >Create</PrimaryButton
                    >
                </template>

                <template #content>
                    You have reached your node limit. Please upgrade your plan.
                </template>
            </fwb-tooltip>
            <PrimaryButton
                v-else
                type="button"
                @click="router.get(route('nodes.create'))"
                >Create</PrimaryButton
            >
        </template>

        <div class="py-12">
            <div
                class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-2 gap-4"
            >
                <NoDataYet v-if="nodes.length === 0" />

                <a
                    v-for="node in nodes"
                    :key="node.id"
                    :href="route('nodes.show', { node: node.id })"
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg p-4 flex flex-col gap-4"
                >
                    <div class="flex gap-4">
                        <div class="flex">
                            <div class="font-bold text-xl">{{ node.name }}</div>
                            <NodeStatus :node="node" />
                        </div>
                        <div class="flex flex-col">
                            <template
                                v-if="node.online"
                                v-for="network in node.data.host.networks"
                                :key="network.if_name"
                            >
                                <ValueCard
                                    v-for="ip in network.ips"
                                    :key="ip.ip"
                                    :label="network.if_name"
                                    :value="ip.ip"
                                />
                            </template>
                        </div>
                    </div>
                    <div class="flex justify-between" v-if="node.online">
                        <div class="flex flex-col">
                            <div class="flex flex-row justify-between">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500"
                                        >Load 1 / 5 / 15 min</span
                                    >
                                    <span class="text-lg font-bold">{{
                                        avgLoad
                                    }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <div class="flex flex-row justify-between">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500"
                                        >CPU Usage</span
                                    >
                                    <span class="text-lg font-bold">{{
                                        cpuUsage
                                    }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <div class="flex flex-row justify-between">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500"
                                        >Memory Usage</span
                                    >
                                    <span class="text-lg font-bold">{{
                                        memoryUsage
                                    }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <div class="flex flex-row justify-between">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500"
                                        >Disk Usage</span
                                    >
                                    <span class="text-lg font-bold">{{
                                        diskUsage
                                    }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </AppLayout>
</template>
