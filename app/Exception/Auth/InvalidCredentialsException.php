<?php

namespace App\Exception\Auth;

use App\Enum\ExceptionMessageCodeEnum;
use App\Exception\AbstractException;
use Swoole\Http\Status;

class InvalidCredentialsException extends AbstractException
{
    public function __construct()
    {
        $code = Status::UNAUTHORIZED;
        $message = ExceptionMessageCodeEnum::INVALID_CREDENTIALS;
        parent::__construct($message, $code);
    }
}