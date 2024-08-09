<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AgentInstall from "@/Pages/Nodes/Partials/AgentInstall.vue";
import FormSection from "@/Components/FormSection.vue";
import ValueCard from "@/Components/ValueCard.vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps(["node", "lastAgentVersion"]);

const upgradeAgentForm = useForm({
    targetVersion: props.lastAgentVersion,
});

function upgradeAgent() {
    upgradeAgentForm.post(
        route("nodes.upgrade-agent", {
            node: props.node.id,
        }),
        {
            preserveScroll: true,
        },
    );
}
</script>
<template>
    <FormSection>
        <template #title> Agent Status </template>

        <template #description>
            Once the Agent on your server is up and running, you can see the
            agent's version, available IP interfaces and upgrade the agent, if a
            new version is available.
        </template>

        <template #form>
            <div
                v-if="$props.node.online && $props.node.data"
                class="col-span-full"
            >
                <ValueCard
                    :value="$props.node.data.version"
                    label="Agent Version"
                />
                <template
                    v-for="network in $props.node.data.host.networks"
                    :key="network.if_name"
                >
                    <ValueCard
                        v-for="ip in network.ips"
                        :key="ip"
                        :value="ip.ip"
                        :label="network.if_name"
                    />
                </template>
                <ValueCard
                    :value="$props.node.data.docker.platform.name"
                    label="Docker Platform"
                />
            </div>
            <div v-else class="col-span-10">
                <AgentInstall :node="$props.node" />
            </div>
        </template>

        <template
            v-if="
                $props.node.online &&
                $props.lastAgentVersion !== $props.node.data.version
            "
            #submit
        >
            <a
                class="text-sm text-blue-700 hover:underline px-8"
                :href="
                    'https://github.com/ptah-sh/ptah-agent/compare/' +
                    $props.node.data.version +
                    '...' +
                    $props.lastAgentVersion
                "
                target="_blank"
                >Compare {{ $props.node.data.version }}...{{
                    $props.lastAgentVersion
                }}</a
            >
            <PrimaryButton type="button" @click="upgradeAgent"
                >Upgrade to {{ $props.lastAgentVersion }}</PrimaryButton
            >
        </template>
    </FormSection>
</template>
