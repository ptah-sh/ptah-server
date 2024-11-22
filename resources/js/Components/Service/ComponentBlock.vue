<script setup>
defineProps({
    label: String,
});

defineEmits(["add", "remove"]);

defineModel();
</script>

<template>
    <div class="col-span-full" v-auto-animate>
        <h3 class="my-2 flex items-center gap-4">
            {{ label }}

            <button
                class="text-xs flex items-center px-2 py-1 rounded-md bg-gray-100 hover:bg-gray-200"
                type="button"
                @click="$emit('add')"
            >
                <svg
                    class="w-3 h-3 text-gray-800 dark:text-white"
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
                        d="M12 5v14M5 12h14"
                    />
                </svg>

                add
            </button>
        </h3>

        <div v-for="(item, index) in modelValue">
            <hr class="my-2" v-if="index > 0" />

            <div class="grid grid-cols-6 gap-4" v-auto-animate>
                <slot :item="modelValue[index]" :$index="index" />
            </div>

            <div class="flex justify-end mt-2">
                <button
                    class="text-xs flex items-center px-2 py-1 rounded-md bg-gray-100 hover:bg-gray-200"
                    type="button"
                    @click="$emit('remove', index)"
                >
                    <svg
                        class="w-3 h-3 text-gray-800 dark:text-white"
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
                            d="M6 18 17.94 6M18 18 6.06 6"
                        />
                    </svg>

                    remove
                </button>
            </div>
        </div>

        <div v-if="modelValue.length === 0">
            <p class="text-sm text-gray-500">No items</p>
        </div>
    </div>
</template>
