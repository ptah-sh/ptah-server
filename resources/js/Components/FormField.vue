<script setup>
import {ref, onMounted, reactive, onActivated} from 'vue';
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
  'error': String,
})

const slot = ref(null);

const state = reactive({
  domId: ''
});

onMounted(() => {
  state.domId = 'f-' + Math.random().toString(36).slice(2);

  slot.value.children[0]?.setAttribute('id', state.domId);
})

</script>

<template>
  <div v-auto-animate>
    <InputLabel :for="state.domId">
      <slot name="label" />
    </InputLabel>

    <div ref="slot">
      <slot />
    </div>

    <InputError :message="$props.error" class="mt-2" />
  </div>
</template>