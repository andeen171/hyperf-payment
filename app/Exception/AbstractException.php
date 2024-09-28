<?php

namespace App\Exception;

use App\Enum\ExceptionMessageCodeEnum;
use Hyperf\Server\Exception\ServerException;
use Throwable;

class AbstractException extends ServerException
{
    protected array $data = [];

    public function __construct(ExceptionMessageCodeEnum $message, int $code, ?Throwable $previous = null)
    {
        parent::__construct($message->value, $code, $previous);
    }

    public function getData(): array
    {
        return $this->data;
    }
}