<script setup>
import ShowLayout from "@/Pages/Nodes/ShowLayout.vue";
import { ref, onMounted, onUnmounted, computed } from "vue";
import VueApexCharts from "vue3-apexcharts";
import { router } from "@inertiajs/vue3";
import Card from "@/Components/Card.vue";
import Warning from "@/Components/Warning.vue";

const props = defineProps(["node", "metrics"]);

function getSeries(metrics, name, extraLabels = {}) {
    return metrics.value.data.result
        .filter(
            (result) =>
                result.metric["__name__"] === name &&
                Object.entries(extraLabels).every(
                    ([key, value]) => result.metric[key] === value,
                ),
        )
        .flatMap((result) => {
            return result.values.map((value) => {
                return {
                    x: value[0],
                    y: parseFloat(value[1]),
                };
            });
        });
}

const metrics = ref(props.metrics);

const refreshTimeoutId = ref(0);
onMounted(() => {
    refreshTimeoutId.value = setInterval(() => {
        router.visit(route("nodes.show", props.node), {
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

const baseChartOptions = {
    chart: {
        height: 175,
        type: "area",
        toolbar: {
            show: false,
        },
        animations: {
            enabled: false,
        },
    },
    dataLabels: {
        enabled: false,
    },
    legend: {
        show: true,
        // position: 'right',
        // floating: true,
    },
    stroke: {
        curve: "straight",
        width: 2,
        // colors: '#FEB019',
    },
    xaxis: {
        type: "datetime",
        tooltip: {
            enabled: false,
        },
        labels: {
            formatter: (value) => {
                return new Date(value * 1000).toLocaleTimeString();
            },
        },
    },
};

const percentChartOptions = {
    ...baseChartOptions,
    yaxis: {
        min: 0,
        max: 100,
        labels: {
            formatter: (value) => {
                return value + "%";
            },
        },
        tooltip: {
            enabled: false,
        },
    },
};

const networkChartOptions = {
    ...baseChartOptions,
    yaxis: {
        labels: {
            formatter: (value) => {
                return value + " KB/s";
            },
        },
        tooltip: {
            enabled: false,
        },
    },
};

const cpuUsageSeries = computed(() => {
    return [
        {
            name: "CPU Usage",
            data: getSeries(metrics, "cpu_usage"),
        },
    ];
});

const memoryUsageSeries = computed(() => {
    return [
        {
            name: "Memory Usage",
            data: getSeries(metrics, "memory_usage"),
        },
        {
            name: "Swap Usage",
            data: getSeries(metrics, "swap_usage"),
        },
    ];
});

const diskUsageSeries = computed(() => {
    return [
        {
            name: "Disk Usage",
            data: getSeries(metrics, "disk_usage"),
        },
    ];
});

const networks = computed(() => {
    return metrics.value.data.result
        .filter((result) => result.metric["__name__"] === "network_rx_bytes")
        .flatMap((result) => {
            return result.metric.interface;
        });
});

const networksSeries = computed(() => {
    return networks.value.reduce((acc, ifname) => {
        acc[ifname] = [
            {
                name: "Inbound",
                data: getSeries(metrics, "network_rx_bytes", {
                    interface: ifname,
                }),
            },
            {
                name: "Outbound",
                data: getSeries(metrics, "network_tx_bytes", {
                    interface: ifname,
                }),
            },
        ];
        return acc;
    }, {});
});

function getStatusCodeColor(statusCode) {
    statusCode = parseInt(statusCode);

    if (statusCode >= 200 && statusCode < 300) {
        return "#00FF00";
    } else if (statusCode >= 300 && statusCode < 400) {
        return "#FFFF00";
    } else if (statusCode >= 400 && statusCode < 500) {
        return "#FF0000";
    } else {
        return "#000000";
    }
}

const httpRequestsSeries = computed(() => {
    const statusCodes = metrics.value.data.result
        .filter((result) => result.metric["__name__"] === "http_requests_count")
        .flatMap((result) => {
            return result.metric.status_code;
        });

    return statusCodes.map((statusCode) => {
        return {
            name: statusCode,
            data: getSeries(metrics, "http_requests_count", {
                status_code: statusCode,
            }),
            color: getStatusCodeColor(statusCode),
        };
    });
});

const httpRequestsChartOptions = {
    ...baseChartOptions,
    yaxis: {
        labels: {
            formatter: (value) => {
                return value + " reqs";
            },
        },
    },
};

const httpRequestsDurationSeries = computed(() => {
    const buckets = [
        "0.005",
        "0.01",
        "0.025",
        "0.05",
        "0.1",
        "0.25",
        "0.5",
        "1",
        "2.5",
        "5",
        "10",
        "+Inf",
    ];

    return buckets.map((bucket, index) => {
        const prevSeries = buckets[index - 1]
            ? getSeries(metrics, "http_requests_duration", {
                  le: buckets[index - 1],
              })
            : [];

        return {
            name: bucket + " s",
            data: getSeries(metrics, "http_requests_duration", {
                le: bucket,
            }).map((value, index) => {
                return {
                    x: value.x,
                    y: value.y - (prevSeries[index]?.y || 0),
                };
            }),
        };
    });
});

const httpRequestsDurationChartOptions = {
    ...baseChartOptions,
    chart: {
        ...baseChartOptions.chart,
        type: "heatmap",
    },
    yaxis: {
        labels: {
            formatter: (value) => {
                return typeof value === "string" ? value : value + " reqs";
            },
        },
    },
};
</script>

<template>
    <ShowLayout :node="props.node">
        <Warning
            v-if="!node.online"
            :title="'The ' + props.node.name + ' node is offline'"
            :link="{
                href: route('nodes.settings', props.node),
                text: 'Check Agent Status',
            }"
        >
            The node is offline. It may be due to a temporary network issue or a
            problem with the node itself.
            <br />
            You might need to start an Agent on the node.
        </Warning>

        <div class="grid md:grid-cols-2 gap-8">
            <Card title="CPU Usage">
                <VueApexCharts
                    height="175px"
                    :options="percentChartOptions"
                    :series="cpuUsageSeries"
                />
            </Card>
            <Card title="Memory Usage">
                <VueApexCharts
                    height="175px"
                    :options="percentChartOptions"
                    :series="memoryUsageSeries"
                />
            </Card>
            <Card title="Disk Usage">
                <VueApexCharts
                    height="175px"
                    :options="percentChartOptions"
                    :series="diskUsageSeries"
                />
            </Card>
            <Card
                v-for="ifname in networks"
                :key="ifname"
                :title="networks.length > 1 ? 'Network ' + ifname : 'Network'"
            >
                <VueApexCharts
                    height="175px"
                    :options="networkChartOptions"
                    :series="networksSeries[ifname]"
                />
            </Card>
            <Card title="HTTP Requests">
                <VueApexCharts
                    height="175px"
                    :options="httpRequestsChartOptions"
                    :series="httpRequestsSeries"
                />
            </Card>
            <Card title="HTTP Requests Duration">
                <VueApexCharts
                    height="175px"
                    :options="httpRequestsDurationChartOptions"
                    :series="httpRequestsDurationSeries"
                />
            </Card>
        </div>
    </ShowLayout>
</template>
