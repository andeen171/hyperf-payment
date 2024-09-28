<?php

namespace App\Enum;

enum ExceptionMessageCodeEnum: string
{
    case USER_NOT_FOUND = 'user_not_found';
    case WRONG_CREDENTIALS = 'wrong_credentials';
    case UNAUTHORIZED = 'unauthorized';
}
