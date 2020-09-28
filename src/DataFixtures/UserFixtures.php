<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        $password = 'first_user';

        $user = new User();
        $encodedPassword = $this->encoder->encodePassword($user, $password);
        $user->setEmail('mperret@mp3000mp.fr');
        $user->setUsername('mp3000mp');
        $user->setPassword($encodedPassword);
        $user->setPasswordUpdatedAt(new \DateTime());
        $user->setRoles(['ROLE_USER']);
        $user->setIsEnabled(true);

        $manager->persist($user);
        $manager->flush();

        $this->addReference(AppFixtures::FIRST_USER, $user);
    }
}
