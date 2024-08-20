<script setup>
import { defineProps } from "vue";
import { useForm } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import FormSection from "@/Components/FormSection.vue";
import Select from "@/Components/Select.vue";
import FormField from "@/Components/FormField.vue";

const props = defineProps({
    node: Object,
});

const joinForm = useForm({
    node_id: props.node.id,
    role: "manager",
    advertise_addr: "",
});

const submit = () => {
    joinForm.post(route("swarm-tasks.join-cluster"), {
        preserveScroll: "errors",
    });
};
</script>

<template>
    <FormSection @submitted="submit">
        <template #title>Swarm Cluster</template>

        <template #description>
            Connect this node to an existing Docker Swarm cluster, enabling
            seamless integration and distributed computing capabilities.
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <div class="grid gap-4">
                    <FormField :error="joinForm.errors.role">
                        <template #label> Role </template>

                        <Select v-model="joinForm.role">
                            <option value="manager">Manager</option>
                            <option value="worker">Worker</option>
                        </Select>
                    </FormField>

                    <FormField :error="joinForm.errors.advertise_addr">
                        <template #label>Advertisement Address</template>
                        <Select
                            id="advertise_addr"
                            v-model="joinForm.advertise_addr"
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
                </div>
            </div>
        </template>

        <template #submit>
            <PrimaryButton type="submit">Join Swarm Cluster</PrimaryButton>
        </template>
    </FormSection>
</template>
