<?php

declare(strict_types=1);

namespace Limenet\LaravelElasticaBridge\Exception\Index;

use Limenet\LaravelElasticaBridge\Exception\BaseException;

class BlueGreenIndicesIncorrectlySetupException extends BaseException
{
    public function __construct()
    {
        parent::__construct('Blue-green indices are not set up correctly');
    }
}
