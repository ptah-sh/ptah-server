<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\VerifyEmailResponse;

class PtahVerifyEmailResponse implements VerifyEmailResponse
{
    public function toResponse($request)
    {
        $node = $request->user()->personalTeam()->nodes()->first();

        return redirect()->to(route('nodes.settings', $node));
    }
}
