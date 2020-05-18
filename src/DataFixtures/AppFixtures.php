<?php declare(strict_types=1);

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class AppFixtures
 *
 * @package App\DataFixtures
 */
class AppFixtures extends Fixture implements DependentFixtureInterface
{
    public const FIRST_USER = 'FIRST_USER';
    public const APPTYPE_PORTAL = 'APPTYPE_PORTAL';

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
    }

    /**
     * @return array
     */
    public function getDependencies(): array
    {
        return [
            TermsOfServiceFixtures::class,
            ApplicationTypeFixtures::class,
            ApplicationFixtures::class,
            UserFixtures::class,
        ];
    }
}
