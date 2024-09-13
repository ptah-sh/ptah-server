<script setup>
import { ref, watchEffect, computed } from "vue";
import { usePage, Link } from "@inertiajs/vue3";

const page = usePage();
const show = ref(true);
const style = ref("success");
const message = ref("");
const link = ref(null);

watchEffect(() => {
    // Check flash store first
    const flashBanner = page.props.jetstream.flash?.banner;
    const flashStyle = page.props.jetstream.flash?.bannerStyle;

    // Then check page error
    const pageErrors = page.props.errors;

    link.value = null;
    if (flashBanner) {
        message.value = flashBanner;
        style.value = flashStyle || "success";
    } else if ("quota" in pageErrors) {
        // Get the first error message
        message.value = pageErrors.quota;
        style.value = "danger";
        link.value = {
            text: "View Quotas",
            href: route("teams.billing.quotas", {
                team: page.props.auth.user.current_team_id,
            }),
        };
    } else {
        // Apply defaults
        message.value = "";
        style.value = "success";
    }

    show.value = !!message.value;
});
</script>

<template>
    <div>
        <div
            v-if="show && message"
            :class="{
                'bg-indigo-500': style == 'success',
                'bg-yellow-500': style == 'warning',
                'bg-red-700': style == 'danger',
            }"
        >
            <div class="max-w-screen-xl mx-auto py-2 px-3 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between flex-wrap">
                    <div class="w-0 flex-1 flex items-center min-w-0">
                        <span
                            class="flex p-2 rounded-lg"
                            :class="{
                                'bg-indigo-600': style == 'success',
                                'bg-yellow-600': style == 'warning',
                                'bg-red-600': style == 'danger',
                            }"
                        >
                            <svg
                                v-if="style == 'success'"
                                class="h-5 w-5 text-white"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>

                            <svg
                                v-if="style == 'warning'"
                                class="h-5 w-5 text-white"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"
                                />
                            </svg>

                            <svg
                                v-if="style == 'danger'"
                                class="h-5 w-5 text-white"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"
                                />
                            </svg>
                        </span>

                        <div class="ms-3 font-medium text-sm text-white">
                            <span class="truncate">{{ message }}</span>
                            <Link
                                v-if="link"
                                :href="link.href"
                                class="underline ml-2"
                            >
                                {{ link.text }}
                            </Link>
                        </div>
                    </div>

                    <div class="shrink-0 sm:ms-3">
                        <button
                            type="button"
                            class="-me-1 flex p-2 rounded-md focus:outline-none sm:-me-2 transition"
                            :class="{
                                'hover:bg-indigo-600 focus:bg-indigo-600':
                                    style == 'success',
                                'hover:bg-yellow-600 focus:bg-yellow-600':
                                    style == 'warning',
                                'hover:bg-red-600 focus:bg-red-600':
                                    style == 'danger',
                            }"
                            aria-label="Dismiss"
                            @click.prevent="show = false"
                        >
                            <svg
                                class="h-5 w-5 text-white"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
