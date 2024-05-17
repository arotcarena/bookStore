<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    )
    {
        
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $admin = new User;
        $admin
            ->setUsername('admin')
            ->setPassword(
                $this->hasher->hashPassword(
                    $admin, 'admin'
                )
            )
            ->setRoles(['ROLE_USER', 'ROLE_ADMIN'])
            ;
        $manager->persist($admin);

        $users = [];
        for($i=0; $i < 20; $i++) {
            $user = new User;
            $user->setUsername($faker->userName())
                ->setPassword('password')
                ;
            $manager->persist($user);
            $users[] = $user;
        }

        for ($i=0; $i < 200; $i++) { 
            $book = new Book;
            $book->setTitle($faker->text(20))
                ->setAuthor($faker->name())
                ->setPublishedAt($faker->dateTime()->format('Y'))
                ->setCreatedAt(new DateTimeImmutable($faker->dateTime()->format('Y:m:d H:i:s')))
                ->setUpdatedAt(new DateTimeImmutable($faker->dateTime()->format('Y:m:d H:i:s')))
                ->setUser($faker->randomElement($users))
                ;
            $manager->persist($book);
        }

        $manager->flush();
    }
}
