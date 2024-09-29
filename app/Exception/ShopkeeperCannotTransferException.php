<?php

namespace App\Exception;

use App\Enum\ExceptionMessageCodeEnum;
use Swoole\Http\Status;

class ShopkeeperCannotTransferException extends AbstractException
{
    public function __construct()
    {
        $code = Status::FORBIDDEN;
        $message = ExceptionMessageCodeEnum::SHOPKEEPER_CANNOT_TRANSFER;
        parent::__construct($message, $code);
    }
}