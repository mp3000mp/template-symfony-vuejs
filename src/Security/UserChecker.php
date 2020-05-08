<?php

namespace App\Security;

use App\Entity\User;
use App\Security\Exception\AccountDisabledException;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
        if ( ! $user instanceof User) {
            return;
        }

        // user is deleted, show a generic Account Not Found message.
        if (!$user->isEnabled()) {
            throw new AccountDisabledException();
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
        if ( ! $user instanceof User) {
            return;
        }

        // user account is expired, the user may be notified
        /*if ($user->isExpired()) {
            throw new AccountExpiredException('...');
        }*/
    }
}
