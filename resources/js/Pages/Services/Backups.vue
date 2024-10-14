<script setup>
import DataTable from "@/Components/DataTable.vue";
import ShowLayout from "./ShowLayout.vue";
import dayjs from "dayjs";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import BackupRestore from "./Partials/BackupRestore.vue";
import { ref } from "vue";

const props = defineProps({
    service: Object,
    backups: Object,
    s3Storages: Object,
    restoreWorkers: Array,
});

const columns = [
    { header: "Producer", key: "worker" },
    { header: "Destination", key: "dest_path" },
    { header: "Timestamps", key: "timestamps" },
    { header: { title: "Status", class: "text-center w-40" }, key: "status" },
];

const backup = ref(null);
</script>

<template>
    <ShowLayout :service="service">
        <DataTable :data="backups" :columns="columns">
            <template #worker="{ item }">
                <div class="overflow-hidden text-ellipsis flex flex-col">
                    <span class="text-xs font-bold text-gray-500">Process</span>
                    <span>{{ item.process }}</span>
                    <span class="text-xs font-bold text-gray-500 mt-1"
                        >Worker</span
                    >
                    <span>{{ item.worker }}</span>
                </div>
            </template>

            <template #dest_path="{ item }">
                <div
                    class="overflow-hidden text-ellipsis max-w-96 flex flex-col"
                >
                    <span class="text-xs font-bold text-gray-500"
                        >S3 Storage</span
                    >
                    <span>
                        {{
                            props.s3Storages.find(
                                (s3Storage) =>
                                    s3Storage.id === item.s3_storage_id,
                            )?.name ?? item.s3_storage_id
                        }}
                    </span>
                    <span class="text-xs font-bold text-gray-500 mt-1"
                        >Path</span
                    >
                    <span class="max-w-96 overflow-hidden text-ellipsis">{{
                        item.dest_path
                    }}</span>
                </div>
            </template>

            <template #timestamps="{ item }">
                <div
                    class="overflow-hidden text-ellipsis max-w-64 flex flex-col"
                >
                    <span class="text-xs font-bold text-gray-500"
                        >Started {{ dayjs(item.started_at).fromNow() }}</span
                    >
                    <span>{{ dayjs(item.started_at).format("LLLL") }}</span>
                    <template v-if="item.ended_at">
                        <span class="text-xs font-bold text-gray-500 mt-1"
                            >Ended {{ dayjs(item.ended_at).fromNow() }}</span
                        >
                        <span
                            >{{ dayjs(item.ended_at).from(item.started_at) }},
                            at {{ dayjs(item.ended_at).format("LT") }}</span
                        >
                    </template>
                </div>
            </template>

            <template #status="{ item }">
                <div
                    class="w-full h-full flex items-center justify-center gap-2"
                >
                    <span
                        class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-green-100 text-green-800"
                    >
                        {{ item.status }}
                    </span>
                </div>

                <div
                    v-if="item.status === 'succeeded'"
                    class="hidden absolute top-0 right-0 h-full w-full group-hover:flex items-center justify-center"
                >
                    <div
                        class="absolute top-0 left-0 w-full h-full bg-white opacity-50 z-10"
                    ></div>
                    <div class="z-20 shadow-lg">
                        <PrimaryButton @click="backup = item"
                            >Restore</PrimaryButton
                        >
                    </div>
                </div>
            </template>
        </DataTable>

        <BackupRestore
            :service="service"
            :backup="backup"
            @close="backup = null"
            :restore-workers="restoreWorkers"
            :s3-storages="s3Storages"
        />
    </ShowLayout>
</template>
