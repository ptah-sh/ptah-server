<script setup>
import ShowLayout from "@/Pages/Services/ShowLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { useForm } from "@inertiajs/vue3";
import DeploymentData from "@/Pages/Services/Partials/DeploymentData.vue";
import ServiceDetailsForm from "@/Pages/Services/Partials/ServiceDetailsForm.vue";
import FormSection from "@/Components/FormSection.vue";
import SectionBorder from "@/Components/SectionBorder.vue";
import DeleteResourceSection from "@/Components/DeleteResourceSection.vue";
import { useCrypto } from "@/encryption";
import { computed } from "vue";

const props = defineProps({
    service: Object,
    dockerRegistries: Array,
    s3Storages: Array,
});

const { encryptDeploymentData } = useCrypto();

const serviceForm = useForm({
    id: props.service.id,
    name: props.service.name,
});

const updateService = () => {
    serviceForm.put(route("services.update", props.service));
};

const deploymentForm = useForm({
    ...props.service.latest_deployment.data,
    processes: props.service.latest_deployment.data.processes.map((process) => {
        return {
            ...process,
            secretFiles: process.secretFiles.map((secretFile) => {
                return {
                    ...secretFile,
                    content: "",
                };
            }),
            secretVars: process.secretVars.map((secretVar) => {
                return {
                    ...secretVar,
                    value: "",
                };
            }),
        };
    }),
});

const initialSecretVars = computed(() => {
    return props.service.latest_deployment.data.processes
        .flatMap((process) => process.secretVars)
        .map((secretVar) => secretVar.name);
});

const deploy = async () => {
    deploymentForm.processing = true;

    const formData = deploymentForm.data();

    const encryptedDeploymentData = await encryptDeploymentData(formData);

    deploymentForm
        .transform(() => encryptedDeploymentData)
        .post(route("services.deploy", props.service));
};

const deletionForm = useForm({
    serviceName: "",
});

const destroyService = () => {
    return deletionForm.delete(route("services.destroy", props.service));
};
</script>

<template>
    <ShowLayout :service="props.service">
        <FormSection @submit="updateService">
            <template #title> Service Details </template>

            <template #description>
                <p>
                    You can update the name of the service. Currently it is not
                    possible to move the service between swarms and/or teams.
                </p>
                <p>
                    The service name change will affect only the UI and not the
                    actual service on the Docker Swarm.
                </p>
            </template>

            <template #form>
                <ServiceDetailsForm
                    v-model="serviceForm"
                    :team="props.service.team"
                    :service="props.service"
                />
            </template>

            <template #submit>
                <PrimaryButton
                    :class="{ 'opacity-25': serviceForm.processing }"
                    :disabled="serviceForm.processing"
                >
                    Save
                </PrimaryButton>
            </template>
        </FormSection>

        <form @submit.prevent="deploy">
            <DeploymentData
                v-model="deploymentForm"
                :errors="deploymentForm.errors"
                :docker-registries="props.dockerRegistries"
                :s3-storages="props.s3Storages"
                :initial-secret-vars="initialSecretVars"
            />

            <div class="flex justify-end">
                <PrimaryButton
                    :class="{ 'opacity-25': deploymentForm.processing }"
                    :disabled="deploymentForm.processing"
                >
                    Deploy Changes
                </PrimaryButton>
            </div>
        </form>

        <SectionBorder />

        <DeleteResourceSection
            resource-kind="Service"
            :resource-name="props.service.name"
            :destroy="destroyService"
        >
        </DeleteResourceSection>
    </ShowLayout>
</template>
