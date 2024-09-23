<script setup>
import { computed } from "vue";
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    data: {
        type: Object,
        required: true,
    },
    columns: {
        type: Array,
        required: true,
    },
    onRowClick: {
        type: Function,
        default: () => {},
    },
});

const headers = computed(() => props.columns.map((column) => column.header));
</script>

<template>
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th
                        v-for="header in headers"
                        :key="header"
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                        {{ header }}
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr
                    v-for="item in data.data"
                    :key="item.id"
                    @click="onRowClick(item)"
                    class="cursor-pointer hover:bg-gray-50 transition-colors duration-150 ease-in-out"
                >
                    <td
                        v-for="column in columns"
                        :key="column.key"
                        class="px-6 py-4 whitespace-nowrap"
                    >
                        <slot :name="column.key" :item="item">
                            {{
                                column.value
                                    ? column.value(item)
                                    : item[column.key]
                            }}
                        </slot>
                    </td>
                </tr>
            </tbody>
        </table>
        <div v-if="data.links.length > 3" class="px-6 py-4">
            <Pagination :links="data.links" />
        </div>
    </div>
</template>
