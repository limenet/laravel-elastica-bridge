<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Enum;

enum IndexBlueGreenSuffix: string
{
    case BLUE = '--blue';

    case GREEN = '--green';
}
