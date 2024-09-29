<?php

namespace App\Exception;

use App\Enum\ExceptionMessageCodeEnum;
use Swoole\Http\Status;

class InsufficientFundsException extends AbstractException
{
    public function __construct()
    {
        $code = Status::FORBIDDEN;
        $message = ExceptionMessageCodeEnum::INSUFFICIENT_FUNDS;
        parent::__construct($message, $code);
    }
}