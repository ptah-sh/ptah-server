<script setup>
import { useForm } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Card from "@/Components/Card.vue";
import InfoField from "@/Components/Admin/InfoField.vue";
import FormField from "@/Components/FormField.vue";

const props = defineProps({
    team: Object,
    quotas: Object,
});

const form = useForm({
    quotas: Object.fromEntries(
        Object.entries(props.quotas).map(([key, value]) => [
            key,
            value.maxUsage,
        ]),
    ),
});

const submit = () => {
    form.put(route("admin.teams.update", props.team.id));
};
</script>

<template>
    <AdminLayout :title="`Edit Team: ${team.name}`">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col gap-8">
                <Card>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <InfoField label="Team Name" :value="team.name" />
                        <InfoField
                            label="Team Owner"
                            :value="team.owner.name"
                        />
                        <InfoField
                            label="Billing Name"
                            :value="team.billing_name || 'N/A'"
                        />
                        <InfoField
                            label="Billing Email"
                            :value="team.billing_email || 'N/A'"
                        />
                    </div>
                </Card>

                <Card>
                    <form @submit.prevent="submit">
                        <h2
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            Quotas
                        </h2>
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-4"
                        >
                            <FormField
                                v-for="(quota, key) in form.quotas"
                                :key="key"
                            >
                                <template #label>{{ key }}</template>
                                <template #counter
                                    >{{ props.quotas[key].currentUsage }} /
                                    {{
                                        props.quotas[key].maxUsage
                                    }}
                                    used</template
                                >

                                <TextInput
                                    :id="key"
                                    v-model="form.quotas[key]"
                                    type="number"
                                    class="block w-full"
                                    required
                                    min="0"
                                    :disabled="!props.quotas[key].isSoft"
                                />

                                <template #hint v-if="!props.quotas[key].isSoft"
                                    >This is a hard quota and cannot be
                                    edited.</template
                                >
                            </FormField>
                        </div>

                        <div class="flex items-center justify-end mt-8">
                            <PrimaryButton
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                            >
                                Update Quotas
                            </PrimaryButton>
                        </div>
                    </form>
                </Card>
            </div>
        </div>
    </AdminLayout>
</template>
