<script setup>
import ExternalLink from "./ExternalLink.vue";
import { computed } from "vue";

const props = defineProps({
    url: String,
});

const domain = computed(() => {
    try {
        const url = new URL(props.url);
        return url.hostname;
    } catch {
        return "";
    }
});

const icon = computed(() => {
    const hostname = domain.value.toLowerCase();

    if (hostname.includes("github")) {
        return "github";
    }
    if (hostname.includes("gitlab")) {
        return "gitlab";
    }
    if (hostname.includes("bitbucket")) {
        return "bitbucket";
    }

    return "git";
});
</script>

<template>
    <ExternalLink :href="url" class="inline-flex items-center gap-2">
        <svg
            v-if="icon === 'github'"
            class="w-4 h-4"
            viewBox="0 0 24 24"
            fill="currentColor"
        >
            <path
                d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"
            />
        </svg>

        <svg
            v-else-if="icon === 'gitlab'"
            class="w-4 h-4"
            viewBox="0 0 24 24"
            fill="currentColor"
        >
            <path
                d="M23.955 13.587l-1.342-4.135-2.664-8.189a.455.455 0 00-.867 0L16.418 9.45H7.582L4.918 1.263a.455.455 0 00-.867 0L1.386 9.45.044 13.587a.924.924 0 00.331 1.03L12 23.054l11.625-8.436a.92.92 0 00.33-1.031"
            />
        </svg>

        <svg
            v-else-if="icon === 'bitbucket'"
            class="w-4 h-4"
            viewBox="0 0 24 24"
            fill="currentColor"
        >
            <path
                d="M.778 1.213a.768.768 0 00-.768.892l3.263 19.81c.084.5.515.868 1.022.873H19.95a.772.772 0 00.77-.646l3.27-20.03a.768.768 0 00-.768-.891L.778 1.213zM14.52 15.53H9.522L8.17 8.466h7.561l-1.211 7.064z"
            />
        </svg>

        <svg v-else class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
            <path
                d="M2.6,10.59L8.38,4.8l1.69,1.7c-0.24,0.85,0.15,1.78,0.93,2.23v5.54c-0.6,0.34-1,0.99-1,1.73c0,1.1,0.9,2,2,2c1.1,0,2-0.9,2-2 c0-0.74-0.4-1.39-1-1.73V9.41l2.07,2.09c-0.07,0.15-0.07,0.32-0.07,0.5c0,1.1,0.9,2,2,2c1.1,0,2-0.9,2-2c0-1.1-0.9-2-2-2 c-0.18,0-0.35,0-0.5,0.07L13.93,7.5C14.23,6.97,14.23,6.27,13.93,5.74l1.69-1.69L21.41,10.59c0.78,0.78,0.78,2.05,0,2.83 l-6.83,6.83c-0.78,0.78-2.05,0.78-2.83,0l-9.15-9.15C1.82,12.64,1.82,11.37,2.6,10.59z"
            />
        </svg>

        {{ domain }}
    </ExternalLink>
</template>
