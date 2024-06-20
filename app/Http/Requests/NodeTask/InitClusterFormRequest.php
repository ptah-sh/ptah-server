<?php

namespace App\Http\Requests\NodeTask;

use Illuminate\Foundation\Http\FormRequest;

class InitClusterFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'node_id' => ['required'],
            'name' => ['required', 'string', 'max:255'],
            'advertise_addr' => ['required', 'ipv4'],
            'force_new_cluster' => ['boolean'],
        ];
    }
}