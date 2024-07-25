<script setup>
import SectionTitle from './SectionTitle.vue';
import {computed, useSlots} from "vue";

const slots = useSlots();

const contentClasses = computed(() => {
  const base = 'px-4 py-5 sm:p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg grid grid-cols-6 gap-4';

  if (slots.tabs) {
    return base + ' rounded-tl-none sm:rounded-tl-none';
  }

  return base;
})
</script>

<template>
    <div class="md:grid md:grid-cols-3 md:gap-6 my-8">
        <SectionTitle>
            <template #title>
              <!-- TODO: add expand/collapse functionality -->
<!--              <div class="-ms-6 absolute">+</div>-->
                <slot name="title" />
            </template>
            <template #description>
                <slot name="description" />
            </template>
        </SectionTitle>

        <div class="mt-5 md:mt-0 md:col-span-2">
          <div v-if="$slots.tabs">
              <slot name="tabs" />
          </div>

            <div :class="contentClasses" v-auto-animate>
                <slot name="content" />
            </div>

          <div v-if="$slots.actions" v-auto-animate="{duration: 90}" class="flex items-center justify-end px-4 py-4 pt-5 -mt-2 gap-2 bg-gray-50 dark:bg-gray-800 text-end sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md">
            <slot name="actions" />
          </div>
        </div>
    </div>
</template>
