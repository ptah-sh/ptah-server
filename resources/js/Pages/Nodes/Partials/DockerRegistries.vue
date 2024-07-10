<script setup>
import FormSection from "@/Components/FormSection.vue";
import AddComponentButton from "@/Components/Service/AddComponentButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {useForm} from "@inertiajs/vue3";
import FormField from "@/Components/FormField.vue";
import TextInput from "@/Components/TextInput.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import {FwbCheckbox, FwbToggle} from "flowbite-vue";
import InputLabel from "@/Components/InputLabel.vue";

const props = defineProps({
  swarm: Object,
  taskGroup: Object,
})

const form = useForm({
  'registries': props.swarm.data.registries,
});

const makeId = (prefix) =>  prefix + '-' + Math.random().toString(36).slice(2);

const addRegistry = () => {
  form.registries.push({ id: makeId('registry') });
}

const submitForm = () => {
  form.post(route('swarms.update-docker-registries', {
    'swarm': props.swarm.id,
  }), {
    preserveScroll: true,
  });
}
</script>

<template>
  <FormSection @submit="submitForm" :task-group="taskGroup">
    <template #title>
      Docker Registries
    </template>

    <template #description>
      Configure your custom Docker Registries to be able to pull images not only from the Docker Hub.
    </template>

    <template #form>
      <div
          v-if="form.registries.length === 0"
          class="col-span-full"
      >
        There are no custom registries yet.
      </div>
      <template v-for="(registry, index) in form.registries" :key="registry.id" class="">
        <hr v-if="index !== 0" class="col-span-full" />

        <input type="hidden" v-model="registry.dockerName" />

        <FormField :error="form.errors['registries.' + index + '.name']" class="col-span-2">
          <template #label>
            Name
          </template>

          <TextInput v-model="registry.name" class="w-full" placeholder="gitlab" />
        </FormField>

        <FormField :error="form.errors['registries.' + index + '.serverAddress']" class="col-span-4">
          <template #label>
            Server Address
          </template>

          <div class="flex gap-2">
            <TextInput v-model="registry.serverAddress" class="w-full" placeholder="registry.gitlab.com" />
            <SecondaryButton @click="form.registries.splice(index, 1)" tabindex="-1">
              <svg class="w-4 h-4 text-gray-800 dark:text-white" aria-hidden="true"
                   xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5 12h14"/>
              </svg>
          </SecondaryButton>
      </div>
        </FormField>

        <FormField :error="form.errors['registries.' + index + '.username']" class="col-span-2">
          <template #label>
            Username
          </template>

          <TextInput v-model="registry.username" type="password" class="w-full" :placeholder="registry.dockerName ? 'keep username' : 'glpat-*******'" />
        </FormField>

        <FormField :error="form.errors['registries.' + index + '.password']" class="col-span-2">
          <template #label>
            Password
          </template>

          <TextInput v-model="registry.password" type="password" class="w-full" :placeholder="registry.dockerName ? 'keep password' : 'glpat-*******'" />
        </FormField>
      </template>
    </template>

    <template #actions>
      <AddComponentButton @click="addRegistry">
        Registry
      </AddComponentButton>
    </template>

    <template #submit>
      <PrimaryButton>Save</PrimaryButton>
    </template>
  </FormSection>
</template>
