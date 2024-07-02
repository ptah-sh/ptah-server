
<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from "@/Components/InputError.vue";
import FormSection from "@/Components/FormSection.vue";
import TextInput from "@/Components/TextInput.vue";
import {useForm} from "@inertiajs/vue3";

const props = defineProps({
    node: Object
});

const form = useForm({
    name: props.node.name,
})
</script>

<template>
    <FormSection>
        <template #title>
            Server Details
        </template>

        <template #description>
            <p>You can change the name of your server.</p>
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-4" v-auto-animate>
                <InputLabel for="server_name" value="Server Name" />
                <TextInput id="server_name"
                           v-model="form.name"
                           class="block w-full mt-1"
                />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>
        </template>

        <template #submit>
            <PrimaryButton :disabled="form.processing">
                Save
            </PrimaryButton>
        </template>
    </FormSection>
</template>
