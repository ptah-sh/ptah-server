<script setup>
import FormField from "@/Components/FormField.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import Select from "@/Components/Select.vue";
import ComponentBlock from "@/Components/Service/ComponentBlock.vue";
import TextInput from "@/Components/TextInput.vue";
import { makeId } from "@/id";

const props = defineProps({
    errors: Object,
});

const model = defineModel();

defineEmits("delete");

const addRedirectRule = () => {
    model.value.redirectRules.push({
        id: makeId("redirect-rule"),
        domainFrom: "",
        domainTo: "",
        pathFrom: "",
        pathTo: "",
        statusCode: 301,
    });
};

const addRewriteRule = () => {
    model.value.rewriteRules.push({
        id: makeId("rewrite-rule"),
        pathFrom: "",
        pathTo: "",
    });
};
</script>

<template>
    <div class="col-span-full grid grid-cols-6 gap-4">
        <FormField class="col-span-2" :error="errors['targetProtocol']">
            <template #label> Container Protocol </template>

            <Select v-model="model.targetProtocol">
                <option value="http">HTTP</option>
                <option value="fastcgi">FastCGI</option>
            </Select>
        </FormField>

        <FormField :error="props.errors['targetPort']">
            <template #label>Container Port</template>

            <TextInput
                v-model="model.targetPort"
                placeholder="8080"
                class="w-full"
            />
        </FormField>

        <FormField class="col-span-2" :error="errors['publishedPort']">
            <template #label>Published Port</template>

            <div class="flex gap-2">
                <Select v-model="model.publishedPort">
                    <option value="443">HTTPS</option>
                    <option value="80">HTTP</option>
                </Select>

                <SecondaryButton
                    @click="$emit('delete')"
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

        <FormField class="col-span-2" :error="errors['domain']">
            <template #label>Domain</template>

            <TextInput
                v-model="model.domain"
                class="w-full"
                placeholder="example.com"
            />
        </FormField>

        <FormField class="col-span-4" :error="errors['path']">
            <template #label>Path</template>

            <TextInput v-model="model.path" class="w-full" placeholder="/*" />
        </FormField>

        <!-- TODO: link to available placeholders docs https://caddyserver.com/docs/json/apps/http/#docs -->
        <ComponentBlock
            v-model="model.redirectRules"
            v-slot="{ item, $index }"
            label="Redirect Rules"
            @remove="model.redirectRules.splice($event, 1)"
            @add="addRedirectRule"
        >
            <FormField
                :error="props.errors['redirectRules.' + $index + '.domainFrom']"
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
                :error="props.errors['redirectRules.' + $index + '.domainTo']"
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
                :error="props.errors['redirectRules.' + $index + '.statusCode']"
                class="col-span-2"
            >
                <template #label>Status Code</template>

                <!-- TODO: link to MDN https://developer.mozilla.org/en-US/docs/Web/HTTP/Redirections -->
                <Select v-model="item.statusCode">
                    <optgroup label="Permanent Redirects">
                        <option value="301">301 Moved Permanently</option>
                        <option value="308">308 Permanent Redirect</option>
                    </optgroup>
                    <optgroup label="Temporary Redirects">
                        <option value="302">302 Found</option>
                        <option value="307">307 Temporary Redirect</option>
                    </optgroup>
                </Select>
            </FormField>

            <FormField
                :error="props.errors['redirectRules.' + $index + '.pathFrom']"
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
                :error="props.errors['redirectRules.' + $index + '.pathTo']"
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
            v-model="model.rewriteRules"
            v-slot="{ item, $index }"
            label="Rewrite Rules"
            @remove="model.rewriteRules.splice($event, 1)"
            @add="addRewriteRule"
        >
            <FormField
                :error="props.errors['rewriteRules.' + $index + '.pathFrom']"
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
                :error="props.errors['rewriteRules.' + $index + '.pathTo']"
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
</template>
