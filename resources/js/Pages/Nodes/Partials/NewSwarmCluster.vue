<script setup>
import {router, useForm} from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import FormSection from "@/Components/FormSection.vue";

const form = useForm({
    swarm_option: 'init',
    swarm_init: {
        listen_addr: '',
        advertise_addr: '',
        force_new_cluster: false
    }
})
</script>

<template>
    <FormSection>
        <template #title>
            Swarm Cluster
        </template>

        <template #description>
            Select either to initialize a new cluster or join an existing cluster.
        </template>

        <template #form>
            <div v-auto-animate class="col-span-6">
                <ul class="items-center w-full text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg sm:flex dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <li class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r dark:border-gray-600">
                        <div class="flex items-center ps-3">
                            <input id="swarm-init-option" v-model="form.swarm_option" type="radio" value="init" name="list-radio" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                            <label for="swarm-init-option" class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Initialize Cluster</label>
                        </div>
                    </li>
                    <li class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r dark:border-gray-600">
                        <div class="flex items-center ps-3">
                            <input id="swarm-join-option" v-model="form.swarm_option" type="radio" value="join" name="list-radio" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                            <label for="swarm-join-option" class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Join an Existing Cluster</label>
                        </div>
                    </li>
                </ul>

                <div v-if="form.swarm_option === 'init'">
                    Select IP
                </div>

                <div v-if="form.swarm_option === 'join'">
                    Unfortunately, this feature is not implemented yet.
                </div>
            </div>
        </template>

        <template #actions>
            <PrimaryButton type="submit">Initialize Node</PrimaryButton>
        </template>
    </FormSection>
</template>
