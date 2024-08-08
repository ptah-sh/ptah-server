<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import ActionSection from "@/Components/ActionSection.vue";
import TextInput from "@/Components/TextInput.vue";
import { onMounted, ref } from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import { router, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import FormSection from "@/Components/FormSection.vue";
import TeamCard from "@/Components/TeamCard.vue";
import PaddleButton from "@/Components/PaddleButton.vue";

const props = defineProps({
    checkout: Object,
});

const form = useForm({
    name: "",
    // swarm_option: 'init',
    // swarm_init: {
    //     listen_addr: '',
    //     advertise_addr: '',
    //     force_new_cluster: false
    // }
});

const createNode = () => {
    form.post(route("nodes.store"), {
        // errorBag: 'updateProfileInformation',
        // preserveScroll: true,
        // onSuccess: (response) => {
        //     console.log('RESPONSE', response);
        //     // router.visit(route('nodes.show', {id: 1}))
        // }
    });
};
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Create Node
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <FormSection @submitted="createNode">
                    <template #title> Server Details </template>

                    <template #description>
                        <p>
                            How your server will be represented in our system.
                        </p>
                        <p>
                            You will receive Agent installation instructions on
                            the next step.
                        </p>
                    </template>

                    <template #form>
                        <TeamCard :team="$page.props.auth.user.current_team" />

                        <div class="col-span-6 sm:col-span-4" v-auto-animate>
                            <InputLabel for="server_name" value="Server Name" />
                            <TextInput
                                id="server_name"
                                v-model="form.name"
                                autofocus
                                class="block w-full mt-1"
                            />
                            <InputError
                                :message="form.errors.name"
                                class="mt-2"
                            />
                        </div>
                    </template>

                    <template #submit>
                        <PrimaryButton
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Create Node
                        </PrimaryButton>
                    </template>
                </FormSection>

                <!--                    <ActionSection>-->
                <!--                        <template #title>-->
                <!--                            Swarm Cluster-->
                <!--                        </template>-->

                <!--                        <template #description>-->
                <!--                            Select either to initialize a new cluster or join an existing cluster.-->
                <!--                        </template>-->

                <!--                        <template #content>-->
                <!--                            <div v-auto-animate>-->
                <!--                                <ul class="items-center w-full text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg sm:flex dark:bg-gray-700 dark:border-gray-600 dark:text-white">-->
                <!--                                    <li class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r dark:border-gray-600">-->
                <!--                                        <div class="flex items-center ps-3">-->
                <!--                                            <input id="swarm-init-option" v-model="form.swarm_option" type="radio" value="init" name="list-radio" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">-->
                <!--                                            <label for="swarm-init-option" class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Initialize Cluster</label>-->
                <!--                                        </div>-->
                <!--                                    </li>-->
                <!--                                    <li class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r dark:border-gray-600">-->
                <!--                                        <div class="flex items-center ps-3">-->
                <!--                                            <input id="swarm-join-option" v-model="form.swarm_option" type="radio" value="join" name="list-radio" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">-->
                <!--                                            <label for="swarm-join-option" class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Join Swarm</label>-->
                <!--                                        </div>-->
                <!--                                    </li>-->
                <!--                                </ul>-->

                <!--                                <div v-if="form.swarm_option === 'init'">-->
                <!--                                    Select IP-->
                <!--                                </div>-->

                <!--                                <div v-if="form.swarm_option === 'join'">-->
                <!--                                    Unfortunately, this feature is not implemented yet.-->
                <!--                                </div>-->
                <!--                            </div>-->
                <!--                        </template>-->
                <!--                    </ActionSection>-->
            </div>
        </div>
    </AppLayout>
</template>
