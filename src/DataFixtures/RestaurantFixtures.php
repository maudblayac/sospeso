<?php 
namespace App\DataFixtures;

use App\Entity\Restaurant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class RestaurantFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $restaurantsData = [
            [
                'name' => 'Le Gourmet',
                'phoneNumber' => '0123456789',
                'address' => '123 Rue de Paris, 75001 Paris',
                'email' => 'contact@legourmet.fr',
                'website' => 'https://www.legourmet.fr',
                'description' => 'Un restaurant raffiné au cœur de Paris.',
                'title' => 'Gourmet Restaurant',
                'userReference' => 'restaurant_user_1',
            ],
            [
                'name' => 'La Belle Époque',
                'phoneNumber' => '0234567890',
                'address' => '456 Avenue des Champs-Élysées, 75008 Paris',
                'email' => 'contact@labelleepoque.fr',
                'website' => 'https://www.labelleepoque.fr',
                'description' => 'Un restaurant avec une cuisine traditionnelle française.',
                'title' => 'Traditional French Cuisine',
                'userReference' => 'restaurant_user_2',
            ],
            [
                'name' => 'Le Bistro du Coin',
                'phoneNumber' => '0345678901',
                'address' => '789 Rue de la République, 69001 Lyon',
                'email' => 'contact@lebistroducoin.fr',
                'website' => 'https://www.lebistroducoin.fr',
                'description' => 'Un bistro convivial avec une ambiance chaleureuse.',
                'title' => 'Cozy Bistro',
                'userReference' => 'restaurant_user_3',
            ],
            [
                'name' => 'Le Petit Bistro',
                'phoneNumber' => '0456789012',
                'address' => '123 Rue de Lyon, 69002 Lyon',
                'email' => 'contact@lepetitbistro.fr',
                'website' => 'https://www.lepetitbistro.fr',
                'description' => 'Un petit bistro charmant avec une cuisine maison.',
                'title' => 'Charming Bistro',
                'userReference' => 'restaurant_user_4',
            ],
            [
                'name' => 'La Table du Chef',
                'phoneNumber' => '0567890123',
                'address' => '456 Boulevard Saint-Germain, 75005 Paris',
                'email' => 'contact@latableduchef.fr',
                'website' => 'https://www.latabledduchef.fr',
                'description' => 'Un restaurant de cuisine raffinée avec une vue magnifique.',
                'title' => 'Refined Cuisine',
                'userReference' => 'restaurant_user_5',
            ],
        ];

        foreach ($restaurantsData as $key => $data) {
            $restaurant = new Restaurant();
            $restaurant
                ->setName($data['name'])
                ->setPhoneNumber($data['phoneNumber'])
                ->setAddress($data['address'])
                ->setEmail($data['email'])
                ->setWebsite($data['website'])
                ->setDescription($data['description'])
                ->setTitle($data['title'])
                ->setUser($this->getReference($data['userReference'])); 

            $this->addReference('restaurant_' . $key, $restaurant);

            $manager->persist($restaurant);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class, 
        ];
    }
}
