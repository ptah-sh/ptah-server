<script setup>
import { onMounted, ref } from "vue";
import { CopyClipboard } from "flowbite";
import InputLabel from "@/Components/InputLabel.vue";
import Tour from "@/Components/Tour.vue";
import CopyToClipboard from "@/Components/CopyToClipboard.vue";

defineProps({
    node: Object,
});

const providers = [
    {
        name: "Hetzner",
        description: "Affordable European hosting with great performance",
        logo: "/images/hetzner-logo.svg",
        link: "https://r.ptah.sh/hetzner",
        specs: {
            price: "€3.79/month",
            cpu: "2 vCPU",
            ram: "4 GB RAM",
            storage: "40 GB SSD",
            transfer: "20 TB transfer",
        },
        bonus: "€20 free credit for new accounts",
        startingPrice: "Plans from €3.79/month",
    },
    {
        name: "DigitalOcean",
        description: "Simple and reliable cloud infrastructure",
        logo: "/images/digitalocean-logo.svg",
        link: "https://r.ptah.sh/digitalocean",
        specs: {
            price: "$6/month",
            cpu: "1 vCPU",
            ram: "1 GB RAM",
            storage: "25 GB SSD",
            transfer: "1 TB transfer",
        },
        bonus: "$200 credit for 60 days",
        startingPrice: "Plans from $4/month",
    },
];

const steps = [
    {
        title: "Welcome to Ptah.sh!",
        content: "This guide will help you install an Agent on your server.",
    },
    {
        target: "#hosting-providers",
        title: "Need a server?",
        content:
            "At Ptah.sh, you bring your own servers. We recommend these providers.",
    },
    {
        target: "#agent-install-cli",
        title: "Install the Agent",
        content:
            "Once the server is ready, copy the script and paste it in your server's console.",
    },
];
</script>

<template>
    <Tour
        tour-id="agent-install"
        :steps="steps"
        auto-start
        finish-button-text="Ok"
    />

    <div class="w-full space-y-6">
        <!-- Hosting Providers Section -->
        <div id="hosting-providers">
            <h3
                class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4"
            >
                Need a server? Try these trusted providers:
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a
                    v-for="provider in providers"
                    :key="provider.name"
                    :href="provider.link"
                    target="_blank"
                    class="flex flex-col p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                >
                    <div class="flex items-center h-full">
                        <img
                            :src="provider.logo"
                            :alt="provider.name"
                            class="h-8 w-8 object-contain"
                        />
                        <div class="ml-4 flex flex-col h-full">
                            <h4
                                class="font-medium text-gray-900 dark:text-white"
                            >
                                {{ provider.name }}
                            </h4>
                            <p
                                class="text-sm text-gray-500 dark:text-gray-400 flex-grow"
                            >
                                {{ provider.description }}
                            </p>
                            <p
                                class="text-sm text-gray-600 dark:text-gray-300 font-medium"
                            >
                                {{ provider.startingPrice }}
                                <span
                                    class="text-xs text-gray-500 dark:text-gray-400 font-normal"
                                    >excl. VAT</span
                                >
                            </p>
                            <p
                                class="text-sm text-emerald-600 dark:text-emerald-400 font-medium mt-1"
                            >
                                <svg
                                    class="w-4 h-4 inline-block mr-1"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                                {{ provider.bonus }}
                            </p>
                        </div>
                    </div>
                    <div
                        class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700"
                    >
                        <p
                            class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >
                            Minimum recommended specs:
                        </p>
                        <ul class="grid grid-cols-2 gap-2">
                            <template
                                v-for="(value, key) in provider.specs"
                                :key="key"
                            >
                                <li
                                    :class="[
                                        'flex items-center text-sm text-gray-600 dark:text-gray-400',
                                        key === 'price'
                                            ? 'col-span-full font-semibold text-emerald-600 dark:text-emerald-500'
                                            : '',
                                    ]"
                                >
                                    <svg
                                        class="w-4 h-4 mr-1.5 text-green-500"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M5 13l4 4L19 7"
                                        ></path>
                                    </svg>

                                    <span>
                                        <span
                                            v-if="key === 'price'"
                                            class="text-xs text-gray-500 dark:text-gray-400 font-normal"
                                            >Starts at</span
                                        >
                                        {{ value }}
                                        <span
                                            v-if="key === 'price'"
                                            class="text-xs text-gray-500 dark:text-gray-400 font-normal"
                                            >(excl. VAT)</span
                                        >
                                    </span>
                                </li>
                            </template>
                        </ul>
                    </div>
                </a>
            </div>
        </div>

        <!-- Existing Installation Command Section -->
        <div>
            <InputLabel for="agent-install-cli">
                Execute this script in your server's console to install an Agent
            </InputLabel>
            <CopyToClipboard
                :value="
                    'export PTAH_TOKEN=' +
                    $props.node.agent_token +
                    ' && curl -sSL https://r.ptah.sh/install-agent > /tmp/install.sh && bash /tmp/install.sh'
                "
                id="agent-install-cli"
            >
                <template #icon>
                    <svg
                        class="w-4 h-4 text-gray-800 dark:text-white"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke="currentColor"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M8 17.345a4.76 4.76 0 0 0 2.558 1.618c2.274.589 4.512-.446 4.999-2.31.487-1.866-1.273-3.9-3.546-4.49-2.273-.59-4.034-2.623-3.547-4.488.486-1.865 2.724-2.899 4.998-2.31.982.236 1.87.793 2.538 1.592m-3.879 12.171V21m0-18v2.2"
                        />
                    </svg>
                </template>
            </CopyToClipboard>
        </div>
    </div>
</template>
