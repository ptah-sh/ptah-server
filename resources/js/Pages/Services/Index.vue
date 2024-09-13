<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { router } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TaskResult from "@/Components/NodeTasks/TaskResult.vue";
import { Link } from "@inertiajs/vue3";
import NoDataYet from "@/Components/NoDataYet.vue";
import { FwbTooltip } from "flowbite-vue";

const props = defineProps({
    services: Array,
    swarmExists: Boolean,
    quotaReached: Boolean,
});
</script>

<template>
    <AppLayout title="Services">
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex"
            >
                Services
            </h2>
        </template>

        <template #actions>
            <PrimaryButton
                v-if="props.swarmExists && !props.quotaReached"
                type="button"
                @click="router.get(route('services.create'))"
                >Create</PrimaryButton
            >
            <fwb-tooltip v-else>
                <template #trigger>
                    <PrimaryButton type="button" disabled>Create</PrimaryButton>
                </template>
                <template #content>
                    <span v-if="!props.swarmExists"
                        >Please initialize a Swarm first</span
                    >
                    <span v-else>
                        Service quota reached. Visit the
                        <a
                            :href="
                                route('teams.show', {
                                    team: $page.props.auth.user.current_team_id,
                                })
                            "
                            class="text-blue-500 hover:underline"
                        >
                            Team Quotas page
                        </a>
                        to see the options to increase the quota.
                    </span>
                </template>
            </fwb-tooltip>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <NoDataYet v-if="$props.services.length === 0" />

                <div class="grid grid-cols-2 gap-4">
                    <div v-for="service in props.services" :key="service.slug">
                        <div
                            class="bg-white dark:bg-gray-800 shadow sm:rounded-lg"
                        >
                            <Link
                                :href="route('services.show', service)"
                                class="p-4 grid grid-cols-2"
                            >
                                <div class="flex flex-col">
                                    <span class="font-semibold text-lg">{{
                                        service.name
                                    }}</span>
                                    <span class="text-sm text-gray-500">{{
                                        service.latest_deployment.data
                                            .dockerImage
                                    }}</span>
                                </div>

                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">{{
                                        service.latest_deployment.data
                                            .internalDomain
                                    }}</span>
                                    <!-- FIXME: collect urls from all processes -->
                                    <span
                                        v-if="
                                            service.latest_deployment.data
                                                .processes[0].caddy[0]
                                        "
                                        class="text-sm text-gray-400 text-nowrap truncate"
                                    >
                                        <span
                                            v-if="
                                                service.latest_deployment.data
                                                    .processes[0].caddy[0]
                                                    .publishedPort === 80
                                            "
                                            >http://</span
                                        >
                                        <span
                                            v-else-if="
                                                service.latest_deployment.data
                                                    .processes[0].caddy[0]
                                                    .publishedPort === 443
                                            "
                                            >https://</span
                                        >
                                        <span class="text-black">{{
                                            service.latest_deployment.data
                                                .processes[0].caddy[0].domain
                                        }}</span>
                                        <span>{{
                                            service.latest_deployment.data
                                                .processes[0].caddy[0].path
                                        }}</span>
                                    </span>
                                    <span
                                        v-if="
                                            service.latest_deployment.data
                                                .processes[0].caddy.length > 1
                                        "
                                        class="text-xs text-gray-400"
                                        >(+{{
                                            service.latest_deployment.data
                                                .processes[0].caddy.length - 1
                                        }}
                                        more)</span
                                    >
                                </div>

                                <!--          <pre>{{service}}</pre>-->
                            </Link>
                            <ul
                                v-if="
                                    service.latest_deployment.latest_task_group
                                        .status !== 'completed'
                                "
                                class="border-t-2 relative"
                            >
                                <TaskResult
                                    :task="
                                        service.latest_deployment
                                            .latest_task_group.latest_task
                                    "
                                    class=""
                                >
                                </TaskResult>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
