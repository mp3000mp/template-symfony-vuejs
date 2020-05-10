<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\TermsOfService;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TermsOfServiceFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $tos = new TermsOfService();
        $tos->setPublishedAt(new DateTime());

        $manager->persist($tos);
        $manager->flush();
    }
}
