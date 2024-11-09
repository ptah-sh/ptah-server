<script setup>
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { reactive, effect } from "vue";
import ExternalLink from "@/Components/ExternalLink.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { makeId } from "@/id.js";
import useSWRV from "swrv";
import DynamicForm from "./DynamicForm.vue";
import set from "lodash.set";
import { evaluate } from "@/expr-lang.js";

const props = defineProps({
    marketplaceUrl: String,
    show: Boolean,
});

const state = reactive({
    category: null,
    step: "select",
    template: {},
    extends: [],
});

const marketplaceCategories = useSWRV(props.marketplaceUrl + ".json");
const marketplaceApps = useSWRV(() =>
    state.category
        ? props.marketplaceUrl + "/" + state.category + ".json"
        : false,
);

const emit = defineEmits(["close", "apply"]);

effect(() => {
    if (state.category === null) {
        state.category =
            marketplaceCategories.data.value?.length > 0
                ? marketplaceCategories.data.value[0].slug
                : null;
    }
});

const form = reactive({
    errors: {},
    data: {},
});

const selectTemplate = async (template) => {
    form.errors = {};
    form.data = {};
    state.extends = [];

    state.template = await (
        await fetch(props.marketplaceUrl + "/" + template.slug + ".json")
    ).json();

    if (state.template.extends) {
        for (const extend of state.template.extends) {
            const res = await (
                await fetch(props.marketplaceUrl + "/" + extend.slug + ".json")
            ).json();

            state.extends.push(res);
        }
    }

    populateDefaultValues(state.template.form, state.template.slug);
    state.extends.forEach((extendedTemplate) => {
        populateDefaultValues(extendedTemplate.form, extendedTemplate.slug);
    });

    state.step = "configure";
};

const populateDefaultValues = (schema, scope) => {
    if (Array.isArray(schema.items)) {
        schema.items.forEach((item) => populateDefaultValues(item, scope));
    } else if (schema.default != null) {
        const itemName = `${scope}/${schema.name}`;

        form.data[itemName] = schema.default;
    }
};

function convertToNestedObject(flatObj) {
    const result = {};

    Object.entries(flatObj).forEach(([key, value]) => {
        const path = key.replace(/\//g, ".");

        set(result, path, value);
    });

    return result;
}

const evaluateForm = (form) => {
    const entries = Object.entries(form).map(([key, value]) => {
        return [key, evaluate(value, {})];
    });

    return Object.fromEntries(entries);
};

const fillPlaceholders = (formData, slug, value) => {
    const scopedFormData = Object.fromEntries(
        Object.entries(formData).map(([key, value]) => {
            return [key.replace(slug + "/", ""), value];
        }),
    );

    const templateData = convertToNestedObject({
        ...scopedFormData,
        service: {
            internalDomainName:
                state.template.slug.replace(/[^a-zA-Z0-9]/g, "-") + ".local",
        },
    });

    return evaluate(value, templateData);
};

const mapProcessTemplate = (formData, templateSlug, process, newIndex) => {
    const envVars = process.data.envVars.map((envVar) => {
        return {
            name: envVar.name,
            value: fillPlaceholders(formData, templateSlug, envVar.value),
        };
    });

    const caddy =
        process.data.caddy?.map((caddy) => {
            return {
                ...caddy,
                id: makeId("caddy"),
                domain: fillPlaceholders(formData, templateSlug, caddy.domain),
            };
        }) || [];

    const secretVars =
        process.data.secretVars?.map((secretVar) => {
            return {
                name: secretVar.name,
                value: fillPlaceholders(
                    formData,
                    templateSlug,
                    secretVar.value,
                ),
            };
        }) || [];

    const configFiles = process.data.configFiles
        ? process.data.configFiles.map((configFile) => {
              return {
                  ...configFile,
                  content: fillPlaceholders(
                      formData,
                      templateSlug,
                      configFile.content,
                  ),
              };
          })
        : [];

    return {
        id: makeId("process"),
        name: "process_" + newIndex,
        placementNodeId: null,
        secretFiles: [],
        ports: [],
        redirectRules: [],
        fastCgi: null,
        ...process.data,
        configFiles,
        workers: process.data.workers.map((worker, idx) => {
            return {
                id: makeId("worker"),
                launchMode: "daemon",
                replicas: 1,
                command: null,
                ...worker,
                name: idx === 0 ? "main" : worker.name || "worker_" + idx,
                releaseCommand: {
                    command: null,
                    ...worker.releaseCommand,
                },
                healthcheck: {
                    interval: 10,
                    timeout: 5,
                    retries: 10,
                    startPeriod: 60,
                    startInterval: 10,
                    command: null,
                    ...worker.healthcheck,
                },
            };
        }),
        rewriteRules:
            process.data.rewriteRules?.map((rule) => {
                return {
                    ...rule,
                    id: makeId("rewrite-rule"),
                };
            }) || [],
        volumes:
            process.data.volumes?.map((volume) => {
                return {
                    ...volume,
                    id: makeId("volume"),
                };
            }) || [],
        envVars,
        secretVars,
        caddy,
    };
};

const applyTemplate = () => {
    form.errors = {};

    validateForm(
        form.data,
        state.template.form,
        form.errors,
        state.template.slug,
    );
    state.extends.forEach((template) => {
        validateForm(form.data, template.form, form.errors, template.slug);
    });

    if (Object.keys(form.errors).length > 0) {
        return;
    }

    const formData = evaluateForm(form.data);

    const extendedTemplates = state.extends.reduce((acc, template) => {
        return [
            ...acc,
            ...template.processes.map((process, index) =>
                mapProcessTemplate(formData, template.slug, process, index),
            ),
        ];
    }, []);

    const processes = [
        ...extendedTemplates,
        ...state.template.processes.map((process, index) =>
            mapProcessTemplate(formData, state.template.slug, process, index),
        ),
    ];

    emit("apply", {
        name: state.template.slug.replace(/[^a-zA-Z0-9]/g, "-"),
        deploymentData: {
            processes,
        },
    });
};

const validateForm = (formData, schema, errors, scope) => {
    if (Array.isArray(schema.items)) {
        for (const item of schema.items) {
            validateForm(formData, item, errors, scope);
        }
    } else {
        const itemName = `${scope}/${schema.name}`;

        if (
            schema.required &&
            schema.format === "string" &&
            (formData[itemName] == null || formData[itemName].trim() === "")
        ) {
            errors[itemName] =
                `The ${schema.label.toLowerCase()} field is required`;
        }
    }

    return Object.keys(errors).length === 0;
};
</script>

<template>
    <ConfirmationModal :show="show" @close="$emit('close')">
        <template #title
            >Select a pre-configured template for your service<br /><small
                class="text-gray-400"
                >Note: all your changes will be overwritten.</small
            ></template
        >
        <template #content>
            <div v-if="state.step === 'select'">
                <ul class="grid gap-4 grid-cols-2">
                    <li
                        class="w-full"
                        v-for="category in marketplaceCategories.data.value"
                    >
                        <input
                            type="radio"
                            :id="category.slug"
                            name="category"
                            :value="category.slug"
                            v-model="state.category"
                            class="hidden peer"
                            required
                        />
                        <label
                            :for="category.slug"
                            class="inline-flex items-center justify-between w-full py-2 px-4 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-blue-500 peer-checked:border-blue-600 peer-checked:text-blue-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700"
                        >
                            <span class="block">
                                <span
                                    class="block font-medium text-gray-900 dark:text-gray-300"
                                    >{{ category.name }}
                                    <span
                                        class="text-xs font-normal text-gray-500 dark:text-gray-300"
                                        >({{ category.apps }})</span
                                    ></span
                                >
                                <span
                                    class="text-xs font-normal text-gray-500 dark:text-gray-300"
                                    >{{ category.description }}</span
                                >
                            </span>
                        </label>
                    </li>
                </ul>

                <div v-auto-animate>
                    <div
                        class="mt-4 border border-gray-200 rounded-lg p-4 relative hover:bg-gray-50 group"
                        v-for="template in marketplaceApps.data.value"
                        :key="template.slug"
                    >
                        <div
                            class="absolute right-4 top-4 hidden group-hover:flex items-center space-x-2"
                        >
                            <ExternalLink
                                :href="
                                    'https://ptah.sh/marketplace/' +
                                    template.slug +
                                    '/'
                                "
                                >View</ExternalLink
                            >
                            <PrimaryButton @click="selectTemplate(template)"
                                >Select</PrimaryButton
                            >
                        </div>

                        <p class="font-medium text-gray-900 dark:text-gray-300">
                            {{ template.name }}
                        </p>
                        <p
                            class="text-xs font-normal text-gray-500 dark:text-gray-300"
                        >
                            {{ template.description }}
                        </p>

                        <div class="mt-4">
                            <p
                                class="text-sm font-medium text-gray-900 dark:text-gray-300"
                            >
                                Processes
                            </p>
                        </div>
                        <ul class="grid gap-4 grid-cols-2">
                            <li
                                v-for="process in template.processes"
                                :key="process.slug"
                            >
                                <p
                                    class="font-medium text-gray-900 dark:text-gray-300"
                                >
                                    {{ process.name }}
                                </p>
                                <p
                                    class="text-xs font-normal text-gray-500 dark:text-gray-300"
                                >
                                    {{ process.description }}
                                </p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div v-if="state.step === 'configure'">
                <button
                    @click="state.step = 'select'"
                    class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 cursor-pointer underline"
                >
                    <span class="font-medium text-gray-900 dark:text-gray-300"
                        >Select another template</span
                    >
                </button>

                <p
                    class="text-sm font-medium text-gray-900 dark:text-gray-300 mt-4"
                >
                    {{ state.template.name }}
                </p>
                <p
                    class="text-xs font-normal text-gray-500 dark:text-gray-300 mt-1"
                >
                    {{ state.template.description }}
                </p>

                <DynamicForm
                    :item="state.template.form"
                    :form="form.data"
                    :scope="state.template.slug"
                    :errors="form.errors"
                />

                <div v-for="template in state.extends">
                    <p
                        class="text-sm font-medium text-gray-900 dark:text-gray-300 mt-4"
                    >
                        {{ template.name }}
                    </p>
                    <p
                        class="text-xs font-normal text-gray-500 dark:text-gray-300 mt-1"
                    >
                        {{ template.description }}
                    </p>

                    <DynamicForm
                        :item="template.form"
                        :scope="template.slug"
                        :form="form.data"
                        :errors="form.errors"
                    />
                </div>
            </div>
        </template>

        <template #footer>
            <SecondaryButton @click="$emit('close')">Cancel</SecondaryButton>
            <PrimaryButton
                :disabled="state.template === null"
                @click="applyTemplate()"
                >Apply</PrimaryButton
            >
        </template>
    </ConfirmationModal>
</template>
