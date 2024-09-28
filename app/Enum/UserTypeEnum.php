<?php

namespace App\Enum;

use App\Trait\EnumValues;

enum UserTypeEnum: string
{
    use EnumValues;

    case COMMON = 'common';
    case SHOPKEEPER = 'shopkeeper';
}
