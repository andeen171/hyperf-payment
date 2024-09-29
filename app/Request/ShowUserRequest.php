<?php

declare(strict_types=1);

namespace App\Request;

use App\Exception\Auth\UserNotFoundException;
use App\Model\User;
use Hyperf\Validation\Request\FormRequest;

class ShowUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function getItem(): User
    {
        $user = User::find($this->route('id'));

        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }
}
