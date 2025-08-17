<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UnauthorizedException extends AccessDeniedHttpException
{
    public function __construct($message = 'Unauthorized access')
    {
        parent::__construct($message);
    }
}
