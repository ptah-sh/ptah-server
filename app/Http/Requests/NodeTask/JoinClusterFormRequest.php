<?php

namespace App\Http\Requests\NodeTask;

use Illuminate\Foundation\Http\FormRequest;

class JoinClusterFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'node_id' => ['required', 'exists:nodes,id'],
            'role' => ['required', 'in:manager,worker'],
            'advertise_addr' => ['exclude_if:role,worker', 'required', 'ipv4'],
        ];
    }
}
