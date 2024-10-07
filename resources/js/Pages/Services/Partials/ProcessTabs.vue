<script setup>
import { computed } from "vue";
import TabItem from "@/Components/Tabs/TabItem.vue";

const model = defineModel();

const props = defineProps({
    processes: Array,
    block: String,
    closable: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["close"]);

const handleClose = (index) => {
    emit("close", index);
};

const canClose = computed(() => {
    return props.closable && props.processes.length > 1;
});
</script>

<template>
    <div class="inline-flex rounded-md shadow" role="group">
        <div class="z-10">
            <TabItem
                v-for="(process, index) in processes"
                :key="process.id"
                @click="model.selectedProcessIndex[block] = index"
                :active="model.selectedProcessIndex[block] === index"
                :closable="canClose"
                @close="handleClose(index)"
            >
                <template #label>
                    {{ process.name }}
                </template>
            </TabItem>
        </div>
    </div>
</template>
