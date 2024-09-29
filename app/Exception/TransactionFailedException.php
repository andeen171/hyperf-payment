<?php

namespace App\Exception;

use App\Enum\ExceptionMessageCodeEnum;
use Swoole\Http\Status;
use Throwable;

class TransactionFailedException extends AbstractException
{
    public function __construct(Throwable $previous)
    {
        $code = Status::FAILED_DEPENDENCY;
        $message = ExceptionMessageCodeEnum::TRANSACTION_FAILED;
        parent::__construct($message, $code, $previous);
    }
}