<?php
namespace App\DataFixtures;

use App\Entity\Restaurant;
use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use app\enum\Status;

class RestaurantFixtures extends Fixture implements DependentFixtureInterface
{
    public const RESTAURANTS = [
        [
            'title' => 'La Belle Vue',
            'description' => 'Un restaurant offrant une vue imprenable.',
            'address' => '12 Avenue de la Paix',
            'city' => 'Paris',
            'country' => 'France',
            'postalCode' => '75001',
            'email' => 'contact@labellevue.fr',
            'phoneNumber' => '0123456789',
            'website' => 'http://labellevue.fr',
            'imageName' => 'space.jpg',
            'userReference' => 'restaurant_user_1',
        ],
        [
            'title' => 'Le Gourmet',
            'description' => 'Une cuisine fine pour les gourmets.',
            'address' => '23 Rue des Délices',
            'city' => 'Lyon',
            'country' => 'France',
            'postalCode' => '69001',
            'email' => 'contact@legourmet.fr',
            'phoneNumber' => '0123456789',
            'website' => 'http://legourmet.fr',
            'imageName' => 'cafeshop.jpg',
            'userReference' => 'restaurant_user_2',
        ],
        [
            'title' => 'Chez Marie',
            'description' => 'Ambiance familiale et plats traditionnels.',
            'address' => '5 Rue de la Mer',
            'city' => 'Marseille',
            'country' => 'France',
            'postalCode' => '13001',
            'email' => 'contact@chezmarie.fr',
            'phoneNumber' => '0123456789',
            'website' => 'http://chezmarie.fr',
            'imageName' => 'restaurant.jpg',
            'userReference' => 'restaurant_user_3',
        ],
        [
            'title' => 'L\'Épicurien',
            'description' => 'Un restaurant pour les amateurs de bonne cuisine.',
            'address' => '45 Avenue de la République',
            'city' => 'Nice',
            'country' => 'France',
            'postalCode' => '06000',
            'email' => 'contact@lepicurien.fr',
            'phoneNumber' => '0123456789',
            'website' => 'http://lepicurien.fr',
            'imageName' => 'cafeshop.jpg',
            'userReference' => 'restaurant_user_4',
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::RESTAURANTS as $key => $data) {
            $restaurant = new Restaurant();
            $restaurant
                ->setTitle($data['title'])
                ->setDescription($data['description'])
                ->setAddress($data['address'])
                ->setCity($data['city'])
                ->setCountry($data['country'])
                ->setPostalCode($data['postalCode'])
                ->setEmail($data['email'])
                ->setPhoneNumber($data['phoneNumber'])
                ->setWebsite($data['website'])
                ->setHasListing(true)
                ->setStatus(Status::VERIFIE)
                ->setIsApproved(true);

            // Créez une nouvelle instance d'image unique meme si visuellement cest la meme pour chaque restaurant
            $image = new Image();
            $image->setName($data['imageName']);
            $image->setUpdatedAt(new \DateTimeImmutable());

            // Associer l'image et l'utilisateur au restaurant
            $restaurant->setImage($image);
            $restaurant->setUser($this->getReference($data['userReference']));

            $this->addReference('restaurant_' . $key, $restaurant);

            // Puis persister 
            $manager->persist($image);
            $manager->persist($restaurant);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class, 
            ImageFixtures::class,
        ];
    }
}
