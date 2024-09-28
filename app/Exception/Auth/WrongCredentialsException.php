<?php

namespace App\Exception\Auth;

use App\Enum\ExceptionMessageCodeEnum;
use App\Exception\AbstractException;
use Swoole\Http\Status;

class WrongCredentialsException extends AbstractException
{
    public function __construct()
    {
        $code = Status::UNAUTHORIZED;
        $message = ExceptionMessageCodeEnum::WRONG_CREDENTIALS;
        parent::__construct($message, $code);
    }
}