<?php

declare(strict_types=1);

namespace App\Request;

use App\Enum\UserTypeEnum;
use App\Model\User;
use Hyperf\Contract\ValidatorInterface;
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

    public function prepareForValidation(): void
    {
        $validator = $this->getValidatorInstance();

        $validator->after(function (ValidatorInterface $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $user = User::find($validator->validated()['payer']);
            if ($user->type === UserTypeEnum::SHOPKEEPER->value) {
                $validator->errors()->add('payer', 'The payer must not be a shopkeeper.');
            }
        });
    }
}
