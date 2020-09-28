<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\ApplicationType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Class ApplicationTypeFixtures.
 */
class ApplicationTypeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // portal
        $appType = new ApplicationType();
        $appType->setName('Portal');
        $manager->persist($appType);

        $this->addReference(AppFixtures::APPTYPE_PORTAL, $appType);

        // app1
        $appType = new ApplicationType();
        $appType->setName('App1');
        $manager->persist($appType);

        // app2
        $appType = new ApplicationType();
        $appType->setName('App2');
        $manager->persist($appType);
        $manager->flush();
    }
}
