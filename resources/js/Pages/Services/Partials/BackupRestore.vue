<script setup>
import DangerButton from "@/Components/DangerButton.vue";
import DialogModal from "@/Components/DialogModal.vue";
import FormField from "@/Components/FormField.vue";
import InputError from "@/Components/InputError.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import Select from "@/Components/Select.vue";
import TextInput from "@/Components/TextInput.vue";
import { router, useForm } from "@inertiajs/vue3";
import dayjs from "dayjs";
import { computed, effect, ref } from "vue";

const props = defineProps({
    service: Object,
    backup: Object,
    restoreWorkers: Array,
    s3Storages: Array,
});

const emit = defineEmits(["close"]);

const confirmation = ref({});

effect(() => {
    if (props.backup) {
        confirmation.value = {};
        restoreForm.reset();
    }
});

const s3StorageName = computed(() => {
    return props.s3Storages.find(
        (s3Storage) => s3Storage.id === props.backup.s3_storage_id,
    )?.name;
});

const restoreForm = useForm({
    worker: null,
});

const handleSubmit = () => {
    restoreForm.errors = {};

    if (confirmation.value.processName !== props.backup.process) {
        restoreForm.errors.processName =
            "The process name does not match the backup process name.";
    }

    if (confirmation.value.workerName !== props.backup.worker) {
        restoreForm.errors.workerName =
            "The worker name does not match the backup worker name.";
    }

    if (restoreForm.worker === null) {
        restoreForm.errors.worker = "The restore worker is required.";
    }

    if (Object.keys(restoreForm.errors).length > 0) {
        return;
    }

    restoreForm.post(
        route("workers.execute", {
            service: props.service.slug,
            process: props.backup.process,
            worker: restoreForm.worker,
            backup: props.backup.id,
        }),
        {
            onSuccess: () => {
                emit("close");

                router.visit(route("services.deployments", props.service.slug));
            },
        },
    );
};
</script>

<template>
    <DialogModal :show="props.backup !== null" @close="$emit('close')">
        <template #title>Restore Backup for {{ props.service.name }}</template>

        <template #content>
            <div class="space-y-4" v-if="backup">
                <p>
                    <strong>Warning:</strong> This is a potentially destructive
                    action. Please confirm you would like to restore this backup
                    by entering the process and the backup producer worker name.
                </p>

                <dl class="grid grid-cols-3 gap-4">
                    <dt>Process</dt>
                    <dd class="col-span-2">{{ backup.process }}</dd>

                    <dt>Producer Worker</dt>
                    <dd class="col-span-2">{{ backup.worker }}</dd>

                    <dt>Created At</dt>
                    <dd class="col-span-2">
                        {{ dayjs(backup.ended_at).format("LLLL") }}
                    </dd>

                    <dt>S3 Storage</dt>
                    <dd class="col-span-2">
                        {{ s3StorageName ?? backup.s3_storage_id }}
                    </dd>

                    <dt>S3 Backup Location</dt>
                    <dd class="col-span-2">{{ backup.dest_path }}</dd>
                </dl>

                <InputError
                    v-if="!s3StorageName"
                    message="Backup restoration is not possible. S3 storage doesn't exist."
                />
                <InputError
                    v-else-if="restoreWorkers.length === 0"
                    message="No workers available to restore the backup with."
                />
                <template v-else>
                    <form
                        id="restore-form"
                        @submit.prevent="handleSubmit"
                        class="grid grid-cols-2 gap-4"
                    >
                        <FormField :error="restoreForm.errors.processName">
                            <template #label>Process Name</template>

                            <template #hint>
                                Please enter the name of the process to confirm
                                you would like to restore the backup.
                            </template>

                            <TextInput
                                v-model="confirmation.processName"
                                class="w-full"
                            />
                        </FormField>

                        <FormField :error="restoreForm.errors.workerName">
                            <template #label>Producer Worker Name</template>

                            <template #hint
                                >Please enter the name of the worker to confirm
                                you would like to restore the backup.</template
                            >

                            <TextInput
                                v-model="confirmation.workerName"
                                class="w-full"
                            />
                        </FormField>
                    </form>

                    <FormField :error="restoreForm.errors.worker">
                        <template #label>Restore with Worker</template>

                        <template #hint
                            >The selected worker will be used to restore the
                            backup.</template
                        >

                        <Select
                            v-model="restoreForm.worker"
                            placeholder="Select a worker"
                        >
                            <option
                                v-for="worker in restoreWorkers"
                                :value="worker.dockerName"
                                :key="worker.dockerName"
                            >
                                {{ worker.name }} $ {{ worker.command }}
                            </option>
                        </Select>
                    </FormField>
                </template>
            </div>
        </template>

        <template #footer>
            <SecondaryButton @click="$emit('close')"> Cancel </SecondaryButton>

            <DangerButton
                type="submit"
                form="restore-form"
                class="ms-3"
                :class="{ 'opacity-25': restoreForm.processing }"
                :disabled="restoreForm.processing"
            >
                Restore Backup
            </DangerButton>
        </template>
    </DialogModal>
</template>
