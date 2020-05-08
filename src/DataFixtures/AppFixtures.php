<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public const FIRST_USER = 'FIRST_USER';

    public function __construct()
    {
    }

    public function load(ObjectManager $manager): void
    {
    }
}
