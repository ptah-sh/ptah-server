<script setup>
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { computed, h, reactive, markRaw, ref, onMounted, effect } from "vue";
import ExternalLink from "@/Components/ExternalLink.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import FormField from "@/Components/FormField.vue";
import { makeId } from "@/id.js";
import useSWRV, { mutate } from "swrv";

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
    } else {
        state.extends = [];
    }

    state.step = "configure";
};

const fillPlaceholders = (slug, value) => {
    return Object.entries({
        ...form.data,
        "service/internalDomainName":
            state.template.slug.replace(/[^a-zA-Z0-9]/g, "-") + ".local",
    }).reduce((acc, [key, value]) => {
        return acc
            .replace(`{{${key.replace(`${slug}/`, "")}}}`, value)
            .replace(`{{${key}}}`, value);
    }, value);
};

const mapProcessTemplate = (templateSlug, process, newIndex) => {
    const envVars = process.data.envVars.map((envVar) => {
        return {
            name: envVar.name,
            value: fillPlaceholders(templateSlug, envVar.value),
        };
    });

    const caddy =
        process.data.caddy?.map((caddy) => {
            return {
                ...caddy,
                id: makeId("caddy"),
                domain: fillPlaceholders(templateSlug, caddy.domain),
            };
        }) || [];

    const secretVars =
        process.data.secretVars?.vars.map((secretVar) => {
            return {
                name: secretVar.name,
                value: fillPlaceholders(templateSlug, secretVar.value),
            };
        }) || [];

    return {
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
        configFiles: [],
        secretFiles: [],
        ports: [],
        replicas: 1,
        redirectRules: [],
        fastCgi: null,
        ...process.data,
        volumes:
            process.data.volumes?.map((volume) => {
                return {
                    ...volume,
                    id: makeId("volume"),
                };
            }) || [],
        envVars,
        secretVars: {
            vars: secretVars,
        },
        caddy,
    };
};

const applyTemplate = () => {
    const extendedTemplates = state.extends.reduce((acc, template) => {
        return [
            ...acc,
            ...template.processes.map((process, index) =>
                mapProcessTemplate(template.slug, process, index),
            ),
        ];
    }, []);

    const processes = [
        ...extendedTemplates,
        ...state.template.processes.map((process, index) =>
            mapProcessTemplate(state.template.slug, process, index),
        ),
    ];

    emit("apply", {
        name: state.template.slug.replace(/[^a-zA-Z0-9]/g, "-"),
        deploymentData: {
            processes,
        },
    });
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
                                <ExternalLink :href="process.url">{{
                                    process.dockerImage
                                }}</ExternalLink>
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

                <template
                    v-for="(_descriptor, prop) in state.template.form.schema
                        .properties"
                >
                    <FormField class="my-2" :error="form.errors[prop]">
                        <template #label>{{ prop }}</template>

                        <TextInput
                            v-model="
                                form.data[`${state.template.slug}/${prop}`]
                            "
                            class="w-full"
                        />
                    </FormField>
                </template>

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

                    <template
                        v-for="(_descriptor, prop) in template.form.schema
                            .properties"
                    >
                        <FormField class="my-2" :error="form.errors[prop]">
                            <template #label>{{ prop }}</template>

                            <TextInput
                                v-model="form.data[`${template.slug}/${prop}`]"
                                class="w-full"
                            />
                        </FormField>
                    </template>
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
