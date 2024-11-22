<script setup>
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import ActionSection from "@/Components/ActionSection.vue";
import FormField from "@/Components/FormField.vue";
import Select from "@/Components/Select.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextArea from "@/Components/TextArea.vue";
import { computed, effect, nextTick, reactive, ref } from "vue";
import ProcessTabs from "@/Pages/Services/Partials/ProcessTabs.vue";
import DangerButton from "@/Components/DangerButton.vue";
import DialogModal from "@/Components/DialogModal.vue";
import WorkerForm from "@/Pages/Services/Partials/DeploymentData/WorkerForm.vue";
import ComponentBlock from "@/Components/Service/ComponentBlock.vue";
import { evaluate } from "@/expr-lang.js";
import ExternalLink from "@/Components/ExternalLink.vue";
import cloneDeep from "lodash.clonedeep";
import CloseButton from "@/Components/CloseButton.vue";
import CaddyForm from "./DeploymentData/CaddyForm.vue";

const model = defineModel();

const props = defineProps({
    networks: Array,
    nodes: Array,
    serviceName: String | undefined,
    dockerRegistries: Array,
    s3Storages: Array,
    errors: Object,
    initialSecretVars: Array,
    blankProcess: Object,
    blankWorker: Object,
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
        workers: 0,
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
    ].secretVars.push({ id: makeId("secret-var"), name: "", value: "" });
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
        rewriteRules: [],
        redirectRules: [],
    });
};

const addFastCgiVar = () => {
    model.value.processes[state.selectedProcessIndex["caddy"]].fastCgi.env.push(
        { id: makeId("fastcgi"), name: "", value: "" },
    );
};

const addProcess = () => {
    const newIndex = model.value.processes.length;

    const newProcess = cloneDeep(props.blankProcess);

    newProcess.id = makeId("process");
    newProcess.name = "process_" + newIndex;

    model.value.processes.push(newProcess);

    state.selectedProcessIndex["processes"] = newIndex;
    state.selectedProcessIndex["workers"] = 0;
};

const removeProcess = (index) => {
    model.value.processes.splice(index, 1);

    for (const [key, selectedIndex] of Object.entries(
        state.selectedProcessIndex,
    )) {
        if (selectedIndex >= index) {
            state.selectedProcessIndex[key] = model.value.processes.length - 1;
        }
    }

    state.selectedProcessIndex["workers"] = 0;
};

const addWorker = () => {
    const newWorker = cloneDeep(props.blankWorker);

    newWorker.id = makeId("worker");
    newWorker.name = `worker_${model.value.processes[state.selectedProcessIndex["processes"]].workers.length + 1}`;

    model.value.processes[state.selectedProcessIndex["processes"]].workers.push(
        newWorker,
    );

    state.selectedProcessIndex["workers"] =
        model.value.processes[state.selectedProcessIndex["processes"]].workers
            .length - 1;
};

const removeWorker = (index) => {
    selectedProcess.value.workers.splice(index, 1);

    const lastWorkerIndex = selectedProcess.value.workers.length - 1;
    if (state.selectedProcessIndex["workers"] > lastWorkerIndex) {
        state.selectedProcessIndex["workers"] = lastWorkerIndex;
    }
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

const processRemoveInput = ref();

const processRemoval = reactive({
    open: false,
    processName: "",
    error: "",
});

const confirmProcessRemoval = (index) => {
    state.selectedProcessIndex["processes"] = index;
    state.selectedProcessIndex["workers"] = 0;

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

const errors = ref({});

const evaluateEnvVarTemplate = (envVar, index) => {
    if (envVar.value) {
        try {
            envVar.value = evaluate(envVar.value, model.value);

            delete errors.value[
                `processes.${state.selectedProcessIndex["envVars"]}.envVars.${index}.value`
            ];
        } catch (error) {
            console.error("Error evaluating env var template:", error);

            errors.value[
                `processes.${state.selectedProcessIndex["envVars"]}.envVars.${index}.value`
            ] = error.message;
        }
    }
};

const evaluateSecretVarTemplate = (secretVar, index) => {
    if (secretVar.value) {
        try {
            secretVar.value = evaluate(secretVar.value, model.value);

            delete errors.value[
                `processes.${state.selectedProcessIndex["secretVars"]}.secretVars.${index}.value`
            ];
        } catch (error) {
            console.error("Error evaluating secret var template:", error);

            errors.value[
                `processes.${state.selectedProcessIndex["secretVars"]}.secretVars.${index}.value`
            ] = error.message;
        }
    }
};

const selectedProcess = computed(() => {
    return model.value.processes[state.selectedProcessIndex["processes"]];
});

const selectedProcessKey = computed(() => {
    return `processes.${state.selectedProcessIndex["processes"]}`;
});

const selectedWorker = computed(() => {
    return selectedProcess.value.workers[state.selectedProcessIndex["workers"]];
});

const selectedWorkerKey = computed(() => {
    return `${selectedProcessKey.value}.workers.${state.selectedProcessIndex["workers"]}`;
});

const selectedWorkerErrors = computed(() => {
    return extractFieldErrors(selectedWorkerKey.value);
});
</script>

<template>
    <ActionSection>
        <template #title>Service Location</template>

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
                    :closable="true"
                    @close="confirmProcessRemoval"
                    @change="state.selectedProcessIndex['workers'] = 0"
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
                class="col-span-2 relative"
                :error="
                    props.errors[
                        `processes.${state.selectedProcessIndex['processes']}.name`
                    ]
                "
            >
                <template #label>Process Name</template>

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
                :error="
                    props.errors[
                        `processes.${state.selectedProcessIndex['processes']}.placementNodeId`
                    ]
                "
                class="col-span-2"
            >
                <template #label>Placement Node</template>

                <Select
                    v-model="
                        model.processes[state.selectedProcessIndex['processes']]
                            .placementNodeId
                    "
                >
                    <option :value="null">Run on all Nodes</option>
                    <option v-for="node in $page.props.nodes" :value="node.id">
                        {{ node.name }}
                    </option>
                </Select>
            </FormField>

            <div class="flex flex-col col-span-full">
                <label
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >Workers</label
                >
                <div class="flex flex-wrap gap-2">
                    <div
                        class="inline-flex rounded-md shadow-sm"
                        role="group"
                        v-auto-animate
                    >
                        <template
                            v-for="(worker, index) in selectedProcess.workers"
                            :key="worker.id"
                        >
                            <button
                                @click="
                                    state.selectedProcessIndex['workers'] =
                                        index
                                "
                                type="button"
                                :class="[
                                    'px-4 py-2 text-sm font-medium border border-gray-200 first:rounded-l-lg focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 relative group last:rounded-r-lg',
                                    state.selectedProcessIndex['workers'] ===
                                    index
                                        ? 'bg-gray-100 text-blue-700 dark:bg-gray-600 dark:text-white'
                                        : 'bg-white text-gray-900 hover:bg-gray-100 hover:text-blue-700 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white',
                                ]"
                            >
                                <span
                                    v-if="worker.name.length === 0"
                                    class="italic"
                                >
                                    unnamed
                                </span>
                                <span v-else>
                                    {{ worker.name }}
                                </span>
                                <CloseButton
                                    v-if="index > 0"
                                    @click.stop="removeWorker(index)"
                                />
                            </button>
                        </template>
                    </div>
                    <button
                        @click="addWorker"
                        type="button"
                        class="px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700"
                    >
                        + worker
                    </button>
                </div>
            </div>

            <WorkerForm
                v-model="selectedWorker"
                :errors="selectedWorkerErrors"
                :dockerRegistries="dockerRegistries"
                :s3Storages="s3Storages"
            />
        </template>
    </ActionSection>

    <ActionSection>
        <template #title>Environment Variables</template>

        <template #description>
            <p>
                Add environment variables to the service. These variables will
                be stored on the Ptah.sh database and will be fully accessible
                to edit them via UI.
            </p>
            <p>
                Values of environment variables support the Ptah.sh expression
                language. You can use built-in functions to generate or
                manipulate values.
                <ExternalLink
                    href="https://ptah.sh/concepts/expression-language/"
                >
                    Learn more about the expression language
                </ExternalLink>
            </p>
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
                    ] ||
                    errors[
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
                        @blur="evaluateEnvVarTemplate(envVar, index)"
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
            <p>
                Values of secret variables also support the Ptah.sh expression
                language, allowing you to use built-in functions.
                <ExternalLink
                    href="https://ptah.sh/concepts/expression-language/"
                >
                    Learn more about the expression language
                </ExternalLink>
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
                        .secretVars.length === 0
                "
                class="col-span-full"
            >
                No Secret Variables defined
            </div>
            <FormField
                v-else
                v-for="(secretVar, index) in model.processes[
                    state.selectedProcessIndex['secretVars']
                ].secretVars"
                :key="secretVar.id"
                :error="
                    props.errors[
                        `processes.${state.selectedProcessIndex['secretVars']}.secretVars.${index}.name`
                    ] ||
                    props.errors[
                        `processes.${state.selectedProcessIndex['secretVars']}.secretVars.${index}.value`
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
                        autocomplete="off"
                        :placeholder="
                            initialSecretVars.includes(secretVar.name)
                                ? 'keep existing secret value'
                                : 'Value'
                        "
                        @blur="evaluateSecretVarTemplate(secretVar, index)"
                    />

                    <SecondaryButton
                        @click="
                            model.processes[
                                state.selectedProcessIndex['secretVars']
                            ].secretVars.splice(index, 1)
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
                        :placeholder="
                            secretFile.dockerName
                                ? 'keep existing secret'
                                : 'enter secret content'
                        "
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
        <template #title>Persistent Volumes</template>

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
                    class="col-span-4"
                >
                    <template #label>Mount Path</template>

                    <TextInput
                        v-model="item.path"
                        class="w-full"
                        placeholder="Path"
                    />
                </FormField>
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
                You can expose any web service via Caddy reverse-proxy by
                clicking <strong>HTTP(S)</strong> button.
            </p>
            <p>
                Certain ports can be exposed to the internet. Use this in
                limited cases, e.g. for databases or non-HTTP services.
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

            <template
                v-for="(caddy, index) in model.processes[
                    state.selectedProcessIndex['caddy']
                ].caddy"
                :key="caddy.id"
            >
                <hr class="col-span-full" v-if="index > 0" />

                <CaddyForm
                    v-model="
                        model.processes[state.selectedProcessIndex['caddy']]
                            .caddy[index]
                    "
                    :errors="
                        extractFieldErrors(
                            `processes.${state.selectedProcessIndex['caddy']}.caddy.${index}`,
                        )
                    "
                    @delete="
                        model.processes[
                            state.selectedProcessIndex['caddy']
                        ].caddy.splice(index, 1)
                    "
                />

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
                                model.processes[
                                    state.selectedProcessIndex['caddy']
                                ].fastCgi.root
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
                                <template #label v-if="index === 0"
                                    >Value</template
                                >

                                <div class="flex gap-2">
                                    <TextInput
                                        v-model="fastcgiVar.value"
                                        class="grow"
                                        placeholder="/app/public/index.php"
                                    />

                                    <SecondaryButton
                                        @click="
                                            model.processes[
                                                state.selectedProcessIndex[
                                                    'caddy'
                                                ]
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
            <form
                id="delete-process-form"
                @submit.prevent="submitProcessRemoval"
            >
                Are you sure you want to delete
                <b>
                    <code>{{
                        model.processes[state.selectedProcessIndex["processes"]]
                            .name
                    }}</code> </b
                >? Once the process is deleted, all of its resources and data
                will be permanently deleted. Please enter the process name to
                confirm you would like to permanently delete it.

                <div class="mt-4" v-auto-animate>
                    <TextInput
                        ref="serviceDeleteInput"
                        v-model="processRemoval.processName"
                        class="mt-1 block w-3/4"
                        :placeholder="
                            model.processes[
                                state.selectedProcessIndex['processes']
                            ].name
                        "
                    />

                    <InputError :message="processRemoval.error" class="mt-2" />
                </div>
            </form>
        </template>

        <template #footer>
            <SecondaryButton @click="closeProcessRemovalModal">
                Cancel
            </SecondaryButton>

            <DangerButton form="delete-process-form" class="ms-3">
                Delete Process
            </DangerButton>
        </template>
    </DialogModal>
</template>
