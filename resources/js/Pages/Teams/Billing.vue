<script setup lang="ts">
import TeamsLayout from "@/Pages/Teams/TeamsLayout.vue";
import PaddleButton from "@/Components/PaddleButton.vue";
import ActionSection from "@/Components/ActionSection.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import FormSection from "@/Components/FormSection.vue";
import FormField from "@/Components/FormField.vue";
import TextInput from "@/Components/TextInput.vue";
import { useForm } from "@inertiajs/vue3";
import ExternalLink from "@/Components/ExternalLink.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import SectionBorder from "@/Components/SectionBorder.vue";

const props = defineProps({
    team: Object,
    customer: Object,
    subscription: Object,
    checkout: Object,
    nextPayment: Object,
    transactions: Array,
    updatePaymentMethodTxnId: String,
    cancelSubscriptionUrl: String,
});

const customerForm = useForm({
    name: props.customer.name,
    email: props.customer.email,
});

const updateCustomer = () => {
    customerForm.patch(route("teams.billing.update-customer", props.team), {
        preserveScroll: true,
    });
};

const updatePaymentMethod = () => {
    Paddle.Checkout.open({
        transactionId: props.updatePaymentMethodTxnId,
    });
};
</script>

<template>
    <TeamsLayout :team="props.team">
        <FormSection @submit="updateCustomer">
            <template #title> Customer Details </template>

            <template #description>
                You can manage the billing details here. Invoices and receipts
                will be sent to the specified E-Mail address.
            </template>

            <template #form>
                <FormField class="col-span-4" :error="customerForm.errors.name">
                    <template #label> Business Name </template>

                    <TextInput v-model="customerForm.name" class="w-full" />
                </FormField>

                <FormField
                    class="col-span-4"
                    :error="customerForm.errors.email"
                >
                    <template #label> Business Email </template>

                    <TextInput v-model="customerForm.email" class="w-full" />
                </FormField>
            </template>

            <template #submit>
                <PrimaryButton
                    :class="{ 'opacity-25': customerForm.processing }"
                    :disabled="customerForm.processing"
                >
                    Save
                </PrimaryButton>
            </template>
        </FormSection>

        <SectionBorder />

        <ActionSection>
            <template #title> Next Payment </template>

            <template #description>
                <template v-if="team.activating_subscription">
                    Your subscription is being activated.
                </template>
                <template v-else-if="props.subscription && props.nextPayment">
                    Your next payment details.
                </template>
                <template v-else-if="props.subscription && !props.nextPayment">
                    You don't have any upcoming payments - subscription will
                    expire soon.
                </template>
                <template v-else>
                    You don't have to pay anything when you have no servers.
                </template>
            </template>

            <template #content>
                <div class="col-span-full" v-if="team.activating_subscription">
                    Your subscription is being activated. Please refresh the
                    page to update the status.
                </div>
                <div
                    v-else-if="props.subscription && nextPayment"
                    class="col-span-full"
                >
                    <div class="col-span-full mb-4">
                        {{
                            new Intl.DateTimeFormat(undefined, {
                                day: "numeric",
                                weekday: "long",
                                month: "long",
                                year: "numeric",
                                hour: "2-digit",
                                minute: "2-digit",
                            }).format(new Date(nextPayment.date))
                        }}
                    </div>
                    <div class="col-span-full flex gap-4">
                        <span class="text-5xl font-extrabold">{{
                            nextPayment.amount
                        }}</span>
                        <span class="text-xs text-gray-500"
                            >(includes taxes)</span
                        >
                    </div>
                </div>
                <div
                    v-else-if="props.subscription?.ends_at"
                    class="col-span-full"
                >
                    <p>
                        Subscription will end on
                        {{
                            new Intl.DateTimeFormat(undefined, {
                                day: "numeric",
                                weekday: "long",
                                month: "long",
                                year: "numeric",
                                hour: "2-digit",
                                minute: "2-digit",
                            }).format(new Date(props.subscription.ends_at))
                        }}.
                    </p>
                </div>
                <div v-else class="col-span-full grid grid-cols-6 gap-4">
                    <div class="col-span-full">
                        <p>Subscription is not active.</p>
                        <ExternalLink href="https://ptah.sh/#pricing"
                            >See Pricing</ExternalLink
                        >
                    </div>

                    <div class="col-span-full">
                        <PaddleButton
                            v-if="!props.nextPayment"
                            :checkout="props.checkout"
                        />
                    </div>
                </div>
            </template>

            <template v-if="nextPayment" #actions>
                <div class="flex justify-between items-center w-full">
                    <SecondaryButton type="button" @click="updatePaymentMethod"
                        >Update Payment Method</SecondaryButton
                    >
                    <ExternalLink :href="cancelSubscriptionUrl"
                        >Cancel Subscription</ExternalLink
                    >
                </div>
            </template>
        </ActionSection>

        <SectionBorder />

        <ActionSection>
            <template #title> Transactions </template>

            <template #description>
                Your recent payments. Click on the invoice number to download it
                as a PDF file.
            </template>

            <template #content>
                <div class="relative overflow-x-auto col-span-full">
                    <table
                        class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"
                    >
                        <thead
                            class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
                        >
                            <tr>
                                <th scope="col" class="px-6 py-3">Date</th>
                                <th scope="col" class="px-6 py-3">Invoice</th>
                                <th scope="col" class="px-6 py-3">Amount</th>
                                <th scope="col" class="px-6 py-3">Tax</th>
                                <th scope="col" class="px-6 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="txn in props.transactions"
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700"
                            >
                                <th
                                    scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                >
                                    {{
                                        new Intl.DateTimeFormat(
                                            undefined,
                                            {},
                                        ).format(new Date(txn.billed_at))
                                    }}
                                </th>
                                <td v-if="txn.invoice_number" class="px-6 py-4">
                                    <a
                                        :href="
                                            route(
                                                'teams.billing.download-invoice',
                                                {
                                                    team: props.team.id,
                                                    transaction: txn.id,
                                                },
                                            )
                                        "
                                        target="_blank"
                                        class="flex hover:underline"
                                        >{{ txn.invoice_number }}
                                        <svg
                                            class="ms-1 w-4 h-4 text-gray-600 dark:text-white"
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
                                                d="M12 13V4M7 14H5a1 1 0 0 0-1 1v4a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-4a1 1 0 0 0-1-1h-2m-1-5-4 5-4-5m9 8h.01"
                                            />
                                        </svg>
                                    </a>
                                </td>
                                <td v-else class="px-6 py-4 italic">
                                    Trial Started
                                </td>
                                <td class="px-6 py-4 text-right">
                                    {{
                                        new Intl.NumberFormat(undefined, {
                                            currency: txn.currency,
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2,
                                        }).format(txn.total / 100)
                                    }}
                                    {{ txn.currency }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    {{
                                        new Intl.NumberFormat(undefined, {
                                            currency: txn.currency,
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2,
                                        }).format(txn.tax / 100)
                                    }}
                                    {{ txn.currency }}
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        v-if="txn.status === 'completed'"
                                        class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300"
                                        >completed</span
                                    >
                                    <span v-else>{{ txn.status }}</span>
                                </td>
                            </tr>
                            <tr v-if="props.transactions.length === 0">
                                <td colspan="5" class="text-center pt-4 italic">
                                    No transactions made yet.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </template>
        </ActionSection>

        <!--    <table>-->
        <!--      @foreach ($transactions as $transaction)-->
        <!--      <tr>-->
        <!--        <td>{{ $transaction->billed_at->toFormattedDateString() }}</td>-->
        <!--        <td>{{ $transaction->total() }}</td>-->
        <!--        <td>{{ $transaction->tax() }}</td>-->
        <!--        <td><a href="{{ route('download-invoice', $transaction->id) }}" target="_blank">Download</a></td>-->
        <!--      </tr>-->
        <!--      @endforeach-->
        <!--    </table>-->

        <!--    use Illuminate\Http\Request;-->
        <!--    use Laravel\Cashier\Transaction;-->

        <!--    Route::get('/download-invoice/{transaction}', function (Request $request, Transaction $transaction) {-->
        <!--    return $transaction->redirectToInvoicePdf();-->
        <!--    })->name('download-invoice');-->
    </TeamsLayout>
</template>
