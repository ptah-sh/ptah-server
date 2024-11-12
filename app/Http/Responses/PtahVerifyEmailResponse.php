<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\VerifyEmailResponse;

class PtahVerifyEmailResponse implements VerifyEmailResponse
{
    public function toResponse($request)
    {
        $node = $request->user()->personalTeam()->nodes()->first();
        if (! $node) {
            return redirect()->to(route('dashboard'));
        }

        return redirect()->to(route('nodes.settings', $node));
    }
}
