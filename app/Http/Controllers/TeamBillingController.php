<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Laravel\Paddle\Cashier;
use Laravel\Paddle\Transaction;

class TeamBillingController extends Controller
{
    public function show(Team $team, Request $request)
    {
        $customer = $team->createAsCustomer();

        $checkout = $team->subscribe(config('billing.paddle.server_price_id'))->customData([
            'team_id' => $team->id,
        ])->returnTo(route('teams.billing.subscription-success', $team));

        $subscription = $team->subscription();

        $nextPayment = $subscription?->nextPayment();

        //Cashier::api()
        return Inertia::render('Teams/Billing', [
            'team' => $team,
            'customer' => $customer,
            'nextPayment' => $nextPayment,
            'subscription' => $subscription?->valid() ? $subscription : null,
            'checkout' => $checkout->options(),
            'transactions' => $team->transactions,
            'updatePaymentMethodTxnId' => $subscription?->paymentMethodUpdateTransaction()['id'],
            'cancelSubscriptionUrl' => $subscription?->cancelUrl(),
        ]);
    }

    public function downloadInvoice(Team $team, Transaction $transaction)
    {
        return $transaction->redirectToInvoicePdf();
    }

    public function updateCustomer(Team $team, Request $request)
    {
        $formData = $request->validate([
            'name' => 'required',
            'email' => ['required', 'email', 'unique:teams,billing_email,'.$team->id],
        ]);

        DB::transaction(function () use ($team, $formData) {
            $customer = $team->createAsCustomer();
            $customer->update($formData);

            Cashier::api('PATCH', 'customers/'.$customer->paddle_id, $formData);
        });

        return redirect()->route('teams.billing.show', $team);
    }

    public function subscriptionSuccess(Team $team, Request $request)
    {
        if (! $team->subscription()?->valid()) {
            $team->activating_subscription = true;
            $team->save();
        }

        session()->flash('success', "Payment successfully processed. We'll active your subscription in a few minutes.");

        return redirect()->to(route('teams.billing.show', $team));
    }
}
