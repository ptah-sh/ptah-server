<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    teamId: Number,
    priceId: String,
    customerId: String,
    name: String,
});

const openCheckout = () => {
    const checkout = {
        settings: {
            displayMode: "overlay",
            successUrl: route(
                "teams.billing.subscription-success",
                { team: props.teamId },
                true,
            ),
        },
        items: [{ priceId: props.priceId, quantity: 1 }],
        customer: { id: props.customerId },
        customData: { team_id: props.teamId },
    };

    Paddle.Checkout.open(checkout);
};
</script>

<template>
    <PrimaryButton
        class="paddle_button bg-green-500 hover:bg-green-700"
        type="button"
        @click="openCheckout()"
        ><span class="w-full text-center"
            >Pick {{ name }} Plan</span
        ></PrimaryButton
    >
</template>
