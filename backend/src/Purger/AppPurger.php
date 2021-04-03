<?php

namespace App\Purger;

use Doctrine\Common\DataFixtures\Purger\PurgerInterface;

class AppPurger implements PurgerInterface
{
    public function purge(): void
    {
    }
}
