<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordEncoder)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User;
        $admin
            ->setEmail('admin@test.fr')
            ->setFirstname('Doe')
            ->setLastname('John')
            ->setAddress('20 rue du bonheur')
            ->setZipcode('75000')
            ->setCity('Paris')
            ->setCountry('France')
            ->setPassword($this->passwordEncoder->hashPassword($admin, 'password'))
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);

        $faker = Factory::create('fr_FR');
        for ($i = 0; $i <= 10; $i++) {
            $user = new User;
            $user
                ->setEmail($faker->email)
                ->setFirstname($faker->firstName)
                ->setLastname($faker->lastName)
                ->setAddress($faker->streetAddress)
                ->setZipcode(str_replace(' ', '',$faker->postcode))
                ->setCity($faker->city)
                ->setCountry('France')
                ->setPassword($this->passwordEncoder->hashPassword($user, 'password'));

            $manager->persist($user);
        }


        $manager->flush();
    }
}
