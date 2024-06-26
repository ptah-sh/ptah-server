<script setup>
import ShowLayout from "@/Pages/Services/ShowLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {useForm} from "@inertiajs/vue3";
import DeploymentData from "@/Pages/Services/Partials/DeploymentData.vue";
import ServiceDetailsForm from "@/Pages/Services/Partials/ServiceDetailsForm.vue";
import FormSection from "@/Components/FormSection.vue";

const props = defineProps({
  service: Object
})

const serviceForm = useForm({
  id: props.service.id,
  name: props.service.name,
  swarm_id: props.service.swarm_id,
});

const updateService = () => {
  serviceForm.put(route('services.update', props.service.id));
}

const deploymentForm = useForm(props.service.latest_deployment.data);

const deploy = () => {
  deploymentForm.post(route('services.deploy', props.service.id));
}
</script>

<template>
  <ShowLayout :service="props.service">
    <FormSection @submit="updateService">
      <template #title>
        Service Details
      </template>

      <template #description>
        <p>You can update the name of the service. Currently it is not possible to move the service between swarms and/or teams.</p>
        <p>The service name change will affect only the UI and not the actual service on the Docker Swarm.</p>
      </template>

      <template #form>
        <ServiceDetailsForm v-model="serviceForm" :team="$props.service.team" :swarms="[$props.service.swarm]" />
      </template>

      <template #actions>
        <PrimaryButton :class="{ 'opacity-25': serviceForm.processing }" :disabled="serviceForm.processing">
          Save
        </PrimaryButton>
      </template>
    </FormSection>

    <form @submit.prevent="deploy">
      <DeploymentData v-model="deploymentForm" :errors="deploymentForm.errors" />

      <div class="flex justify-end">
        <PrimaryButton :class="{ 'opacity-25': deploymentForm.processing }" :disabled="deploymentForm.processing">
          Deploy Changes
        </PrimaryButton>
      </div>
    </form>
  </ShowLayout>
</template>