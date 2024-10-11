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
        default: null,
    },
});

const headers = computed(() =>
    props.columns.map((column) => {
        if (typeof column.header === "object") {
            return column.header;
        }

        return { title: column.header };
    }),
);
</script>

<template>
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th
                        v-for="header in headers"
                        :key="header.title"
                        :class="[
                            header.class,
                            'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider',
                        ]"
                        scope="col"
                    >
                        {{ header.title }}
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr
                    v-for="item in data.data"
                    :key="item.id"
                    @click="onRowClick ? onRowClick(item) : null"
                    :class="[
                        onRowClick ? 'cursor-pointer' : '',
                        'hover:bg-gray-50 transition-colors duration-150 ease-in-out group relative',
                    ]"
                >
                    <td
                        v-for="column in columns"
                        :key="column.key"
                        :class="[column.class, 'px-6 py-4 whitespace-nowrap']"
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
                <tr v-if="data.data.length === 0">
                    <td
                        colspan="100%"
                        class="px-6 py-4 text-center text-gray-500 text-sm"
                    >
                        No data found
                    </td>
                </tr>
            </tbody>
        </table>
        <div v-if="data.links.length > 3" class="px-6 py-4">
            <Pagination :links="data.links" />
        </div>
    </div>
</template>
