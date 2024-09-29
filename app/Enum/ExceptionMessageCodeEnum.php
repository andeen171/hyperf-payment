<?php

namespace App\Enum;

enum ExceptionMessageCodeEnum: string
{
    case USER_NOT_FOUND = 'user_not_found';
    case INVALID_CREDENTIALS = 'invalid_credentials';
    case UNAUTHORIZED = 'unauthorized';
    case INSUFFICIENT_FUNDS = 'insufficient_funds';
    case TRANSACTION_FAILED = 'transaction_failed';
    case SHOPKEEPER_CANNOT_TRANSFER = 'shopkeeper_cannot_transfer';
}
