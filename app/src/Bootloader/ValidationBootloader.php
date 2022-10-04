<?php

namespace App\Bootloader;

use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Validation\ValidationInterface;
use Spiral\Validation\ValidationProvider;
use Spiral\Validator\FilterDefinition;
use Spiral\Validator\Validation;

final class ValidationBootloader extends Bootloader
{
    protected const DEPENDENCIES = [
        ValidationBootloader::class
    ];

    public function boot(ValidationProvider $validationProvider): void
    {
        $validationProvider->register(
            FilterDefinition::class,
            static fn(Validation $validation): ValidationInterface => $validation
        );
    }
}