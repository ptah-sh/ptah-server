<script setup>
import FormSection from "@/Components/FormSection.vue";
import TeamsLayout from "./TeamsLayout.vue";
import { useForm } from "@inertiajs/vue3";
import FormField from "@/Components/FormField.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    team: Object,
});

const backupForm = useForm({
    retentionDays: props.team.backup_retention_days,
});

const saveBackupSettings = () => {
    backupForm.patch(route("teams.settings.backup", { team: props.team.id }));
};
</script>

<template>
    <TeamsLayout :team="team">
        <FormSection @submit="saveBackupSettings">
            <template #title> Backup Settings </template>
            <template #description>
                Manage your team backup settings.
            </template>
            <template #form>
                <FormField
                    class="col-span-3"
                    :error="backupForm.errors.retentionDays"
                >
                    <template #label> Default Retention Days </template>

                    <TextInput
                        v-model="backupForm.retentionDays"
                        class="w-full"
                    />
                    <template #hint>
                        The number of days to keep backups for.
                    </template>
                </FormField>
            </template>

            <template #actions>
                <div class="col-span-12 flex justify-end">
                    <PrimaryButton type="submit"> Save </PrimaryButton>
                </div>
            </template>
        </FormSection>
    </TeamsLayout>
</template>
