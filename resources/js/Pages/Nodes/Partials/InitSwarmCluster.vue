<script setup>
import { defineProps } from "vue";
import { useForm } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import FormSection from "@/Components/FormSection.vue";
import InputError from "@/Components/InputError.vue";
import { FwbCheckbox } from "flowbite-vue";
import Select from "@/Components/Select.vue";
import FormField from "@/Components/FormField.vue";

const props = defineProps({
    node: Object,
});

const initForm = useForm({
    node_id: props.node.id,
    advertise_addr: "",
});

const submit = () => {
    initForm.post(route("swarm-tasks.init-cluster"), {
        preserveScroll: "errors",
    });
};
</script>

<template>
    <FormSection @submitted="submit">
        <template #title>Swarm Cluster</template>

        <template #description>
            This is your first node in the swarm cluster. Initializing a new
            swarm cluster will make this node the manager node, responsible for
            orchestrating and managing the cluster. You can add worker nodes to
            this cluster later to distribute workloads efficiently.
        </template>

        <template #form>
            <div v-auto-animate class="col-span-6 sm:col-span-4">
                <div class="grid gap-4">
                    <template v-if="$props.node.data.host.networks.length > 0">
                        <FormField :error="initForm.errors.advertise_addr">
                            <template #label>Advertisement Address</template>
                            <Select
                                id="advertise_addr"
                                v-model="initForm.advertise_addr"
                                placeholder="Select Advertise Address"
                            >
                                <optgroup
                                    v-for="network in $props.node.data.host
                                        .networks"
                                    :label="network.if_name"
                                >
                                    <option
                                        v-for="ip in network.ips"
                                        :value="ip.ip"
                                    >
                                        {{ ip.ip }}
                                    </option>
                                </optgroup>
                            </Select>
                        </FormField>
                    </template>
                    <template v-else> No IP found </template>
                </div>
            </div>
        </template>

        <template #submit>
            <PrimaryButton type="submit"
                >Initialize Swarm Cluster</PrimaryButton
            >
        </template>
    </FormSection>
</template>
