<script setup>
import DangerButton from "@/Components/DangerButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import InputError from "@/Components/InputError.vue";
import DialogModal from "@/Components/DialogModal.vue";
import ActionSection from "@/Components/ActionSection.vue";
import TextInput from "@/Components/TextInput.vue";
import {computed, effect, reactive} from "vue";

const props = defineProps({
  actionName: String,
  resourceKind: String,
  resourceName: String,
  error: String,
  destroy: Function,
});

const actionName = computed(() => props.actionName ? props.actionName : 'Destroy');

const modalControl = reactive({
  open: false,
});

const openModal = () => {
  confirmation.enteredName = '';
  confirmation.error = '';
  modalControl.open = true;
}

const closeModal = () => {
  modalControl.open = false;
}

const confirmation = reactive({
  enteredName: '',
  error: props.error,
  processing: false,
});

effect(() => {
  confirmation.error = props.error;
})

const destroyResource = async () => {
  if (confirmation.enteredName !== props.resourceName) {
    confirmation.error = "The entered resource name doesn't match";

    return;
  }

  confirmation.error = '';
  confirmation.processing = true;

  try {
    const res = await props.destroy();
    console.log(res);
  } catch (err) {
    console.warn(err);
  } finally {
    confirmation.processing = false;
  }
}
</script>

<template>
  <ActionSection>
    <template #title>
      {{actionName}} the {{props.resourceKind}}
    </template>

    <template #description>
      Permanently {{actionName}} the {{props.resourceKind}}. Please create backups of your data.
    </template>

    <template #content>
      <div class="max-w-xl text-sm text-gray-600 dark:text-gray-400 col-span-full grid grid-cols-6 gap-4">
        <div class="col-span-full">
          We will {{actionName}} {{props.resourceKind}}. You will not be able to use it again. The related resources, configs and secrets will be kept on your Docker Swarm cluster.
        </div>

        <div v-if="$slots.default" class="col-span-full">
          <slot />
        </div>

        <div class="col-span-full">
          <DangerButton @click="openModal">
            {{actionName}} {{props.resourceKind}}
          </DangerButton>
        </div>

        <DialogModal :show="modalControl.open" @close="closeModal">
          <template #title>
            {{actionName}} {{props.resourceKind}}
          </template>

          <template #content>
            Are you sure you want to {{actionName}} <b><code>{{ props.resourceName }}</code></b>? Once we {{actionName}} {{resourceName}}, all of its related resources and data will be permanently deleted. Please enter the name of the {{resourceKind}} to confirm you would like to permanently delete it.

            <div class="mt-4" v-auto-animate>
              <TextInput
                  ref="serviceDeleteInput"
                  v-model="confirmation.enteredName"
                  class="mt-1 block w-3/4"
                  :placeholder="props.resourceName"
                  @keyup.enter="destroyResource"
              />

              <InputError :message="confirmation.error" class="mt-2" />
            </div>
          </template>

          <template #footer>
            <SecondaryButton @click="closeModal">
              Cancel
            </SecondaryButton>

            <DangerButton
                class="ms-3"
                :class="{ 'opacity-25': confirmation.processing }"
                :disabled="confirmation.processing"
                @click="destroyResource"
            >
              {{actionName}} {{props.resourceKind}}
            </DangerButton>
          </template>
        </DialogModal>
      </div>
    </template>
  </ActionSection>
</template>
