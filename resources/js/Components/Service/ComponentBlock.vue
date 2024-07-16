<script setup>
defineProps({
  'label': String,
})

defineEmits(['remove'])

defineModel();
</script>

<template>
  <div class="col-span-full" v-auto-animate>
    <h3 v-if="modelValue.length > 0" class="my-2">{{ label }}</h3>

    <div v-for="(item, index) in modelValue">
      <hr class="my-2" v-if="index > 0" />

      <div class="grid grid-cols-6 gap-4" v-auto-animate>
        <slot :item="modelValue[index]" :$index="index" />
      </div>

      <div class="flex justify-end">
        <button class="text-xs flex items-center" type="button" @click="$emit('remove', index)">
          <svg class="w-3 h-3 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/>
          </svg>

          remove
        </button>
      </div>
    </div>
  </div>
</template>