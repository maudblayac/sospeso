<?php
namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $this->createAdmin($manager);
        $this->createRestaurants($manager); 
        $this->createUsers($manager);
    }

    private function createAdmin(ObjectManager $manager): void
    {
        $admin = new User();
        $admin
            ->setEmail('admin@mail.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->userPasswordHasher->hashPassword(
                $admin,
                'password'
            ))
            ->setCreatedAt(new \DateTimeImmutable())
            ->setIsActive(true)
            ->setIsVerified(true);

        $this->addReference('admin_user', $admin);

        $manager->persist($admin);
    }

    private function createRestaurants(ObjectManager $manager): void
    {
        $faker = Factory::create();

        
        for ($i = 1; $i <= 5; $i++) {
            $restaurant = new User();
            $restaurant
                ->setEmail('restaurant' . $i . '@mail.com')
                ->setRoles(['ROLE_RESTAURANT'])
                ->setPassword($this->userPasswordHasher->hashPassword(
                    $restaurant,
                    'password'
                ))
                ->setCreatedAt(new \DateTimeImmutable())
                ->setIsActive(true)
                ->setIsVerified(true);

            $this->addReference('restaurant_user_' . $i, $restaurant);

            $manager->persist($restaurant);
        }
    }

    private function createUsers(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $user = new User();
        $user
            ->setEmail('user@mail.com')
            ->setRoles(['ROLE_USER'])
            ->setPassword($this->userPasswordHasher->hashPassword(
                $user,
                'password'
            ))
            ->setCreatedAt(new \DateTimeImmutable('2020-10-20'))
            ->setIsActive(true)
            ->setIsVerified(false);

        $this->addReference('specific_user', $user);
        $manager->persist($user);

        for ($i = 0; $i < 9; $i++) {
            $additionalUser = new User();
            $additionalUser
                ->setEmail($faker->email())
                ->setRoles(['ROLE_USER'])
                ->setPassword($this->userPasswordHasher->hashPassword(
                    $additionalUser,
                    'password'
                ))
                ->setCreatedAt(new \DateTimeImmutable())
                ->setIsActive(true)
                ->setIsVerified(false);

            $this->addReference('user_' . $i, $additionalUser);
            $manager->persist($additionalUser);
        }

        $manager->flush();
    }
}
