<script setup>
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import ActionSection from "@/Components/ActionSection.vue";
import FormField from "@/Components/FormField.vue";
import Select from "@/Components/Select.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextArea from "@/Components/TextArea.vue";
import { computed, effect, nextTick, reactive, ref } from "vue";
import { FwbToggle, FwbTooltip } from "flowbite-vue";
import ProcessTabs from "@/Pages/Services/Partials/ProcessTabs.vue";
import DangerButton from "@/Components/DangerButton.vue";
import DialogModal from "@/Components/DialogModal.vue";
import AddComponentButton from "@/Components/Service/AddComponentButton.vue";
import RemoveComponentButton from "@/Components/Service/RemoveComponentButton.vue";
import ComponentBlock from "@/Components/Service/ComponentBlock.vue";
import BackupSchedule from "@/Components/BackupSchedule.vue";

const model = defineModel();

const props = defineProps({
    networks: Array,
    nodes: Array,
    serviceName: String | undefined,
    dockerRegistries: Array,
    s3Storages: Array,
    errors: Object,
});

const state = reactive({
    internalDomainTouched: Boolean(model.value.internalDomain),
    selectedProcessIndex: {
        processes: 0,
        envVars: 0,
        secretVars: 0,
        configFiles: 0,
        secretFiles: 0,
        volumes: 0,
        caddy: 0,
    },
});

effect(() => {
    if (props.serviceName && !state.internalDomainTouched) {
        model.value.internalDomain = props.serviceName + ".local";
    }
});

const makeId = (prefix) => prefix + "-" + Math.random().toString(36).slice(2);

const addEnvVar = () => {
    model.value.processes[state.selectedProcessIndex["envVars"]].envVars.push({
        id: makeId("env"),
        name: "",
        value: "",
    });
};

const addSecretVar = () => {
    model.value.processes[
        state.selectedProcessIndex["secretVars"]
    ].secretVars.vars.push({ id: makeId("secret-var"), name: "", value: "" });
};

const addConfigFile = () => {
    model.value.processes[
        state.selectedProcessIndex["configFiles"]
    ].configFiles.push({ id: makeId("config"), name: "", content: "" });
};

const addSecretFile = () => {
    model.value.processes[
        state.selectedProcessIndex["secretFiles"]
    ].secretFiles.push({ id: makeId("secret-file"), name: "", content: "" });
};

const addVolume = () => {
    model.value.processes[state.selectedProcessIndex["volumes"]].volumes.push({
        id: makeId("volume"),
        name: "",
        path: "",
        backupSchedule: null,
    });
};

const addPort = () => {
    model.value.processes[state.selectedProcessIndex["caddy"]].ports.push({
        id: makeId("port"),
        targetPort: "",
        publishedPort: "",
    });
};

const addCaddy = () => {
    model.value.processes[state.selectedProcessIndex["caddy"]].caddy.push({
        id: makeId("caddy"),
        targetProtocol: "http",
        targetPort: "",
        publishedPort: "443",
        domain: "",
        path: "",
    });
};

const addRedirectRule = () => {
    model.value.processes[
        state.selectedProcessIndex["caddy"]
    ].redirectRules.push({
        id: makeId("redirect-rule"),
        domainFrom: "",
        domainTo: "",
        pathFrom: "",
        pathTo: "",
        statusCode: 301,
    });
};

const addRewriteRule = () => {
    model.value.processes[
        state.selectedProcessIndex["caddy"]
    ].rewriteRules.push({
        id: makeId("rewrite-rule"),
        pathFrom: "",
        pathTo: "",
    });
};

const addFastCgiVar = () => {
    model.value.processes[state.selectedProcessIndex["caddy"]].fastCgi.env.push(
        { id: makeId("fastcgi"), name: "", value: "" },
    );
};

const addProcess = () => {
    const newIndex = model.value.processes.length;

    model.value.processes.push({
        id: makeId("process"),
        name: "process_" + newIndex,
        dockerRegistryId: null,
        dockerImage: "",
        releaseCommand: {
            command: "",
        },
        command: "",
        backups: [],
        workers: [],
        launchMode: "daemon",
        envVars: [],
        secretVars: {
            vars: [],
        },
        configFiles: [],
        secretFiles: [],
        volumes: [],
        ports: [],
        replicas: 1,
        caddy: [],
        redirectRules: [],
        rewriteRules: [],
        fastCgi: null,
    });

    state.selectedProcessIndex["processes"] = newIndex;
};

const removeProcess = (index) => {
    for (const [key, selectedIndex] of Object.entries(
        state.selectedProcessIndex,
    )) {
        if (selectedIndex >= index) {
            state.selectedProcessIndex[key] =
                state.selectedProcessIndex[key] - 1;
        }
    }

    model.value.processes.splice(index, 1);
};

const addWorker = () => {
    model.value.processes[state.selectedProcessIndex["processes"]].workers.push(
        {
            id: makeId("worker"),
            name: `worker_${model.value.processes[state.selectedProcessIndex["processes"]].workers.length + 1}`,
            replicas: 1,
        },
    );
};

const removeWorker = (index) => {
    model.value.processes[
        state.selectedProcessIndex["processes"]
    ].workers.splice(index, 1);
};

const hasFastCgiHandlers = computed(() => {
    return model.value.processes[
        state.selectedProcessIndex["caddy"]
    ].caddy.some((caddy) => caddy.targetProtocol === "fastcgi");
});

const defaultFastCgi = {
    root: "",
    env: [],
};

effect(() => {
    model.value.processes[state.selectedProcessIndex["caddy"]].fastCgi =
        hasFastCgiHandlers.value
            ? model.value.processes[state.selectedProcessIndex["caddy"]]
                  .fastCgi || defaultFastCgi
            : null;
});

const toggleVolumeBackups = (volume) => {
    if (volume.backupSchedule === null) {
        volume.backupSchedule = {
            preset: "daily",
            s3StorageId: props.s3Storages[0].id,
            expr: "0 0 * * *",
        };
    } else {
        volume.backupSchedule = null;
    }
};

const addProcessBackup = () => {
    model.value.processes[state.selectedProcessIndex["processes"]].backups.push(
        {
            id: makeId("backup-cmd"),
            backupSchedule: {
                preset: "daily",
                s3StorageId: props.s3Storages[0].id,
                expr: "0 0 * * *",
            },
        },
    );
};

const processRemoveInput = ref();

const processRemoval = reactive({
    open: false,
    processName: "",
    error: "",
});

const confirmProcessRemoval = (index) => {
    processRemoval.open = true;

    nextTick(() => {
        if (processRemoveInput.value) {
            processRemoveInput.value.focus();
        }
    });
};

const closeProcessRemovalModal = () => {
    processRemoval.open = false;
    processRemoval.processName = "";
    processRemoval.error = "";
};

const submitProcessRemoval = () => {
    if (
        processRemoval.processName !==
        model.value.processes[state.selectedProcessIndex["processes"]].name
    ) {
        processRemoval.error = "Invalid process name.";
    } else {
        removeProcess(state.selectedProcessIndex["processes"]);
        closeProcessRemovalModal();
    }
};

const extractFieldErrors = (basePath) => {
    return Object.fromEntries(
        Object.keys(props.errors)
            .filter((key) => key.startsWith(basePath))
            .map((key) => [
                key.substring(basePath.length + 1),
                props.errors[key],
            ]),
    );
};
</script>

<template>
    <ActionSection>
        <template #title> Service Location </template>

        <template #description>
            <p>
                To isolate containers from one another, you can utilize
                different networks.
            </p>
            <p>
                Services can be accessed by their internal domain name by other
                containers on the same network.
            </p>
            <p>
                If no Placement Node is selected, the containers will be able to
                run on any node within the Swarm Cluster.
            </p>
        </template>

        <template #content>
            <FormField
                :error="props.errors['internalDomain']"
                class="col-span-2"
            >
                <template #label>Internal Domain Name</template>

                <TextInput
                    v-model="model.internalDomain"
                    @change="state.internalDomainTouched = true"
                    class="w-full"
                />
            </FormField>

            <FormField :error="props.errors['networkName']" class="col-span-2">
                <template #label>Attach to Network</template>

                <Select v-model="model.networkName">
                    <option
                        v-for="network in $page.props.networks"
                        :value="network.docker_name"
                    >
                        {{ network.name }}
                    </option>
                </Select>
            </FormField>

            <FormField
                :error="props.errors['placementNodeId']"
                class="col-span-2"
            >
                <template #label>Placement Node</template>

                <Select v-model="model.placementNodeId">
                    <option :value="null">Run on all Nodes</option>
                    <option v-for="node in $page.props.nodes" :value="node.id">
                        {{ node.name }}
                    </option>
                </Select>
            </FormField>
        </template>
    </ActionSection>

    <ActionSection>
        <template #title> Processes </template>

        <template #description>
            <p>
                You can start multiple service instances if your image contains
                multiple entry points. Each process will be run in it's own
                container and can define it's own environment variables,
                secrets, files and so on.
            </p>
        </template>

        <template #tabs>
            <div class="flex">
                <ProcessTabs
                    v-model="state"
                    :processes="model.processes"
                    block="processes"
                />

                <div class="flex items-center">
                    <button
                        type="button"
                        @click="addProcess"
                        class="flex items-center justify-center p-1 mx-1 px-2 border rounded bg-gray-200 text-xs hover:text-black hover:bg-white"
                    >
                        <svg
                            class="w-4 h-4 me-1 text-gray-800 dark:text-white"
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
                                d="M5 12h14m-7 7V5"
                            />
                        </svg>

                        process
                    </button>
                </div>
            </div>
        </template>

        <template #content>
            <FormField
                class="col-span-2"
                :error="
                    props.errors[
                        `processes.${state.selectedProcessIndex['processes']}.dockerRegistryId`
                    ]
                "
            >
                <template #label>Docker Registry</template>

                <Select
                    v-model="
                        model.processes[state.selectedProcessIndex['processes']]
                            .dockerRegistryId
                    "
                >
                    <option :value="null">Docker Hub / Anonymous</option>
                    <option
                        v-for="registry in $props.dockerRegistries"
                        :value="registry.id"
                    >
                        {{ registry.name }}
                    </option>
                </Select>
            </FormField>

            <FormField
                class="col-span-4"
                :error="
                    props.errors[
                        `processes.${state.selectedProcessIndex['processes']}.dockerImage`
                    ]
                "
            >
                <template #label>Docker Image</template>

                <div class="flex gap-4">
                    <TextInput
                        v-model="
                            model.processes[
                                state.selectedProcessIndex['processes']
                            ].dockerImage
                        "
                        class="block w-full"
                        placeholder="nginxdemos/hello:latest"
                    />

                    <DangerButton
                        v-if="
                            model.processes[
                                state.selectedProcessIndex['processes']
                            ].dockerName &&
                            model.processes.length > 1 &&
                            model.processes[
                                state.selectedProcessIndex['processes']
                            ].name
                        "
                        @click="
                            confirmProcessRemoval(
                                state.selectedProcessIndex['processes'],
                            )
                        "
                        tabindex="-1"
                    >
                        <svg
                            class="w-4 h-4 text-gray-800 dark:text-white"
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
                                d="M5 12h14"
                            />
                        </svg>
                    </DangerButton>
                    <SecondaryButton
                        v-else
                        @click="
                            removeProcess(
                                state.selectedProcessIndex['processes'],
                            )
                        "
                        tabindex="-1"
                        :disabled="model.processes.length === 1"
                    >
                        <svg
                            class="w-4 h-4 text-gray-800 dark:text-white"
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
                                d="M5 12h14"
                            />
                        </svg>
                    </SecondaryButton>
                </div>
            </FormField>

            <FormField
                class="col-span-2 relative"
                :error="
                    props.errors[
                        `processes.${state.selectedProcessIndex['processes']}.name`
                    ]
                "
            >
                <template #label>Name</template>

                <TextInput
                    v-model="
                        model.processes[state.selectedProcessIndex['processes']]
                            .name
                    "
                    class="block w-full"
                    placeholder="queue-worker"
                />

                <span class="text-gray-500 text-xs"
                    >{{
                        model.processes[state.selectedProcessIndex["processes"]]
                            .name
                    }}.{{ model.internalDomain }}</span
                >
            </FormField>

            <FormField
                class="col-span-2"
                :error="
                    props.errors[
                        `processes.${state.selectedProcessIndex['processes']}.launchMode`
                    ]
                "
            >
                <template #label>Launch Mode</template>

                <Select
                    v-model="
                        model.processes[state.selectedProcessIndex['processes']]
                            .launchMode
                    "
                >
                    <option value="daemon">Daemon / Worker</option>
                    <!--          <option :value="false">Schedule (crontab)</option>-->
                    <!--          <option>Lifecycle Hook (before deploy / after deploy to, for example run migrations and/or upload static files)</option>-->
                </Select>
            </FormField>

            <FormField
                v-if="
                    model.processes[state.selectedProcessIndex['processes']]
                        .launchMode === 'scheduled'
                "
                class="col-span-1"
                :error="
                    props.errors[
                        `processes.${state.selectedProcessIndex['processes']}.interval`
                    ]
                "
            >
                <template #label>Interval</template>

                <Select
                    v-model="
                        model.processes[state.selectedProcessIndex['processes']]
                            .interval
                    "
                >
                    <option value="* * * * *">Every minute</option>
                </Select>
            </FormField>

            <FormField
                v-if="
                    model.processes[state.selectedProcessIndex['processes']]
                        .launchMode === 'daemon'
                "
                class="col-span-2"
                :error="
                    props.errors[
                        `processes.${state.selectedProcessIndex['processes']}.replicas`
                    ]
                "
            >
                <template #label>Replicas</template>

                <TextInput
                    v-model="
                        model.processes[state.selectedProcessIndex['processes']]
                            .replicas
                    "
                    class="w-full"
                />
            </FormField>

            <FormField
                class="col-span-6"
                :error="
                    props.errors[
                        `processes.${state.selectedProcessIndex['processes']}.releaseCommand.command`
                    ]
                "
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
                            This command will be executed right before the
                            Docker Image's entrypoint. You can leave it empty.
                        </template>
                    </fwb-tooltip>
                </template>

                <TextInput
                    v-model="
                        model.processes[state.selectedProcessIndex['processes']]
                            .releaseCommand.command
                    "
                    class="block w-full"
                    placeholder="php artisan config:cache && php artisan migrate --no-interaction --verbose --ansi --force"
                />
            </FormField>

            <FormField
                class="col-span-6"
                :error="
                    props.errors[
                        `processes.${state.selectedProcessIndex['processes']}.command`
                    ]
                "
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
                            You can leave this field empty to use the default
                            command defined in your Dockerfile.
                        </template>
                    </fwb-tooltip>
                </template>

                <TextInput
                    v-model="
                        model.processes[state.selectedProcessIndex['processes']]
                            .command
                    "
                    class="block w-full"
                    placeholder="php artisan queue:work"
                />
            </FormField>

            <template
                v-for="(worker, index) in model.processes[
                    state.selectedProcessIndex['processes']
                ].workers"
            >
                <hr class="col-span-full" />

                <FormField
                    class="col-span-2"
                    :error="
                        props.errors[
                            `processes.${state.selectedProcessIndex['processes']}.workers.${index}.name`
                        ]
                    "
                >
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
                                Workers will be launched as a standalone Docker
                                Service, but they will share the same Docker
                                Image, Configs and Secrets. Volumes, Ports and
                                Web endpoints will not be shared.
                            </template>
                        </fwb-tooltip>
                    </template>

                    <TextInput
                        v-model="
                            model.processes[
                                state.selectedProcessIndex['processes']
                            ].workers[index].name
                        "
                        class="block w-full"
                        :placeholder="'worker_' + (index + 1)"
                    />
                </FormField>

                <FormField
                    class="col-span-1"
                    :error="
                        props.errors[
                            `processes.${state.selectedProcessIndex['processes']}.workers.${index}.replicas`
                        ]
                    "
                >
                    <template #label> Replicas </template>

                    <div class="flex gap-2">
                        <TextInput
                            v-model="
                                model.processes[
                                    state.selectedProcessIndex['processes']
                                ].workers[index].replicas
                            "
                            class="block w-full"
                            placeholder="1"
                        />
                        <RemoveComponentButton @click="removeWorker(index)" />
                    </div>
                </FormField>

                <FormField
                    class="col-span-6"
                    :error="
                        props.errors[
                            `processes.${state.selectedProcessIndex['processes']}.workers.${index}.command`
                        ]
                    "
                >
                    <template #label> Command </template>

                    <TextInput
                        v-model="
                            model.processes[
                                state.selectedProcessIndex['processes']
                            ].workers[index].command
                        "
                        class="block w-full"
                        placeholder="php artisan config:cache && php artisan queue:work"
                    />
                </FormField>
            </template>

            <ComponentBlock
                label="Backups"
                v-model="
                    model.processes[state.selectedProcessIndex['processes']]
                        .backups
                "
                v-slot="{ item, $index }"
                @remove="
                    model.processes[
                        state.selectedProcessIndex['processes']
                    ].backups.splice($event, 1)
                "
            >
                <FormField
                    class="col-span-2"
                    :error="
                        props.errors[
                            `processes.${state.selectedProcessIndex['processes']}.backups.${$index}.name`
                        ]
                    "
                >
                    <template #label> Name </template>

                    <TextInput
                        v-model="
                            model.processes[
                                state.selectedProcessIndex['processes']
                            ].backups[$index].name
                        "
                        class="block w-full"
                        placeholder="daily"
                    />
                </FormField>

                <FormField
                    class="col-span-full"
                    :error="
                        props.errors[
                            `processes.${state.selectedProcessIndex['processes']}.backups.${$index}.name`
                        ]
                    "
                >
                    <template #label> Command </template>

                    <TextInput
                        v-model="
                            model.processes[
                                state.selectedProcessIndex['processes']
                            ].backups[$index].command
                        "
                        class="w-full"
                        placeholder="PGPASSWORD=$POSTGRESQL_PASSWORD pg_dump -Fc -h $PTAH_HOSTNAME -U $POSTGRESQL_USER -d $POSTGRESQL_DATABASE > daily_backup.dump"
                    />
                </FormField>

                <BackupSchedule
                    :s3-storages="props.s3Storages"
                    v-model="
                        model.processes[state.selectedProcessIndex['processes']]
                            .backups[$index].backupSchedule
                    "
                />
            </ComponentBlock>
        </template>

        <template #actions>
            <AddComponentButton @click="addWorker"> Worker </AddComponentButton>
            <AddComponentButton
                v-if="props.s3Storages.length > 0"
                @click="addProcessBackup"
            >
                Backup
            </AddComponentButton>

            <FwbTooltip v-else>
                <template #trigger>
                    <AddComponentButton disabled>Backup</AddComponentButton>
                </template>

                <template #content
                    >Backups can't be enabled - no S3 Storages
                    configured</template
                >
            </FwbTooltip>
        </template>
    </ActionSection>

    <ActionSection>
        <template #title> Environment Variables </template>

        <template #description>
            Add environment variables to the service. These variables will be
            stored on the Ptah.sh database and will be fully accessible to edit
            them via UI.
        </template>

        <template #tabs>
            <ProcessTabs
                v-model="state"
                :processes="model.processes"
                block="envVars"
            />
        </template>

        <template #content>
            <div
                v-if="
                    model.processes[state.selectedProcessIndex['envVars']]
                        .envVars.length === 0
                "
                class="col-span-6"
            >
                No Environment Variables defined
            </div>
            <FormField
                v-else
                v-for="(envVar, index) in model.processes[
                    state.selectedProcessIndex['envVars']
                ].envVars"
                :key="envVar.id"
                class="col-span-full"
                :error="
                    props.errors[
                        `processes.${state.selectedProcessIndex['envVars']}.envVars.${index}.name`
                    ] ||
                    props.errors[
                        `processes.${state.selectedProcessIndex['envVars']}.envVars.${index}.value`
                    ]
                "
            >
                <template #label v-if="index === 0"
                    >Environment Variables</template
                >

                <div class="flex gap-2">
                    <TextInput
                        v-model="envVar.name"
                        class="w-56"
                        placeholder="Name"
                    />
                    <TextInput
                        v-model="envVar.value"
                        class="grow"
                        placeholder="Value"
                    />

                    <SecondaryButton
                        @click="
                            model.processes[
                                state.selectedProcessIndex['envVars']
                            ].envVars.splice(index, 1)
                        "
                        tabindex="-1"
                    >
                        <svg
                            class="w-4 h-4 text-gray-800 dark:text-white"
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
                                d="M5 12h14"
                            />
                        </svg>
                    </SecondaryButton>
                </div>
            </FormField>
        </template>

        <template #actions>
            <SecondaryButton @click="addEnvVar">
                <svg
                    class="w-4 h-4 me-2 -ms-1 text-gray-800 dark:text-white"
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
                        d="M5 12h14m-7 7V5"
                    />
                </svg>
                Environment Variable
            </SecondaryButton>
        </template>
    </ActionSection>

    <ActionSection>
        <template #title> Secret Variables </template>

        <template #description>
            <p>
                Add secret variables to the service. Only variable names will be
                stored on the Ptah.sh database. You wouldn't be able to view
                their contents, but you'd able to provide new values.
            </p>
            <p>
                Secret Variables are stored as Docker Configs and you will be
                able to see it's contents via Docker CLI.
            </p>
        </template>

        <template #tabs>
            <ProcessTabs
                v-model="state"
                :processes="model.processes"
                block="secretVars"
            />
        </template>

        <template #content>
            <div
                v-if="
                    model.processes[state.selectedProcessIndex['secretVars']]
                        .secretVars.vars.length === 0
                "
                class="col-span-full"
            >
                No Secret Variables defined
            </div>
            <FormField
                v-else
                v-for="(secretVar, index) in model.processes[
                    state.selectedProcessIndex['secretVars']
                ].secretVars.vars"
                :key="secretVar.id"
                :error="
                    props.errors[
                        `processes.${state.selectedProcessIndex['secretVars']}.secretVars.vars.${index}.name`
                    ] ||
                    props.errors[
                        `processes.${state.selectedProcessIndex['secretVars']}.secretVars.vars.${index}.value`
                    ]
                "
                class="col-span-full"
            >
                <template #label v-if="index === 0">Secret Variables</template>

                <div class="flex gap-2">
                    <TextInput
                        v-model="secretVar.name"
                        class="w-56"
                        placeholder="Name"
                    />
                    <TextInput
                        v-model="secretVar.value"
                        class="grow"
                        placeholder="Value"
                    />

                    <SecondaryButton
                        @click="
                            model.processes[
                                state.selectedProcessIndex['secretVars']
                            ].secretVars.vars.splice(index, 1)
                        "
                        tabindex="-1"
                    >
                        <svg
                            class="w-4 h-4 text-gray-800 dark:text-white"
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
                                d="M5 12h14"
                            />
                        </svg>
                    </SecondaryButton>
                </div>
            </FormField>
        </template>

        <template #actions>
            <SecondaryButton @click="addSecretVar">
                <svg
                    class="w-4 h-4 me-2 -ms-1 text-gray-800 dark:text-white"
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
                        d="M5 12h14m-7 7V5"
                    />
                </svg>
                Secret Variable
            </SecondaryButton>
        </template>
    </ActionSection>

    <ActionSection>
        <template #title> Config Files </template>

        <template #description>
            <p>
                Add config files to mount into the container. Config files will
                be stored on the Ptah.sh database and will be fully accessible
                to edit them via UI.
            </p>
            <p>
                Config Files are stored as Docker Config Files and will be
                mounted directly into the container.
            </p>
        </template>

        <template #tabs>
            <ProcessTabs
                v-model="state"
                :processes="model.processes"
                block="configFiles"
            />
        </template>

        <template #content>
            <div
                v-if="
                    model.processes[state.selectedProcessIndex['configFiles']]
                        .configFiles.length === 0
                "
                class="col-span-6"
            >
                No Config Files defined
            </div>
            <template
                v-else
                v-for="(configFile, index) in model.processes[
                    state.selectedProcessIndex['configFiles']
                ].configFiles"
                :key="configFile.id"
            >
                <FormField
                    class="col-span-full"
                    :error="
                        props.errors[
                            `processes.${state.selectedProcessIndex['configFiles']}.configFiles.${index}.path`
                        ]
                    "
                >
                    <template #label>Path</template>

                    <div class="flex gap-2">
                        <TextInput
                            v-model="configFile.path"
                            class="block grow"
                        />

                        <SecondaryButton
                            @click="
                                model.processes[
                                    state.selectedProcessIndex['configFiles']
                                ].configFiles.splice(index, 1)
                            "
                            tabindex="-1"
                        >
                            <svg
                                class="w-4 h-4 text-gray-800 dark:text-white"
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
                                    d="M5 12h14"
                                />
                            </svg>
                        </SecondaryButton>
                    </div>
                </FormField>
                <FormField
                    class="col-span-full"
                    :error="
                        props.errors[
                            `processes.${state.selectedProcessIndex['configFiles']}.configFiles.${index}.content`
                        ]
                    "
                >
                    <template #label>Config File Content</template>

                    <TextArea
                        v-model="configFile.content"
                        class="block w-full"
                        rows="3"
                    />
                </FormField>
            </template>
        </template>

        <template #actions>
            <SecondaryButton @click="addConfigFile">
                <svg
                    class="w-4 h-4 me-2 -ms-1 text-gray-800 dark:text-white"
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
                        d="M5 12h14m-7 7V5"
                    />
                </svg>
                Config File
            </SecondaryButton>
        </template>
    </ActionSection>

    <ActionSection>
        <template #title> Secret Files </template>

        <template #description>
            <p>
                Add secret files to mount into the container. Secret files will
                be stored as Docker Secrets.
            </p>
            <p>
                You will not be able to view their contents unless you mount
                them into a one-time container via Docker CLI.
            </p>
            <p>
                You can provide a new value to overwrite an existing one at any
                time via the UI.
            </p>
            <p>You can safely store .env or private encryption keys here.</p>
        </template>

        <template #tabs>
            <ProcessTabs
                v-model="state"
                :processes="model.processes"
                block="secretFiles"
            />
        </template>

        <template #content>
            <div
                v-if="
                    model.processes[state.selectedProcessIndex['secretFiles']]
                        .secretFiles.length === 0
                "
                class="col-span-6"
            >
                No Secret Files defined
            </div>
            <template
                v-else
                v-for="(secretFile, index) in model.processes[
                    state.selectedProcessIndex['secretFiles']
                ].secretFiles"
                :key="secretFile.id"
            >
                <FormField
                    class="col-span-full"
                    :error="
                        props.errors[
                            `processes.${state.selectedProcessIndex['secretFiles']}.secretFiles.${index}.path`
                        ]
                    "
                >
                    <template #label>Path</template>

                    <div class="flex gap-2">
                        <TextInput
                            v-model="secretFile.path"
                            class="block grow"
                        />

                        <SecondaryButton
                            @click="
                                model.processes[
                                    state.selectedProcessIndex['secretFiles']
                                ].secretFiles.splice(index, 1)
                            "
                            tabindex="-1"
                        >
                            <svg
                                class="w-4 h-4 text-gray-800 dark:text-white"
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
                                    d="M5 12h14"
                                />
                            </svg>
                        </SecondaryButton>
                    </div>
                </FormField>
                <FormField
                    class="col-span-full"
                    :error="
                        props.errors[
                            `processes.${state.selectedProcessIndex['secretFiles']}.secretFiles.${index}.content`
                        ]
                    "
                >
                    <template #label>Secret File Content</template>

                    <TextArea
                        v-model="secretFile.content"
                        class="block w-full"
                        rows="3"
                    />
                </FormField>
            </template>
        </template>

        <template #actions>
            <SecondaryButton @click="addSecretFile">
                <svg
                    class="w-4 h-4 me-2 -ms-1 text-gray-800 dark:text-white"
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
                        d="M5 12h14m-7 7V5"
                    />
                </svg>
                Secret File
            </SecondaryButton>
        </template>
    </ActionSection>

    <ActionSection>
        <template #title> Persistent Volumes </template>

        <template #description>
            <p>
                Use Persistent Volumes if you to store your data between
                container restarts. Databases are the most common users of
                Persistent Volumes.
            </p>
            <p>
                Please note, you'll have to pick a Node to host your Persistent
                Volumes (and so launch containers on).
            </p>
        </template>

        <template #tabs>
            <ProcessTabs
                v-model="state"
                :processes="model.processes"
                block="volumes"
            />
        </template>

        <template #content>
            <div
                v-if="
                    model.processes[state.selectedProcessIndex['volumes']]
                        .volumes.length === 0
                "
                class="col-span-6"
            >
                No Persistent Volumes defined
            </div>
            <FormField
                v-else
                v-for="(volume, index) in model.processes[
                    state.selectedProcessIndex['volumes']
                ].volumes"
                :key="volume.id"
                :error="
                    props.errors[
                        `processes.${state.selectedProcessIndex['volumes']}.volumes.${index}.name`
                    ] ||
                    props.errors[
                        `processes.${state.selectedProcessIndex['volumes']}.volumes.${index}.path`
                    ]
                "
                class="col-span-full"
            >
                <template #label v-if="index === 0"
                    >Persistent Volumes</template
                >

                <div class="flex gap-2">
                    <TextInput
                        v-model="volume.name"
                        class="w-56"
                        placeholder="Name"
                    />
                    <TextInput
                        v-model="volume.path"
                        class="grow"
                        placeholder="Path"
                    />

                    <SecondaryButton
                        @click="
                            model.processes[
                                state.selectedProcessIndex['volumes']
                            ].volumes.splice(index, 1)
                        "
                        tabindex="-1"
                    >
                        <svg
                            class="w-4 h-4 text-gray-800 dark:text-white"
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
                                d="M5 12h14"
                            />
                        </svg>
                    </SecondaryButton>
                </div>
            </FormField>
            <ComponentBlock
                v-else
                v-model="
                    model.processes[state.selectedProcessIndex['volumes']]
                        .volumes
                "
                v-slot="{ item, $index }"
                @remove="
                    model.processes[
                        state.selectedProcessIndex['volumes']
                    ].volumes.splice($event, 1)
                "
            >
                <FormField
                    :error="
                        props.errors[
                            `processes.${state.selectedProcessIndex['volumes']}.volumes.${$index}.name`
                        ] ||
                        props.errors[
                            `processes.${state.selectedProcessIndex['volumes']}.volumes.${$index}.path`
                        ]
                    "
                    class="col-span-2"
                >
                    <template #label>Volume Name</template>

                    <TextInput
                        v-model="item.name"
                        class="w-full"
                        placeholder="Name"
                    />
                </FormField>

                <FormField
                    :error="
                        props.errors[
                            `processes.${state.selectedProcessIndex['volumes']}.volumes.${$index}.path`
                        ]
                    "
                    class="col-span-3"
                >
                    <template #label>Mount Path</template>

                    <TextInput
                        v-model="item.path"
                        class="w-full"
                        placeholder="Path"
                    />
                </FormField>

                <FormField class="col-span-1">
                    <template #label v-if="props.s3Storages.length > 0"
                        >Enable Backups</template
                    >

                    <template #label v-else>
                        <FwbTooltip trigger="hover">
                            <template #trigger>
                                <div class="flex">
                                    Backup

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
                                Backups can't be enabled - no S3 Storages
                                configured
                            </template>
                        </FwbTooltip>
                    </template>

                    <div class="pt-2 select-none">
                        <FwbToggle
                            :model-value="item.backupSchedule != null"
                            :disabled="props.s3Storages.length === 0"
                            :class="
                                props.s3Storages.length === 0
                                    ? 'cursor-not-allowed opacity-40'
                                    : ''
                            "
                            @change="toggleVolumeBackups(item)"
                        />
                    </div>
                </FormField>

                <BackupSchedule
                    v-if="item.backupSchedule"
                    v-model="item.backupSchedule"
                    :errors="
                        extractFieldErrors(
                            `processes.${state.selectedProcessIndex['volumes']}.volumes.${$index}.backupSchedule`,
                        )
                    "
                    :s3-storages="props.s3Storages"
                />
            </ComponentBlock>
        </template>

        <template #actions>
            <SecondaryButton @click="addVolume">
                <svg
                    class="w-4 h-4 me-2 -ms-1 text-gray-800 dark:text-white"
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
                        d="M5 12h14m-7 7V5"
                    />
                </svg>
                Persistent Volume
            </SecondaryButton>
        </template>
    </ActionSection>

    <ActionSection>
        <template #title> Public Access over the Internet </template>

        <template #description>
            <p>
                You can make certain ports of the containers accessible from the
                internet. All requests will be forwarded to your containers via
                Caddy.
            </p>
            <p>
                Currently you can expose only HTTP services via Caddy
                reverse-proxy.
            </p>
        </template>

        <template #tabs>
            <ProcessTabs
                v-model="state"
                :processes="model.processes"
                block="caddy"
            />
        </template>

        <template #content>
            <InputError />

            <div
                v-if="
                    model.processes[state.selectedProcessIndex['caddy']].caddy
                        .length === 0 &&
                    model.processes[state.selectedProcessIndex['caddy']].ports
                        .length === 0
                "
                class="col-span-full"
            >
                The service will be not exposed to the internet.
            </div>

            <h3
                v-if="
                    model.processes[state.selectedProcessIndex['caddy']].ports
                        .length > 0
                "
                class="col-span-full"
            >
                Ports
            </h3>
            <div class="col-span-full grid grid-cols-2 gap-4" v-auto-animate>
                <template
                    v-for="(port, index) in model.processes[
                        state.selectedProcessIndex['caddy']
                    ].ports"
                    :key="port.id"
                >
                    <div class="flex gap-2">
                        <FormField class="col-span-2">
                            <template #label>Target Port</template>

                            <TextInput
                                v-model="port.targetPort"
                                class="w-28"
                                placeholder="8080"
                            />
                        </FormField>

                        <FormField class="col-span-2">
                            <template #label>Published Port</template>
                            <div class="flex gap-2">
                                <TextInput
                                    v-model="port.publishedPort"
                                    class="w-28"
                                    placeholder="80"
                                />
                                <SecondaryButton
                                    @click="
                                        model.processes[
                                            state.selectedProcessIndex['caddy']
                                        ].ports.splice(index, 1)
                                    "
                                    tabindex="-1"
                                >
                                    <svg
                                        class="w-4 h-4 text-gray-800 dark:text-white"
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
                                            d="M5 12h14"
                                        />
                                    </svg>
                                </SecondaryButton>
                            </div>
                        </FormField>
                    </div>
                    <InputError
                        class="col-span-2"
                        :message="
                            props.errors[
                                `processes.${state.selectedProcessIndex['caddy']}.ports.${index}.targetPort`
                            ] ||
                            props.errors[
                                `processes.${state.selectedProcessIndex['caddy']}.ports.${index}.publishedPort`
                            ]
                        "
                    />
                </template>
            </div>

            <hr
                v-if="
                    model.processes[state.selectedProcessIndex['caddy']].ports
                        .length > 0 &&
                    model.processes[state.selectedProcessIndex['caddy']].caddy
                        .length > 0
                "
                class="col-span-full"
            />

            <h3
                v-if="
                    model.processes[state.selectedProcessIndex['caddy']].caddy
                        .length > 0
                "
                class="col-span-full"
            >
                Caddy
            </h3>
            <div
                v-for="(caddy, index) in model.processes[
                    state.selectedProcessIndex['caddy']
                ].caddy"
                :key="caddy.id"
                class="col-span-full grid grid-cols-6 gap-4"
            >
                <hr class="col-span-full" v-if="index > 0" />

                <FormField
                    class="col-span-2"
                    :error="
                        props.errors[
                            `processes.${state.selectedProcessIndex['caddy']}.caddy.${index}.targetProtocol`
                        ]
                    "
                >
                    <template #label> Container Protocol </template>

                    <Select v-model="caddy.targetProtocol">
                        <option value="http">HTTP</option>
                        <option value="fastcgi">FastCGI</option>
                    </Select>
                </FormField>

                <FormField
                    :error="
                        props.errors[
                            `processes.${state.selectedProcessIndex['caddy']}.caddy.${index}.targetPort`
                        ]
                    "
                >
                    <template #label>Container Port</template>

                    <TextInput
                        v-model="caddy.targetPort"
                        placeholder="8080"
                        class="w-full"
                    />
                </FormField>

                <FormField
                    class="col-span-2"
                    :error="
                        props.errors[
                            `processes.${state.selectedProcessIndex['caddy']}.caddy.${index}.publishedPort`
                        ]
                    "
                >
                    <template #label>Published Port</template>

                    <div class="flex gap-2">
                        <Select v-model="caddy.publishedPort">
                            <option value="443">HTTPS</option>
                            <option value="80">HTTP</option>
                        </Select>

                        <SecondaryButton
                            @click="
                                model.processes[
                                    state.selectedProcessIndex['caddy']
                                ].caddy.splice(index, 1)
                            "
                            tabindex="-1"
                            class="grow"
                        >
                            <svg
                                class="w-4 h-4 text-gray-800 dark:text-white"
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
                                    d="M5 12h14"
                                />
                            </svg>
                        </SecondaryButton>
                    </div>
                </FormField>

                <div class="col-span-1"></div>

                <FormField
                    class="col-span-2"
                    :error="
                        props.errors[
                            `processes.${state.selectedProcessIndex['caddy']}.caddy.${index}.domain`
                        ]
                    "
                >
                    <template #label>Domain</template>

                    <TextInput
                        v-model="caddy.domain"
                        class="w-full"
                        placeholder="example.com"
                    />
                </FormField>

                <FormField
                    class="col-span-4"
                    :error="
                        props.errors[
                            `processes.${state.selectedProcessIndex['caddy']}.caddy.${index}.path`
                        ]
                    "
                >
                    <template #label>Path</template>
                    <TextInput
                        v-model="caddy.path"
                        class="w-full"
                        placeholder="/*"
                    />
                </FormField>

                <!-- TODO: link to available placeholders docs https://caddyserver.com/docs/json/apps/http/#docs -->
                <ComponentBlock
                    v-model="
                        model.processes[state.selectedProcessIndex['caddy']]
                            .redirectRules
                    "
                    v-slot="{ item }"
                    label="Redirect Rules"
                    @remove="
                        model.processes[
                            state.selectedProcessIndex['caddy']
                        ].redirectRules.splice($event, 1)
                    "
                >
                    <FormField
                        :error="
                            props.errors[
                                `processes.${state.selectedProcessIndex['caddy']}.redirectRules.${item.$index}.domainFrom`
                            ]
                        "
                        class="col-span-2"
                    >
                        <template #label>Domain From</template>

                        <TextInput
                            v-model="item.domainFrom"
                            class="w-full"
                            placeholder="wp.example.com"
                        />
                    </FormField>

                    <FormField
                        :error="
                            props.errors[
                                `processes.${state.selectedProcessIndex['caddy']}.redirectRules.${item.$index}.domainTo`
                            ]
                        "
                        class="col-span-2"
                    >
                        <template #label>Domain To</template>

                        <TextInput
                            v-model="item.domainTo"
                            class="w-full"
                            placeholder="example.com"
                        />
                    </FormField>

                    <FormField
                        :error="
                            props.errors[
                                `processes.${state.selectedProcessIndex['caddy']}.redirectRules.${item.$index}.statusCode`
                            ]
                        "
                        class="col-span-2"
                    >
                        <template #label>Status Code</template>

                        <!-- TODO: link to MDN https://developer.mozilla.org/en-US/docs/Web/HTTP/Redirections -->
                        <Select v-model="item.statusCode">
                            <optgroup label="Permanent Redirects">
                                <option value="301">
                                    301 Moved Permanently
                                </option>
                                <option value="308">
                                    308 Permanent Redirect
                                </option>
                            </optgroup>
                            <optgroup label="Temporary Redirects">
                                <option value="302">302 Found</option>
                                <option value="307">
                                    307 Temporary Redirect
                                </option>
                            </optgroup>
                        </Select>
                    </FormField>

                    <FormField
                        :error="
                            props.errors[
                                `processes.${state.selectedProcessIndex['caddy']}.redirectRules.${item.$index}.pathFrom`
                            ]
                        "
                        class="col-span-3"
                    >
                        <template #label>Path From</template>

                        <TextInput
                            v-model="item.pathFrom"
                            class="w-full"
                            placeholder="/(.*)"
                        />
                    </FormField>

                    <FormField
                        :error="
                            props.errors[
                                `processes.${state.selectedProcessIndex['caddy']}.redirectRules.${item.$index}.pathTo`
                            ]
                        "
                        class="col-span-3"
                    >
                        <template #label>Path To</template>

                        <TextInput
                            v-model="item.pathTo"
                            class="w-full"
                            placeholder="/blog/$1"
                        />
                    </FormField>
                </ComponentBlock>

                <ComponentBlock
                    v-model="
                        model.processes[state.selectedProcessIndex['caddy']]
                            .rewriteRules
                    "
                    v-slot="{ item }"
                    label="Rewrite Rules"
                    @remove="
                        model.processes[
                            state.selectedProcessIndex['caddy']
                        ].rewriteRules.splice($event, 1)
                    "
                >
                    <FormField
                        :error="
                            props.errors[
                                `processes.${state.selectedProcessIndex['caddy']}.rewriteRules.${item.$index}.pathFrom`
                            ]
                        "
                        class="col-span-3"
                    >
                        <template #label>Path From</template>

                        <TextInput
                            v-model="item.pathFrom"
                            class="w-full"
                            placeholder="/old-path/(.*)"
                        />
                    </FormField>

                    <FormField
                        :error="
                            props.errors[
                                `processes.${state.selectedProcessIndex['caddy']}.rewriteRules.${item.$index}.pathTo`
                            ]
                        "
                        class="col-span-3"
                    >
                        <template #label>Path To</template>

                        <TextInput
                            v-model="item.pathTo"
                            class="w-full"
                            placeholder="/new-path/$1"
                        />
                    </FormField>
                </ComponentBlock>
            </div>

            <template v-if="hasFastCgiHandlers">
                <hr class="col-span-full" />

                <FormField
                    class="col-span-3"
                    :error="
                        props.errors[
                            `processes.${state.selectedProcessIndex['caddy']}.fastCgi.root`
                        ]
                    "
                >
                    <template #label>FastCGI Root</template>

                    <TextInput
                        v-model="
                            model.processes[state.selectedProcessIndex['caddy']]
                                .fastCgi.root
                        "
                        class="w-full"
                        placeholder="/app/public"
                    />
                </FormField>

                <div class="col-span-3"></div>

                <template
                    v-for="(fastcgiVar, index) in model.processes[
                        state.selectedProcessIndex['caddy']
                    ].fastCgi.env"
                    :key="fastcgiVar.id"
                >
                    <div class="col-span-full grid grid-cols-6 gap-2">
                        <FormField
                            class="col-span-2"
                            :error="
                                props.errors[
                                    `processes.${state.selectedProcessIndex['caddy']}.fastCgi.env.${index}.name`
                                ]
                            "
                        >
                            <template #label v-if="index === 0"
                                >FastCGI Variable Name</template
                            >

                            <TextInput
                                v-model="fastcgiVar.name"
                                class="w-full"
                                placeholder="SCRIPT_FILENAME"
                            />
                        </FormField>

                        <FormField
                            class="col-span-4"
                            :error="
                                props.errors[
                                    `processes.${state.selectedProcessIndex['caddy']}.fastCgi.env.${index}.value`
                                ]
                            "
                        >
                            <template #label v-if="index === 0">Value</template>

                            <div class="flex gap-2">
                                <TextInput
                                    v-model="fastcgiVar.value"
                                    class="grow"
                                    placeholder="/app/public/index.php"
                                />

                                <SecondaryButton
                                    @click="
                                        model.processes[
                                            state.selectedProcessIndex['caddy']
                                        ].fastCgi.env.splice(index, 1)
                                    "
                                    tabindex="-1"
                                >
                                    <svg
                                        class="w-4 h-4 text-gray-800 dark:text-white"
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
                                            d="M5 12h14"
                                        />
                                    </svg>
                                </SecondaryButton>
                            </div>
                        </FormField>
                    </div>
                </template>
            </template>
        </template>

        <template #actions>
            <SecondaryButton @click="addPort">
                <svg
                    class="w-4 h-4 me-2 -ms-1 text-gray-800 dark:text-white"
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
                        d="M5 12h14m-7 7V5"
                    />
                </svg>
                Port
            </SecondaryButton>

            <SecondaryButton @click="addCaddy">
                <svg
                    class="w-4 h-4 me-2 -ms-1 text-gray-800 dark:text-white"
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
                        d="M5 12h14m-7 7V5"
                    />
                </svg>
                HTTP(S)
            </SecondaryButton>

            <AddComponentButton
                v-if="
                    model.processes[state.selectedProcessIndex['caddy']].caddy
                        .length > 0
                "
                @click="addRedirectRule"
            >
                Redirect Rule
            </AddComponentButton>
            <AddComponentButton
                v-if="
                    model.processes[state.selectedProcessIndex['caddy']].caddy
                        .length > 0
                "
                @click="addRewriteRule"
            >
                Rewrite Rule
            </AddComponentButton>
            <SecondaryButton v-if="hasFastCgiHandlers" @click="addFastCgiVar">
                <svg
                    class="w-4 h-4 me-2 -ms-1 text-gray-800 dark:text-white"
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
                        d="M5 12h14m-7 7V5"
                    />
                </svg>

                FastCGI Variable
            </SecondaryButton>
        </template>
    </ActionSection>

    <DialogModal :show="processRemoval.open" @close="closeProcessRemovalModal">
        <template #title> Delete Service </template>

        <template #content>
            Are you sure you want to delete
            <b
                ><code>{{
                    model.processes[state.selectedProcessIndex["processes"]]
                        .name
                }}</code></b
            >? Once the process is deleted, all of its resources and data will
            be permanently deleted. Please enter the process name to confirm you
            would like to permanently delete it.

            <div class="mt-4" v-auto-animate>
                <TextInput
                    ref="serviceDeleteInput"
                    v-model="processRemoval.processName"
                    class="mt-1 block w-3/4"
                    :placeholder="
                        model.processes[state.selectedProcessIndex['processes']]
                            .name
                    "
                    @keyup.enter="submitProcessRemoval"
                />

                <InputError :message="processRemoval.error" class="mt-2" />
            </div>
        </template>

        <template #footer>
            <SecondaryButton @click="closeProcessRemovalModal">
                Cancel
            </SecondaryButton>

            <DangerButton class="ms-3" @click="submitProcessRemoval">
                Delete Process
            </DangerButton>
        </template>
    </DialogModal>
</template>
