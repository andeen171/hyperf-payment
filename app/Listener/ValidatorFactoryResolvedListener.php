<?php

namespace App\Listener;

use App\Validator\DocumentValidator;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Hyperf\Validation\Event\ValidatorFactoryResolved;
use Hyperf\Validation\Validator;

#[Listener]
class ValidatorFactoryResolvedListener implements ListenerInterface
{
    public function listen(): array
    {
        return [
            ValidatorFactoryResolved::class,
        ];
    }

    public function process(object $event): void
    {
        /** @var ValidatorFactoryInterface $validatorFactory */
        $validatorFactory = $event->validatorFactory;

        $validatorFactory->extend('cpf', function (string $attribute, mixed $value, array $parameters, Validator $validator): bool {
            return DocumentValidator::validateCpf($value);
        });

        $validatorFactory->extend('cnpj', function (string $attribute, mixed $value, array $parameters, Validator $validator): bool {
            return DocumentValidator::validateCnpj($value);
        });
    }


}
