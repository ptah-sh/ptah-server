<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import TextInput from "@/Components/TextInput.vue";
import ActionSection from "@/Components/ActionSection.vue";
import { useForm } from "@inertiajs/vue3";
import FormField from "@/Components/FormField.vue";
import Select from "@/Components/Select.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextArea from "@/Components/TextArea.vue";
import { computed, effect, reactive } from "vue";
import ServiceDetailsForm from "@/Pages/Services/Partials/ServiceDetailsForm.vue";
import DeploymentData from "@/Pages/Services/Partials/DeploymentData.vue";
import TemplatePicker from "@/Pages/Services/Partials/TemplatePicker.vue";

const props = defineProps({
    swarms: Array,
    networks: Array,
    nodes: Array,
    deploymentData: Object,
    dockerRegistries: Array,
    s3Storages: Array,
    marketplaceUrl: String,
});

const form = useForm({
    name: "",
    swarm_id: props.swarms[0]?.id,
    deploymentData: props.deploymentData,
});

const createService = () => {
    form.post(route("services.store"));
};

const showTemplatePicker = reactive({
    show: false,
});

const openTemplatePicker = () => {
    showTemplatePicker.show = true;
};

const hideTemplatePicker = () => {
    showTemplatePicker.show = false;
};

const applyTemplate = (template) => {
    form.name = template.name;

    form.deploymentData.processes = template.deploymentData.processes;

    hideTemplatePicker();
};
</script>

<template>
    <AppLayout title="Dashboard">
        <TemplatePicker
            :marketplace-url="marketplaceUrl"
            :show="showTemplatePicker.show"
            @apply="applyTemplate"
            @close="hideTemplatePicker"
        />

        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Create Service
            </h2>
        </template>

        <template #actions>
            <SecondaryButton @click="openTemplatePicker"
                >Use a Template</SecondaryButton
            >
        </template>

        <div class="py-12">
            <form
                class="max-w-7xl mx-auto sm:px-6 lg:px-8"
                @submit.prevent="createService"
            >
                <ActionSection>
                    <template #title> Service Details </template>

                    <template #description>
                        <p>
                            Select a swarm where the service will be deployed
                            to.
                        </p>
                        <p>
                            The name of the service can be changed at any time.
                            Be aware, that the service name in the Swarm cluster
                            will not be changed.
                        </p>

                        <p>
                            You can isolate containers from each other by using
                            different networks.
                        </p>
                        <p>
                            Service will be accessible by the internal domain
                            name to other containers on the same network.
                        </p>
                        <p>
                            You can make certain ports of the containers
                            accessible from the host network. Please set up the
                            firewall rule on your host to prevent unwanted
                            access.
                        </p>
                        <p>
                            If you don't select the Placement Node, the
                            containers will be able to run on any node of the
                            Swarm Cluster.
                        </p>
                    </template>

                    <template #content>
                        <ServiceDetailsForm
                            v-model="form"
                            :team="$page.props.auth.user.current_team"
                            :swarms="swarms"
                        />
                    </template>
                </ActionSection>

                <DeploymentData
                    v-model="form.deploymentData"
                    :networks="networks"
                    :nodes="nodes"
                    :errors="form.errors"
                    :service-name="form.name"
                    :docker-registries="props.dockerRegistries"
                    :s3-storages="props.s3Storages"
                />

                <div class="flex justify-end">
                    <PrimaryButton
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Create Service
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
