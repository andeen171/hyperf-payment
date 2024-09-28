<?php

declare(strict_types=1);

namespace HyperfTest;

use Faker\Factory as FakerFactory;
use Hyperf\Database\Model\Factory;

class ModelFactory
{
    public function __invoke(): Factory
    {
        return Factory::construct(FakerFactory::create(), __DIR__ . '/Factories');
    }
}
