<?php

declare(strict_types=1);

namespace App\Http\Requests\v1;

use App\Constants\AvailableMethodsConstants;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExchangeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $method = $this->get(key: 'method');

        return [
            'method' => ['required', Rule::in(values: [
                AvailableMethodsConstants::RATES,
                AvailableMethodsConstants::CONVERT,
            ])],
            'currency' => [
                Rule::requiredIf(callback: $method === AvailableMethodsConstants::RATES),
                'string',
            ],
            'currency_from' => [
                Rule::requiredIf(callback: $method === AvailableMethodsConstants::CONVERT),
                'string',
            ],
            'currency_to' => [
                Rule::requiredIf(callback: $method === AvailableMethodsConstants::CONVERT),
                'string',
            ],
            'value' => [
                Rule::requiredIf($method === AvailableMethodsConstants::CONVERT),
                'numeric',
                'gte:0.01',
            ],
        ];
    }
}
