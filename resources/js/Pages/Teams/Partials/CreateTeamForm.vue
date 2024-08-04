<script setup>
import { useForm } from "@inertiajs/vue3";
import FormSection from "@/Components/FormSection.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import FormField from "@/Components/FormField.vue";
import ActionSection from "@/Components/ActionSection.vue";

const form = useForm({
    name: "",
    billing_name: "",
    billing_email: "",
});

const createTeam = () => {
    form.post(route("teams.store"), {
        errorBag: "createTeam",
        preserveScroll: true,
    });
};
</script>

<template>
    <ActionSection>
        <template #title> Customer Details </template>

        <template #description>
            Invoices and receipts will be sent to the specified E-Mail address.
        </template>

        <template #content>
            <FormField class="col-span-4" :error="form.errors.billing_name">
                <template #label> Business Name </template>

                <TextInput v-model="form.billing_name" class="w-full" />
            </FormField>

            <FormField class="col-span-4" :error="form.errors.billing_email">
                <template #label> Business Email </template>

                <TextInput v-model="form.billing_email" class="w-full" />

                <template #hint>
                    E-Mails should be unique. You can use '+' sign to use the
                    same email for multiple teams. For example,
                    oliver+new-team@ptah.sh
                </template>
            </FormField>
        </template>
    </ActionSection>

    <FormSection @submitted="createTeam">
        <template #title> Team Details </template>

        <template #description>
            Create a new team to collaborate with others on projects.
        </template>

        <template #form>
            <div class="col-span-6">
                <InputLabel value="Team Owner" />

                <div class="flex items-center mt-2">
                    <img
                        class="object-cover w-12 h-12 rounded-full"
                        :src="$page.props.auth.user.profile_photo_url"
                        :alt="$page.props.auth.user.name"
                    />

                    <div class="ms-4 leading-tight">
                        <div class="text-gray-900 dark:text-white">
                            {{ $page.props.auth.user.name }}
                        </div>
                        <div class="text-sm text-gray-700 dark:text-gray-300">
                            {{ $page.props.auth.user.email }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="name" value="Team Name" />
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="block w-full mt-1"
                    autofocus
                />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>
        </template>

        <template #submit>
            <PrimaryButton
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Create
            </PrimaryButton>
        </template>
    </FormSection>
</template>
