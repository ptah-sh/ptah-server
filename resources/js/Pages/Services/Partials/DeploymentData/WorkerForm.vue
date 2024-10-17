<script setup>
import { computed, ref, effect } from "vue";
import { FwbToggle, FwbTooltip } from "flowbite-vue";
import TextInput from "@/Components/TextInput.vue";
import FormField from "@/Components/FormField.vue";
import Select from "@/Components/Select.vue";

const props = defineProps({
    model: Object,
    errors: Object,
    dockerRegistries: Array,
    s3Storages: Array,
});

const model = defineModel();

const customCrontab = ref(null);

const archiveFormat = ref(null);
effect(() => {
    archiveFormat.value = model.value.backupCreate?.archive?.format ?? null;
});

const defaultCrontab = "0 0 * * *";
const crontabValues = {
    "* * * * *": "Every minute",
    "*/5 * * * *": "Every five minutes",
    "0 * * * *": "Every hour",
    "0 0 * * *": "Every day at midnight",
    "0 0 * * 0": "Every Sunday at midnight",
};

effect(() => {
    if (!model.value.crontab && customCrontab.value) {
        customCrontab.value.focus();
    }
});

const isCronjob = computed(() => {
    return ["cronjob", "backup_create"].includes(model.value.launchMode);
});

const isBackup = computed(() => {
    return ["backup_create"].includes(model.value.launchMode);
});

const isRestore = computed(() => {
    return ["backup_restore"].includes(model.value.launchMode);
});

const resetCrontab = () => {
    model.value.crontab = defaultCrontab;
};

const handleLaunchModeChange = (evt) => {
    model.value.replicas = 1;

    if (isCronjob.value) {
        model.value.crontab = defaultCrontab;
    } else {
        model.value.crontab = null;
    }

    if (isBackup.value) {
        model.value.backupCreate = {
            s3StorageId:
                props.s3Storages.length > 0 ? props.s3Storages[0].id : null,
            archive: {
                format: "tar.gz",
            },
        };
    } else {
        model.value.backupCreate = null;
    }

    if (isRestore.value) {
        model.value.backupRestore = {};
    } else {
        model.value.backupRestore = null;
    }
};

const toggleReleaseCommand = (evt) => {
    model.value.releaseCommand.command = evt.target.checked ? "" : null;
};

const toggleCommand = (evt) => {
    model.value.command = evt.target.checked ? "" : null;
};

const toggleHealthcheck = (evt) => {
    model.value.healthcheck.command = evt.target.checked ? "" : null;
};

const calculateHealthcheckTimes = computed(() => {
    const healthcheck = model.value.healthcheck;
    if (healthcheck.command === null) {
        return null;
    }

    const interval = Number(healthcheck.interval) || 10;
    const timeout = Number(healthcheck.timeout) || 5;
    const retries = Number(healthcheck.retries) || 10;
    const startPeriod = Number(healthcheck.startPeriod) || 60;

    const optimisticTime = interval;
    const pessimisticTime =
        retries * interval + retries * timeout + startPeriod;

    return {
        optimistic: optimisticTime,
        pessimistic: pessimisticTime,
    };
});

const commandPlaceholder = computed(() => {
    if (model.value.launchMode === "cronjob") {
        return "php artisan schedule:work";
    }

    if (model.value.launchMode === "backup_create") {
        return "PGPASSWORD=$POSTGRESQL_PASSWORD pg_dump -Fc -h $PTAH_HOSTNAME -U $POSTGRESQL_USER -d $POSTGRESQL_DATABASE > $PTAH_BACKUP_DIR/daily_backup.dump";
    }

    if (model.value.launchMode === "backup_restore") {
        return "PGPASSWORD=$POSTGRESQL_PASSWORD pg_restore -h $PTAH_HOSTNAME -U $POSTGRESQL_USER -d $POSTGRESQL_DATABASE $PTAH_BACKUP_DIR/daily_backup.dump";
    }

    return "php artisan queue:work";
});
</script>

<template>
    <FormField class="col-span-2" :error="props.errors['name']">
        <template #label>
            <fwb-tooltip class="">
                <template #trigger>
                    <div class="flex items-center">
                        Worker Name

                        <svg
                            class="ms-1 w-4 h-4 text-blue-600 dark:text-white"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9.529 9.988a2.502 2.502 0 1 1 5 .191A2.441 2.441 0 0 1 12 12.582V14m-.01 3.008H12M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                            />
                        </svg>
                    </div>
                </template>

                <template #content>
                    Workers will be launched as a standalone Docker Service, but
                    they will share the same Docker Image, Configs and Secrets.
                    Volumes, Ports and Web endpoints will not be shared.
                </template>
            </fwb-tooltip>
        </template>

        <TextInput
            v-model="model.name"
            :readonly="model.name === 'main'"
            class="block w-full transition-all duration-300"
            placeholder="worker"
        />
    </FormField>

    <div class="col-span-4 grid grid-cols-4 gap-4" v-auto-animate>
        <FormField class="col-span-2" :error="props.errors['launchMode']">
            <template #label>Launch Mode</template>

            <Select v-model="model.launchMode" @change="handleLaunchModeChange">
                <option value="daemon">Daemon or Queue Worker</option>
                <option value="cronjob">Schedule: cronjob</option>
                <option value="backup_create">Backup: create a backup</option>
                <option value="backup_restore">Backup: restore a backup</option>
                <!-- <option value="manual">Manual Only</option> -->
            </Select>
        </FormField>

        <FormField
            v-if="isCronjob"
            class="col-span-2"
            :error="props.errors['crontab']"
        >
            <template #label>
                <span
                    class="flex items-center justify-between pr-2"
                    v-auto-animate
                >
                    <span>Schedule</span>
                    <button
                        v-if="!(model.crontab in crontabValues)"
                        @click="resetCrontab"
                        type="button"
                        class="text-xs text-gray-500 hover:text-gray-900"
                    >
                        reset
                    </button>
                </span>
            </template>

            <div v-auto-animate>
                <Select
                    v-if="model.crontab in crontabValues"
                    v-model="model.crontab"
                >
                    <option v-for="(value, key) in crontabValues" :value="key">
                        {{ value }}
                    </option>
                    <option value="">Custom</option>
                </Select>

                <TextInput
                    v-else
                    ref="customCrontab"
                    v-model="model.crontab"
                    class="block w-full"
                    placeholder="* * * * *"
                />
            </div>
        </FormField>

        <FormField
            v-if="model.launchMode === 'daemon'"
            class="col-span-2"
            :error="props.errors['replicas']"
        >
            <template #label>Replicas</template>

            <TextInput v-model="model.replicas" class="w-full" />
        </FormField>
    </div>

    <FormField class="col-span-2" :error="props.errors['dockerRegistryId']">
        <template #label>Docker Registry</template>

        <Select v-model="model.dockerRegistryId">
            <option :value="null">Docker Hub / Anonymous</option>
            <option v-for="registry in dockerRegistries" :value="registry.id">
                {{ registry.name }}
            </option>
        </Select>
    </FormField>

    <FormField class="col-span-4" :error="props.errors['dockerImage']">
        <template #label>Docker Image</template>

        <div class="flex gap-4">
            <TextInput
                v-model="model.dockerImage"
                class="block w-full"
                placeholder="nginxdemos/hello:latest"
            />
        </div>
    </FormField>

    <div
        v-if="isBackup && model.backupCreate"
        class="grid grid-cols-6 gap-4 col-span-full"
    >
        <FormField
            class="col-span-2"
            :error="
                s3Storages.length > 0
                    ? props.errors['backupCreate.s3StorageId']
                    : 'You need to add at least one S3 Storage to use backups.'
            "
        >
            <template #label>Backup S3 Storage</template>

            <Select
                v-model="model.backupCreate.s3StorageId"
                :placeholder="
                    s3Storages.length > 0
                        ? 'Select S3 Storage'
                        : 'No S3 Storages found'
                "
            >
                <option v-for="storage in s3Storages" :value="storage.id">
                    {{ storage.name }}
                </option>
            </Select>
        </FormField>

        <FormField
            class="col-span-2"
            :error="props.errors['backupCreate.archive.format']"
        >
            <template #label>Archive Format</template>

            <Select v-model="model.backupCreate.archive.format">
                <option value="tar.gz">tar.gz</option>
                <option value="zip">zip</option>
            </Select>
        </FormField>
    </div>

    <FormField class="col-span-full">
        <template #label>Advanced Options</template>

        <div class="col-span-full flex gap-8">
            <div>
                <FwbToggle
                    label="Use Release Command"
                    :modelValue="model.releaseCommand.command !== null"
                    @change="toggleReleaseCommand"
                />
            </div>
            <div>
                <FwbToggle
                    label="Override Command"
                    :modelValue="model.command !== null"
                    @change="toggleCommand"
                />
            </div>
            <div>
                <FwbToggle
                    label="Override Healthcheck"
                    :modelValue="model.healthcheck.command !== null"
                    @change="toggleHealthcheck"
                />
            </div>
        </div>
    </FormField>

    <FormField
        v-if="model.releaseCommand.command !== null"
        class="col-span-6"
        :error="props.errors['releaseCommand.command']"
    >
        <template #label>
            <fwb-tooltip class="">
                <template #trigger>
                    <div class="flex items-center">
                        Release Command

                        <svg
                            class="ms-1 w-4 h-4 text-blue-600 dark:text-white"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9.529 9.988a2.502 2.502 0 1 1 5 .191A2.441 2.441 0 0 1 12 12.582V14m-.01 3.008H12M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                            />
                        </svg>
                    </div>
                </template>

                <template #content>
                    This command will be executed right before the Docker
                    Image's entrypoint. You can leave it empty.
                </template>
            </fwb-tooltip>
        </template>

        <TextInput
            v-model="model.releaseCommand.command"
            class="block w-full"
            placeholder="php artisan config:cache && php artisan migrate --no-interaction --verbose --ansi --force"
        />
    </FormField>

    <FormField
        v-if="model.command !== null"
        class="col-span-6"
        :error="props.errors['command']"
    >
        <template #label>
            <fwb-tooltip class="">
                <template #trigger>
                    <div class="flex items-center">
                        Command

                        <svg
                            class="ms-1 w-4 h-4 text-blue-600 dark:text-white"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9.529 9.988a2.502 2.502 0 1 1 5 .191A2.441 2.441 0 0 1 12 12.582V14m-.01 3.008H12M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                            />
                        </svg>
                    </div>
                </template>

                <template #content>
                    You can leave this field empty to use the default command
                    defined in your Dockerfile.
                </template>
            </fwb-tooltip>
        </template>

        <TextInput
            v-model="model.command"
            class="block w-full"
            :placeholder="commandPlaceholder"
        />
    </FormField>

    <FormField
        v-if="model.healthcheck.command !== null"
        class="col-span-6"
        :error="props.errors['healthcheck.command']"
    >
        <template #label>
            <fwb-tooltip class="">
                <template #trigger>
                    <div class="flex items-center">
                        Healthcheck Command
                        <svg
                            class="ms-1 w-4 h-4 text-blue-600 dark:text-white"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9.529 9.988a2.502 2.502 0 1 1 5 .191A2.441 2.441 0 0 1 12 12.582V14m-.01 3.008H12M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                            />
                        </svg>
                    </div>
                </template>
                <template #content>
                    Specify a command to check the health of your container.
                    This command will be run inside the defined container and
                    will override any default healthcheck provided by the
                    container's image Dockerfile.
                </template>
            </fwb-tooltip>
        </template>

        <TextInput
            v-model="model.healthcheck.command"
            class="block w-full"
            placeholder="curl -f http://localhost/ || exit 1"
        />
    </FormField>

    <div
        v-if="model.healthcheck.command !== null"
        class="col-span-6 grid grid-cols-5 gap-4"
    >
        <FormField
            :error="props.errors['healthcheck.interval']"
            class="col-span-1"
        >
            <template #label>Interval, s</template>
            <TextInput
                v-model="model.healthcheck.interval"
                class="block w-full"
                placeholder="10"
                type="number"
            />
        </FormField>

        <FormField
            :error="props.errors['healthcheck.timeout']"
            class="col-span-1"
        >
            <template #label>Timeout, s</template>
            <TextInput
                v-model="model.healthcheck.timeout"
                class="block w-full"
                placeholder="5"
                type="number"
            />
        </FormField>

        <FormField
            :error="props.errors['healthcheck.retries']"
            class="col-span-1"
        >
            <template #label>Retries</template>
            <TextInput
                v-model="model.healthcheck.retries"
                class="block w-full"
                type="number"
                placeholder="10"
            />
        </FormField>

        <FormField
            :error="props.errors['healthcheck.startPeriod']"
            class="col-span-1"
        >
            <template #label>Start Period, s</template>
            <TextInput
                v-model="model.healthcheck.startPeriod"
                class="block w-full"
                placeholder="60"
                type="number"
            />
        </FormField>

        <FormField
            :error="props.errors['healthcheck.startInterval']"
            class="col-span-1"
        >
            <template #label>Start Interval, s</template>
            <TextInput
                v-model="model.healthcheck.startInterval"
                class="block w-full"
                placeholder="10"
                type="number"
            />
        </FormField>

        <div class="col-span-full" v-if="calculateHealthcheckTimes">
            <p class="text-sm text-gray-600">
                Healthcheck pass time:
                <span class="font-semibold"
                    >Optimistic:
                    {{ calculateHealthcheckTimes.optimistic }}s</span
                >
                |
                <span class="font-semibold"
                    >Pessimistic:
                    {{ calculateHealthcheckTimes.pessimistic }}s</span
                >
            </p>
        </div>
    </div>
</template>
