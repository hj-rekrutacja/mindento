<?php

namespace App\Http\Requests;

use App\Rules\DelegationIntervalNotOverlapping;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDelegationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'start' => 'required|date',
            'end' => [
                'required',
                'date',
                'after:start',
                new DelegationIntervalNotOverlapping()
            ],
            'worker_id' => 'required|numeric|exists:workers,id',
            'country' => [
                'required',
                'alpha',
                'size:2',
                Rule::in(collect(config('delegation.amount_rules'))->keys())
            ],
        ];
    }
}
