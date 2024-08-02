<script setup>
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <div class="flex flex-col items-center">
        <a
            href="https://x.com/b_shulha"
            class="flex gap-2 text-sm"
            target="_blank"
        >
            <svg
                class="w-6 h-6 text-gray-800 dark:text-white"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                fill="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    fill-rule="evenodd"
                    d="M22 5.892a8.178 8.178 0 0 1-2.355.635 4.074 4.074 0 0 0 1.8-2.235 8.343 8.343 0 0 1-2.605.981A4.13 4.13 0 0 0 15.85 4a4.068 4.068 0 0 0-4.1 4.038c0 .31.035.618.105.919A11.705 11.705 0 0 1 3.4 4.734a4.006 4.006 0 0 0 1.268 5.392 4.165 4.165 0 0 1-1.859-.5v.05A4.057 4.057 0 0 0 6.1 13.635a4.192 4.192 0 0 1-1.856.07 4.108 4.108 0 0 0 3.831 2.807A8.36 8.36 0 0 1 2 18.184 11.732 11.732 0 0 0 8.291 20 11.502 11.502 0 0 0 19.964 8.5c0-.177 0-.349-.012-.523A8.143 8.143 0 0 0 22 5.892Z"
                    clip-rule="evenodd"
                />
            </svg>

            <span class="underline hover:text-blue-500">@b_shulha</span>
        </a>

        <div
            class="w-full flex flex-col gap-4 items-center px-6 py-4 text-xs text-gray-500"
        >
            <div>
                <a
                    href="https://ptah.sh/"
                    class="underline hover:text-blue-500"
                    target="_blank"
                    >Ptah.sh</a
                >
                &copy; {{ new Date().getFullYear() }}
            </div>
            <div class="flex gap-4">
                <div>
                    <div>Operated by Bohdan Shulha, Private Entrepreneur</div>
                    <div>00-833, Warsaw, ul. Sienna 75, lok. 8.51</div>
                </div>
                <div>
                    <div>Tax Identification Number (NIP): 5223265838</div>
                    <div>
                        Contact Email:
                        <a
                            href="mailto:contact@ptah.sh"
                            class="underline hover:text-blue-500"
                            >contact@ptah.sh</a
                        >
                    </div>
                </div>
            </div>
        </div>

        <div class="flex gap-8 text-xs">
            <Link
                :href="route('terms.show')"
                class="underline hover:text-blue-500"
                >Terms of Service</Link
            >
            <Link
                :href="route('policy.show')"
                class="underline hover:text-blue-500"
                >Privacy Policy</Link
            >
            <Link
                :href="route('refund-policy.show')"
                class="underline hover:text-blue-500"
                >Refund Policy</Link
            >
        </div>
    </div>
</template>
