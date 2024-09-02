<script setup>
import { onMounted, ref, reactive } from "vue";
import { CopyClipboard } from "flowbite";
import InputLabel from "@/Components/InputLabel.vue";

defineProps({
    node: Object,
});

const trigger = ref(null);
const copyToClipboardRef = ref(null);

const showSuccessMessage = ref(false);

onMounted(() => {
    const clipboard = new CopyClipboard(
        trigger.value,
        copyToClipboardRef.value,
    );

    clipboard.updateOnCopyCallback((clipboard) => {
        showSuccess();

        // reset to default state
        setTimeout(() => {
            resetToDefault();
        }, 2000);
    });

    const showSuccess = () => {
        showSuccessMessage.value = true;
    };

    const resetToDefault = () => {
        showSuccessMessage.value = false;
    };
});
</script>

<template>
    <div class="w-full">
        <InputLabel for="agent-install-cli"
            >Execute this script in your server's console to install an
            Agent</InputLabel
        >
        <div class="relative">
            <div class="relative">
                <div
                    class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none"
                >
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
                </div>
                <!-- TODO: correct script -->
                <!--        <input id="agent-install-cli" ref="copyToClipboardRef" type="text"-->
                <!--               class="ps-10 p-2.5  col-span-6 bg-gray-50 border border-gray-300 text-gray-500 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-2.5 py-4 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"-->
                <!--               value="script cli goes here" disabled readonly>-->
                <input
                    id="agent-install-cli"
                    ref="copyToClipboardRef"
                    type="text"
                    class="ps-10 p-2.5 col-span-6 bg-gray-50 border border-gray-300 text-gray-500 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-2.5 py-4 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    :value="
                        'export PTAH_TOKEN=' +
                        $props.node.agent_token +
                        ' && curl -sSL https://r.ptah.sh/install-agent | bash'
                    "
                    disabled
                    readonly
                />
            </div>
            <button
                ref="trigger"
                class="absolute end-2.5 w-24 top-1/2 -translate-y-1/2 text-gray-900 dark:text-gray-400 hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-600 dark:hover:bg-gray-700 rounded-lg py-2 px-2.5 inline-flex items-center justify-center bg-white border-gray-200 border"
            >
                <span v-auto-animate>
                    <span
                        v-if="!showSuccessMessage"
                        id="default-message"
                        class="inline-flex items-center"
                    >
                        <svg
                            class="w-3 h-3 me-1.5"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor"
                            viewBox="0 0 18 20"
                        >
                            <path
                                d="M16 1h-3.278A1.992 1.992 0 0 0 11 0H7a1.993 1.993 0 0 0-1.722 1H2a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2Zm-3 14H5a1 1 0 0 1 0-2h8a1 1 0 0 1 0 2Zm0-4H5a1 1 0 0 1 0-2h8a1 1 0 1 1 0 2Zm0-5H5a1 1 0 0 1 0-2h2V2h4v2h2a1 1 0 1 1 0 2Z"
                            />
                        </svg>
                        <span class="text-xs font-semibold">Copy</span>
                    </span>

                    <span
                        v-if="showSuccessMessage"
                        id="success-message"
                        class="inline-flex whitespace-nowrap items-center"
                    >
                        <svg
                            class="w-3 h-3 text-blue-700 dark:text-blue-500 me-1.5"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 16 12"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M1 5.917 5.724 10.5 15 1.5"
                            />
                        </svg>
                        <span
                            class="text-xs font-semibold text-blue-700 dark:text-blue-500"
                            >Copied</span
                        >
                    </span>
                </span>
            </button>
        </div>
    </div>
</template>
