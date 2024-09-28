<?php

declare(strict_types=1);

namespace App\Request\Auth;

use App\Enum\UserTypeEnum;
use Hyperf\Validation\Request\FormRequest;

class SignUpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userTypes = UserTypeEnum::valuesAsString();

        return [
            'type' => ['string', "in:$userTypes"],
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'email' => 'required|email',
            'document' => 'required|string',
            'password' => 'required|string',
            'passwordConfirmation' => 'required|string|same:password',
        ];
    }

    public function prepareForValidation(): void
    {
        $validator = $this->getValidatorInstance();

        $validator->sometimes('document', 'cpf', function ($input) {
            return !$this->input('type') || $this->input('type') === UserTypeEnum::COMMON->value;
        });

        $validator->sometimes('document', 'cnpj', function ($input) {
            return $this->input('type') === UserTypeEnum::SHOPKEEPER->value;
        });
    }
}
