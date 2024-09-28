<?php

namespace App\Exception\Auth;

use App\Enum\ExceptionMessageCodeEnum;
use App\Exception\AbstractException;
use Swoole\Http\Status;

class UserNotFoundException extends AbstractException
{
    public function __construct()
    {
        $code = Status::NOT_FOUND;
        $message = ExceptionMessageCodeEnum::USER_NOT_FOUND;
        parent::__construct($message, $code);
    }
}