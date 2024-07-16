<script setup>
import FormSection from "@/Components/FormSection.vue";
import AddComponentButton from "@/Components/Service/AddComponentButton.vue";
import FormField from "@/Components/FormField.vue";
import ComponentBlock from "@/Components/Service/ComponentBlock.vue";
import {useForm} from "@inertiajs/vue3";
import TextInput from "@/Components/TextInput.vue";
import {makeId} from "@/id.js";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
  'swarm': Object,
  'taskGroup': Object,
})

const form = useForm({
  's3Storages': props.swarm.data.s3Storages,
});

const addStorage = () => {
  form.s3Storages.push({
    id: makeId('storage'),
  });
}

const submitForm = () => {
  form.post(route('swarms.update-s3-storages', {
    swarm: props.swarm.id,
  }), {
    preserveScroll: true
  });
}
</script>

<template>
  <FormSection @submit="submitForm">
    <template #title>
      S3 Storages
    </template>

    <template #description>
      You can manage your S3 Storages here. They will be used to upload and download backups.
    </template>

    <template #form>
      <p v-if="form.s3Storages.length === 0" class="col-span-full">No S3 Storages configured yet.</p>
      <ComponentBlock v-else v-model="form.s3Storages" v-slot="{ item, $index }" @remove="form.s3Storages.splice($event, 1)">
        <FormField class="col-span-2" :error="form.errors[`s3Storages.${$index}.name`]">
          <template #label>
            Name
          </template>

          <TextInput v-model="item.name" class="w-full" placeholder="digitalocean - backups" />
        </FormField>

        <FormField class="col-span-4" :error="form.errors[`s3Storages.${$index}.endpoint`]">
          <template #label>
            Endpoint
          </template>

          <TextInput v-model="item.endpoint" class="w-full" placeholder="nyc3.digitaloceanspaces.com" />
        </FormField>

        <FormField class="col-span-2" :error="form.errors[`s3Storages.${$index}.region`]">
          <template #label>
            Region
          </template>

          <TextInput v-model="item.region" class="w-full" placeholder="nyc3" />
        </FormField>

        <FormField class="col-span-2" :error="form.errors[`s3Storages.${$index}.bucket`]">
          <template #label>
            Bucket
          </template>

          <TextInput v-model="item.bucket" class="w-full" placeholder="backups" />
        </FormField>

        <FormField class="col-span-2" :error="form.errors[`s3Storages.${$index}.pathPrefix`]">
          <template #label>
            Path Prefix
          </template>

          <TextInput v-model="item.pathPrefix" class="w-full" placeholder="/backups" />
        </FormField>

        <FormField class="col-start-1 col-span-2" :error="form.errors[`s3Storages.${$index}.accessKey`]">
          <template #label>
            Access Key ID
          </template>

          <TextInput v-model="item.accessKey" type="password" class="w-full" placeholder="**********" />
        </FormField>

        <FormField class="col-span-2" :error="form.errors[`s3Storages.${$index}.secretKey`]">
          <template #label>
            Secret Access Key
          </template>

          <TextInput v-model="item.secretKey" type="password" class="w-full" placeholder="**********" />
        </FormField>
      </ComponentBlock>
    </template>

    <template #actions>
      <AddComponentButton @click="addStorage">Storage</AddComponentButton>
    </template>

    <template #submit>
      <PrimaryButton>Save</PrimaryButton>
    </template>
  </FormSection>
</template>