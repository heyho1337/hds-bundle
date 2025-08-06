<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}
    
    public function load(ObjectManager $manager): void
    {
        /*$user = new User();
        $user->setEmail('heyho1337@gmail.com');
        $user->setName('heyho1337');
        $user->setActive(true);
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setUpdatedAt(new \DateTimeImmutable());
        $user->setVerified(true);

        $hashedPassword = $this->passwordHasher->hashPassword($user, 'Akarom123');
        $user->setPassword($hashedPassword);

        $manager->persist($user);
        $manager->flush();
        */
    }
}
