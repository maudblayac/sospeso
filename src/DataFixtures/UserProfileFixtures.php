<?php

namespace App\DataFixtures;

use App\Entity\UserProfile;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class UserProfileFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Créer un profil pour un utilisateur spécifique
        $specificUser = $this->getReference('specific_user');
        $this->createUserProfile($manager, $specificUser, $faker);

        // Créer des profils pour d'autres utilisateurs
        for ($i = 0; $i < 9; $i++) {
            $userReference = $this->getReference('user_' . $i);
            $this->createUserProfile($manager, $userReference, $faker);
        }

        $manager->flush();
    }

    private function createUserProfile(ObjectManager $manager, User $user, $faker): void
    {
        $userProfile = new UserProfile();
        $userProfile
            ->setUser($user)
            ->setFirstname($faker->firstName())
            ->setLastname($faker->lastName())
            ->setPhoneNumber($faker->phoneNumber())
            ->setAddress($faker->streetAddress())
            ->setCity($faker->city())
            ->setCountry($faker->country())
            ->setPostalCode(substr($faker->postcode(), 0, 8))
            ->setDateOfBirth($faker->dateTimeBetween('-40 years', '-18 years'))
            ->setUpdateAt(new \DateTimeImmutable());

        $manager->persist($userProfile);
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
