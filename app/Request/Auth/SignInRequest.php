<?php

declare(strict_types=1);

namespace App\Request\Auth;

use App\Model\User;
use Hyperf\Validation\Request\FormRequest;

class SignInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string',
            'passwordConfirmation' => 'required|string|same:password',
        ];
    }

    public function getItem(): ?User
    {
        /** @var ?User $user */
        $user = User::where('email', $this->input('email'))->first();

        return $user;
    }
}
