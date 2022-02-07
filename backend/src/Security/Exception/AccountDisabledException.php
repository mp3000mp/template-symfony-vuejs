<?php

declare(strict_types=1);

namespace App\Security\Exception;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

class AccountDisabledException extends AccountStatusException
{
    public function getMessageKey(): string
    {
        return 'Your accound is disabled.';
    }
}
