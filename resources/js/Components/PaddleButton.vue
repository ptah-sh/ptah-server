<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
  'checkout': Object,
});

const openCheckout = () => {
  Paddle.Checkout.open({
    ...props.checkout,
    settings: {
      ...props.checkout.settings,
      'displayMode': 'overlay',
    }
  });
}
</script>

<template>
  <PrimaryButton class="paddle_button bg-green-500 hover:bg-green-700"
          type="button"
          @click="openCheckout()"
  >Start Free Trial - 14 days</PrimaryButton>
</template>