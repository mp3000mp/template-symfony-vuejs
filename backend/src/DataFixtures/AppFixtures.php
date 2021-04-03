<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class AppFixtures.
 */
class AppFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public const USER_ADMIN = 'USER_ADMIN';
    public const USER_USER = 'USER_USER';

    public function load(ObjectManager $manager): void
    {
    }

    public static function getGroups(): array
    {
        return ['dev', 'test'];
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
