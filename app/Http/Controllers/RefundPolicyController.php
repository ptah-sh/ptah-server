<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Laravel\Jetstream\Jetstream;

class RefundPolicyController extends Controller
{
    /**
     * Show the privacy policy for the application.
     *
     * @return \Inertia\Response
     */
    public function show(Request $request)
    {
        $policyFile = Jetstream::localizedMarkdownPath('refund.md');

        return Inertia::render('RefundPolicy', [
            'policy' => Str::markdown(file_get_contents($policyFile)),
        ]);
    }
}
