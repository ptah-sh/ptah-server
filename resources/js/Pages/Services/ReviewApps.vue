<script setup>
import ShowLayout from "@/Pages/Services/ShowLayout.vue";
import TaskGroup from "@/Components/NodeTasks/TaskGroup.vue";
import Card from "@/Components/Card.vue";
import ExternalLink from "@/Components/ExternalLink.vue";
import GitRepoLink from "@/Components/GitRepoLink.vue";
import RelativeDate from "@/Components/RelativeDate.vue";
import NoDataYet from "@/Components/NoDataYet.vue";

const props = defineProps({
    service: Object,
});

function getVisitUrl(reviewApp) {
    const caddys = reviewApp.latest_deployment.data.processes[0].caddy;
    if (caddys.length === 0) {
        return null;
    }

    const caddy = caddys[0];

    const schema = caddy.publishedPort === 443 ? "https" : "http";

    return {
        url: `${schema}://${caddy.domain}`,
        hostname: caddy.domain,
    };
}
</script>

<template>
    <ShowLayout :service="$props.service">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <Card
                    v-for="reviewApp in service.review_apps"
                    :key="reviewApp.id"
                >
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">
                            #{{ reviewApp.ref }}
                        </h3>
                        <span
                            class="px-2.5 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200"
                        >
                            {{ reviewApp.process }}.{{ reviewApp.worker }}
                        </span>
                    </div>
                    <GitRepoLink :url="reviewApp.ref_url" />

                    <div
                        class="flex items-center text-sm text-gray-600 dark:text-gray-400"
                    >
                        <svg
                            class="w-4 h-4 mr-2"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                        {{ reviewApp.latest_deployment.data.internalDomain }}
                    </div>

                    <div class="space-y-2">
                        <div
                            class="flex items-center text-sm text-gray-600 dark:text-gray-400"
                        >
                            <RelativeDate :date="reviewApp.created_at">
                                <template #prefix>
                                    <svg
                                        class="w-4 h-4 mr-2"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>

                                    Created
                                </template>
                            </RelativeDate>
                        </div>

                        <div
                            v-if="getVisitUrl(reviewApp)"
                            class="flex flex-col gap-2"
                        >
                            <ExternalLink :href="getVisitUrl(reviewApp).url">
                                {{ getVisitUrl(reviewApp).hostname }}
                            </ExternalLink>
                        </div>
                    </div>

                    <TaskGroup
                        v-if="reviewApp.latest_task_group"
                        :task-group="reviewApp.latest_task_group"
                        class="mt-4"
                    />
                </Card>
            </div>

            <NoDataYet v-if="!service.review_apps.length">
                No review apps available
            </NoDataYet>
        </div>
    </ShowLayout>
</template>
