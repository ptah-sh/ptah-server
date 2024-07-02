<script setup>
import ShowLayout from "@/Pages/Services/ShowLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {useForm} from "@inertiajs/vue3";
import DeploymentData from "@/Pages/Services/Partials/DeploymentData.vue";
import ServiceDetailsForm from "@/Pages/Services/Partials/ServiceDetailsForm.vue";
import FormSection from "@/Components/FormSection.vue";
import SectionBorder from "@/Components/SectionBorder.vue";
import DangerButton from "@/Components/DangerButton.vue";
import TextInput from "@/Components/TextInput.vue";
import DialogModal from "@/Components/DialogModal.vue";
import InputError from "@/Components/InputError.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import {nextTick, reactive, ref} from "vue";
import ActionSection from "@/Components/ActionSection.vue";

const props = defineProps({
  service: Object,
  dockerRegistries: Array,
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

const serviceDeletion = reactive({
  open: false,
});

const deletionForm = useForm({
  serviceName: '',
});

const deleteService = () => {
  if (deletionForm.serviceName !== props.service.name) {
    deletionForm.setError("serviceName", 'Invalid service name.');
  } else {
    deletionForm.delete(route('services.destroy', props.service.id));
  }
}

const serviceDeleteInput = ref(null);

const confirmServiceDeletion = () => {
  deletionForm.serviceName = '';
  deletionForm.setError('serviceName', null);

  serviceDeletion.open = true;

  nextTick(() => {
    if (serviceDeleteInput.value) {
      serviceDeleteInput.value.focus();
    }
  });
}

const closeDeletionModal = () => {
  serviceDeletion.open = false
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
      <DeploymentData v-model="deploymentForm" :errors="deploymentForm.errors" :docker-registries="$props.dockerRegistries" />

      <div class="flex justify-end">
        <PrimaryButton :class="{ 'opacity-25': deploymentForm.processing }" :disabled="deploymentForm.processing">
          Deploy Changes
        </PrimaryButton>
      </div>
    </form>

    <SectionBorder />

    <ActionSection>
      <template #title>
        Destroy the Service
      </template>

      <template #description>
        Permanently destroy the service. Please create backups of your data.
      </template>

      <template #content>
        <div class="max-w-xl text-sm text-gray-600 dark:text-gray-400 col-span-6">
          The service will be destroyed. You will not be able to re-deploy it. The related configs and secrets will be kept on your Docker Swarm cluster.

          <div class="mt-5">
            <DangerButton @click="confirmServiceDeletion">
              Destroy Service
            </DangerButton>
          </div>

          <DialogModal :show="serviceDeletion.open" @close="closeDeletionModal">
            <template #title>
              Delete Service
            </template>

            <template #content>
              Are you sure you want to delete <b><code>{{ service.name }}</code></b>? Once the service is deleted, all of its resources and data will be permanently deleted. Please enter the service name password to confirm you would like to permanently delete it.

              <div class="mt-4" v-auto-animate>
                <TextInput
                    ref="serviceDeleteInput"
                    v-model="deletionForm.serviceName"
                    class="mt-1 block w-3/4"
                    :placeholder="service.name"
                    @keyup.enter="deleteService"
                />

                <InputError :message="deletionForm.errors.serviceName" class="mt-2" />
              </div>
            </template>

            <template #footer>
              <SecondaryButton @click="closeDeletionModal">
                Cancel
              </SecondaryButton>

              <DangerButton
                  class="ms-3"
                  :class="{ 'opacity-25': deletionForm.processing }"
                  :disabled="deletionForm.processing"
                  @click="deleteService"
              >
                Delete Service
              </DangerButton>
            </template>
          </DialogModal>
        </div>
      </template>
    </ActionSection>
  </ShowLayout>
</template>