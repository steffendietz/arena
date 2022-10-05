<?php

declare(strict_types=1);

namespace App\Request;

use Spiral\Filters\Attribute\Input\Post;
use Spiral\Filters\Model\Filter;
use Spiral\Filters\Model\FilterDefinitionInterface;
use Spiral\Filters\Model\HasFilterDefinition;
use Spiral\Validator\FilterDefinition;

class LoginRequest extends Filter implements HasFilterDefinition
{
    #[Post(key: 'username')]
    public string $username;

    #[Post(key: 'password')]
    public string $password;

    public function filterDefinition(): FilterDefinitionInterface
    {
        return new FilterDefinition(
            validationRules: [
                'username' => [['notEmpty']],
                'password' => [['notEmpty']],
            ]
        );
    }
}
