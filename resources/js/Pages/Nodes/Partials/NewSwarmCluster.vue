<script setup>
import {defineProps, reactive} from "vue";
import {router, useForm} from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import FormSection from "@/Components/FormSection.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import Dropdown from "@/Components/Dropdown.vue";
import {FwbCheckbox, FwbSelect} from "flowbite-vue";
import Select from "@/Components/Select.vue";
import FormField from "@/Components/FormField.vue";

const props = defineProps({
  node: Object
});

const swarmOption = reactive({
  form: 'init',
});

const initForm = useForm({
  node_id: props.node.id,
  name: '',
        advertise_addr: '',
        force_new_cluster: false
})

const submit = () => {
  // TODO: if option is 'init' -> initCluster()
  // TODO: if option is 'join' -> joinCluster()
  initForm.post(route('swarm-tasks.init-cluster'), {
    preserveScroll: 'errors'
  });
}
</script>

<template>
    <FormSection @submitted="submit">
        <template #title>
            Swarm Cluster
        </template>

        <template #description>
            Select either to initialize a new cluster or join an existing cluster.
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <ul class="items-center w-full text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg sm:flex dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <li class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r dark:border-gray-600">
                        <div class="flex items-center ps-3">
                            <input id="swarm-init-option" v-model="swarmOption.form" type="radio" value="init" name="list-radio" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                            <label for="swarm-init-option" class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Initialize Cluster</label>
                        </div>
                    </li>
                    <li class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r dark:border-gray-600">
                        <div class="flex items-center ps-3">
                            <input id="swarm-join-option" v-model="swarmOption.form" type="radio" value="join" name="list-radio" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                            <label for="swarm-join-option" class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Join an Existing Cluster</label>
                        </div>
                    </li>
                </ul>


            </div>

          <div v-auto-animate class="col-span-6 sm:col-span-4">
          <template v-if="swarmOption.form === 'init'">
            <template v-if="$props.node.data.host.networks.length > 0" >
              <FormField :error="initForm.errors.name">
                <template #label>Swarm Name</template>

                <TextInput v-model="initForm.name" class="block w-full" />
              </FormField>
              <FormField :error="initForm.errors.advertise_addr">
                <template #label>Advertisement Address</template>
                <Select id="advertise_addr" v-model="initForm.advertise_addr">
                  <option>Select Advertise Address</option>
                  <optgroup v-for="network in $props.node.data.host.networks" :label="network.if_name">
                    <option v-for="ip in network.ips" :value="ip.ip">{{ ip.ip }}</option>
                  </optgroup>
                </Select>
              </FormField>

              <FormField :error="initForm.errors.force_new_cluster">
                <!-- TODO: add warning that the force new cluster will wipe out your existing cluster (services, data?) -->
                <FwbCheckbox label="Force New Cluster" v-model="initForm.force_new_cluster" />
              </FormField>
              <InputError v-if="initForm.force_new_cluster" message="Be aware that you will lose your data!" />
            </template>
            <template v-else>
              No IP found
            </template>
          </template>

          <div v-if="swarmOption.form === 'join'">
            Unfortunately, this feature is not implemented yet.
          </div>
          </div>
        </template>


        <template #actions>
            <PrimaryButton type="submit">Initialize Node</PrimaryButton>
        </template>
    </FormSection>
</template>
