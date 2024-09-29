<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;

class TransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'value' => 'required|numeric|min:1',
            'payer' => 'required|integer|exists:users,id',
            'payee' => 'required|integer|exists:users,id',
        ];
    }
}
