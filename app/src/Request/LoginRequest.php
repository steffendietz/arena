<?php

declare(strict_types=1);

namespace App\Request;

use Spiral\Filters\Filter;

class LoginRequest extends Filter
{
    protected const SCHEMA = [
        'username' => 'data:username',
        'password' => 'data:password'
    ];

    protected const VALIDATES = [
        'username' => ['notEmpty'],
        'password' => ['notEmpty']
    ];

    protected const SETTERS = [];
}
