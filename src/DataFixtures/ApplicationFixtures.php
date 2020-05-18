<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Application;
use App\Entity\ApplicationType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Class ApplicationFixtures
 *
 * @package App\DataFixtures
 */
class ApplicationFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        /** @var ApplicationType $portalType */
        $portalType = $this->getReference(AppFixtures::APPTYPE_PORTAL);

        $app = new Application();
        $app->setName('portal');
        $app->setType($portalType);
        $app->setVersion('1.1');
        $app->setImg('/img/favicon.png');
        $app->setUrl('http://template-sf.localhost');

        $manager->persist($app);
        $manager->flush();
    }
}
