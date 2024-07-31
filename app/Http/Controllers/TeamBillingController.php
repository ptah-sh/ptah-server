<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Laravel\Paddle\Cashier;
use Laravel\Paddle\Transaction;

class TeamBillingController extends Controller
{
    public function show(Team $team)
    {
        $customer = $team->createAsCustomer();

        //        dd($team->subscription()->nextPayment());
        $checkout = $team->subscribe('pri_01j2ag2ts45hznad1t67bs4syd')->returnTo(route('teams.billing.show', $team));
        //$team->subscription()->anchorBillingCycleOn(Carbon::now()->addMonth()->startOfMonth());
        $nextPayment = $team->subscription()?->nextPayment();

        //Cashier::api()
        return Inertia::render('Teams/Billing', [
            'team' => $team,
            'customer' => $customer,
            'nextPayment' => $nextPayment,
            'subscription' => $team->subscription()?->canceled() ? null : $team->subscription(),
            'checkout' => $checkout->options(),
            'transactions' => $team->transactions,
            'updatePaymentMethodUrl' => $team->subscription()?->paymentMethodUpdateUrl(),
            'cancelSubscriptionUrl' => $team->subscription()?->cancelUrl(),
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
            'email' => 'required|email',
        ]);

        DB::transaction(function () use ($team, $formData) {
            $customer = $team->createAsCustomer();
            $customer->update($formData);

            Cashier::api('PATCH', 'customers/'.$customer->paddle_id, $formData);
        });

        return redirect()->route('teams.billing.show', $team);
    }
}
