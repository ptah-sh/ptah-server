<script setup>
import ActionSection from "@/Components/ActionSection.vue";
import CopyToClipboard from "@/Components/CopyToClipboard.vue";
import ExternalLink from "@/Components/ExternalLink.vue";

defineProps({
    swarm: Object,
});
</script>

<template>
    <ActionSection>
        <template #title> Public SSH Key </template>

        <template #description>
            Use this SSH Key as a deploy key in your Git repository.

            <ExternalLink
                href="https://ptah.sh/concepts/workers/#git-based-deployments/"
            >
                Learn about Git based deployments
            </ExternalLink>
        </template>

        <template #content>
            <div class="col-span-full">
                <CopyToClipboard :value="swarm.data.publicSSHKey" />
            </div>
        </template>
    </ActionSection>
</template>
