<?php

namespace App\Exception\Auth;

use App\Enum\ExceptionMessageCodeEnum;
use App\Exception\AbstractException;
use Swoole\Http\Status;

class UnauthorizedException extends AbstractException
{
    public function __construct()
    {
        $code = Status::UNAUTHORIZED;
        $message = ExceptionMessageCodeEnum::UNAUTHORIZED;
        parent::__construct($message, $code);
    }
}