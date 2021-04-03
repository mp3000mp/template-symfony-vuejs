<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    private UserPasswordEncoderInterface $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public static function getGroups(): array
    {
        return ['dev', 'test'];
    }

    public function load(ObjectManager $manager): void
    {
        $password = 'Test2000!';

        // user
        $user = new User();
        $encodedPassword = $this->encoder->encodePassword($user, $password);
        $user->setEmail('user@mp3000.fr');
        $user->setUsername('user');
        $user->setPassword($encodedPassword);
        $user->setPasswordUpdatedAt(new \DateTime());
        $user->setRoles(['ROLE_USER']);
        $user->setIsEnabled(true);

        $manager->persist($user);
        $this->addReference(AppFixtures::USER_USER, $user);

        // admin
        $user = new User();
        $encodedPassword = $this->encoder->encodePassword($user, $password);
        $user->setEmail('admin@mp3000.fr');
        $user->setUsername('admin');
        $user->setPassword($encodedPassword);
        $user->setPasswordUpdatedAt(new \DateTime());
        $user->setRoles(['ROLE_ADMIN']);
        $user->setIsEnabled(true);

        $manager->persist($user);
        $this->addReference(AppFixtures::USER_ADMIN, $user);

        // disabled
        $user = new User();
        $encodedPassword = $this->encoder->encodePassword($user, $password);
        $user->setEmail('disabled@mp3000.fr');
        $user->setUsername('disabled');
        $user->setPassword($encodedPassword);
        $user->setPasswordUpdatedAt(new \DateTime());
        $user->setRoles(['ROLE_USER']);
        $user->setIsEnabled(false);

        $manager->persist($user);

        $manager->flush();
    }
}
