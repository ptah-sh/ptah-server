<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import ActionSection from "@/Components/ActionSection.vue";
import { useForm } from "@inertiajs/vue3";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { reactive } from "vue";
import ServiceDetailsForm from "@/Pages/Services/Partials/ServiceDetailsForm.vue";
import DeploymentData from "@/Pages/Services/Partials/DeploymentData.vue";
import TemplatePicker from "@/Pages/Services/Partials/TemplatePicker.vue";
import { useCrypto } from "@/encryption";
import Tour from "@/Components/Tour.vue";

const props = defineProps({
    networks: Array,
    nodes: Array,
    deploymentData: Object,
    dockerRegistries: Array,
    s3Storages: Array,
    marketplaceUrl: String,
    blankProcess: Object,
    blankWorker: Object,
});

const { encryptDeploymentData } = useCrypto();

const form = useForm({
    name: "",
    deploymentData: props.deploymentData,
});

const createService = async () => {
    form.processing = true;

    const formData = form.data();

    const encryptedFormData = {
        ...formData,
        deploymentData: await encryptDeploymentData(formData.deploymentData),
    };

    form.transform(() => encryptedFormData).post(route("services.store"));
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

    const processes = template.deploymentData.processes.map((process) => {
        return {
            ...process,
            workers: process.workers.map((worker) => {
                const backupCreate =
                    worker.launchMode === "backup_create"
                        ? {
                              ...worker.backupCreate,
                              archive: {
                                  format:
                                      worker.backupCreate?.archive?.format ||
                                      "tar.gz",
                              },
                              s3StorageId: props.s3Storages[0]?.id,
                          }
                        : null;

                const crontab =
                    worker.launchMode === "backup_create" ? "0 0 * * *" : null;

                return {
                    crontab,
                    ...worker,
                    backupCreate,
                };
            }),
            placementNodeId:
                process.volumes.length > 0 && props.nodes.length === 1
                    ? props.nodes[0].id
                    : null,
        };
    });

    form.deploymentData.processes = processes;

    hideTemplatePicker();
};

const steps = [
    {
        target: "#use-template-button",
        title: "Use a Template",
        content:
            "Use a template to quickly start a database (PostgreSQL, MySQL, MongoDB, etc.) or web service (Wordpress, Plausible Analytics, etc.).",
        position: "bottom",
    },
];
</script>

<template>
    <Tour auto-start tour-id="use-template" :steps="steps" />

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
            <SecondaryButton
                id="use-template-button"
                @click="openTemplatePicker"
            >
                Use a Template
            </SecondaryButton>
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
                            Note that the service name can be modified at any
                            time, but this change will not affect the service
                            name within the Swarm cluster.
                        </p>
                    </template>

                    <template #content>
                        <ServiceDetailsForm
                            v-model="form"
                            :team="$page.props.auth.user.current_team"
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
                    :initial-secret-vars="[]"
                    :blank-process="props.blankProcess"
                    :blank-worker="props.blankWorker"
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
