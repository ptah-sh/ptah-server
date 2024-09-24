<script setup lang="ts">
import TeamsLayout from "@/Pages/Teams/TeamsLayout.vue";
import { UsageQuotas, ItemQuota } from "@/types/quotas";
import { Link } from "@inertiajs/vue3";

interface Props {
    team: {
        id: number;
        name: string;
    };
    quotas: UsageQuotas;
    isOnTrial: boolean;
    quotaReached: boolean;
}

const props = defineProps<Props>();

function getUsagePercentage(quota: ItemQuota): number {
    return Math.min(100, (quota.currentUsage / quota.maxUsage) * 100);
}

function isQuotaFull(quota: ItemQuota): boolean {
    return (
        quota.currentUsage >= quota.maxUsage &&
        !quota.isSoftQuota &&
        !quota.isIntrinsic
    );
}

function isOverQuota(quota: ItemQuota): boolean {
    return quota.currentUsage > quota.maxUsage && !quota.isIntrinsic;
}

function getQuotaStatus(
    quota: ItemQuota,
): "normal" | "warning" | "full" | "over" {
    if (isOverQuota(quota)) return "over";
    if (isQuotaFull(quota)) return "full";
    if (quota.almostQuotaReached && !quota.isIntrinsic && !quota.isSoftQuota)
        return "warning";
    return "normal";
}

const quotaDescriptions = {
    nodes: "The maximum number of nodes you can add to your team.",
    swarms: "The maximum number of swarms you can create for your team.",
    services:
        "The maximum number of services you can deploy across all swarms.",
    deployments: "The maximum number of deployments you can perform per day.",
};

function getResetPeriodText(quota: ItemQuota): string | null {
    return quota.resetPeriod === "daily" ? "Resets daily" : null;
}
</script>

<template>
    <TeamsLayout :team="team">
        <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg p-6"
        >
            <h3
                class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4"
            >
                Team Quotas
            </h3>

            <div
                v-if="quotaReached"
                class="mb-4 p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700"
            >
                <p class="font-bold">Quota Limit Reached</p>
                <p>
                    You've reached the limit for one or more quotas. Consider
                    upgrading your plan to increase your limits.
                </p>
                <Link
                    :href="route('teams.billing.show', team.id)"
                    class="text-blue-600 hover:text-blue-800 underline"
                >
                    View Billing Options
                </Link>
            </div>

            <div v-if="quotas">
                <ul class="space-y-6">
                    <li v-for="(quota, key) in quotas" :key="key" class="py-4">
                        <div class="flex justify-between items-center">
                            <span
                                class="text-sm font-medium text-gray-900 dark:text-gray-100 capitalize"
                            >
                                {{ key }}
                                <span
                                    v-if="quota.isSoftQuota"
                                    class="text-xs text-gray-500 ml-1"
                                    >(Soft Quota)</span
                                >
                                <span
                                    v-if="quota.isIntrinsic"
                                    class="text-xs text-gray-500 ml-1"
                                    >(System Limit)</span
                                >
                            </span>
                            <span
                                :class="[
                                    'text-sm',
                                    {
                                        'text-green-500':
                                            getQuotaStatus(quota) === 'normal',
                                        'text-yellow-500':
                                            getQuotaStatus(quota) === 'warning',
                                        'text-red-500 font-bold': [
                                            'full',
                                            'over',
                                        ].includes(getQuotaStatus(quota)),
                                    },
                                ]"
                            >
                                {{ quota.currentUsage }} / {{ quota.maxUsage }}
                            </span>
                        </div>
                        <div
                            class="mt-1 text-xs text-gray-500 dark:text-gray-400 flex items-center"
                        >
                            {{
                                quotaDescriptions[
                                    key as keyof typeof quotaDescriptions
                                ]
                            }}
                            <span
                                v-if="quota.isSoftQuota && !quota.isIntrinsic"
                            >
                                If you need to increase this quota, please
                                contact
                                <a
                                    href="mailto:contact@ptah.sh"
                                    class="text-blue-600 hover:text-blue-800 ml-1"
                                    >contact@ptah.sh</a
                                >.
                            </span>
                            <span v-if="quota.isIntrinsic">
                                This limit is set by the system architecture and
                                cannot be increased.
                            </span>
                            <span
                                v-if="getResetPeriodText(quota)"
                                class="ml-2 px-2 py-0.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full font-medium"
                            >
                                {{ getResetPeriodText(quota) }}
                            </span>
                        </div>
                        <div class="mt-2">
                            <div
                                class="bg-gray-200 dark:bg-gray-700 rounded-full h-2.5"
                            >
                                <div
                                    :class="[
                                        'h-2.5 rounded-full',
                                        {
                                            'bg-green-600':
                                                getQuotaStatus(quota) ===
                                                'normal',
                                            'bg-yellow-600':
                                                getQuotaStatus(quota) ===
                                                'warning',
                                            'bg-red-600': [
                                                'full',
                                                'over',
                                            ].includes(getQuotaStatus(quota)),
                                        },
                                    ]"
                                    :style="{
                                        width: `${getUsagePercentage(quota)}%`,
                                    }"
                                ></div>
                            </div>
                        </div>
                        <div
                            v-if="getQuotaStatus(quota) === 'warning'"
                            class="mt-1 text-xs text-yellow-500"
                        >
                            Approaching quota limit
                        </div>
                        <div
                            v-else-if="getQuotaStatus(quota) === 'full'"
                            class="mt-1 text-xs text-red-500"
                        >
                            Quota limit reached
                        </div>
                        <div
                            v-else-if="getQuotaStatus(quota) === 'over'"
                            class="mt-1 text-xs text-red-500"
                        >
                            Usage exceeds quota limit
                        </div>
                    </li>
                </ul>
            </div>
            <div v-else class="text-gray-500 dark:text-gray-400">
                No quotas found for this team.
            </div>
        </div>
    </TeamsLayout>
</template>
