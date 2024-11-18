<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public const PRODUCTS = [
        [
            'name' => 'Café',
            'description'=> 'Un bon café qui réchauffe et réveille.',
            'price' => 1.80,
            'image' => 'cafe_image',
            'categories' => 'categories_1',
        ],
        [
            'name' => 'Cookie',
            'description'=> 'Un excellent cookie qui donnera de la force.',
            'price' => 2.50,
            'image' => 'cookie_image',
            'categories' => 'categories_2',
        ],
        [
            'name' => 'Sandwich',
            'description'=> 'Un sandwich croustillant, garni de viande tendre, fromage fondant, légumes frais et une sauce onctueuse.',
            'price' => 4.80,
            'image' => 'sandwich_image',
            'categories' => 'categories_3',
        ],
        [
            'name' => 'Bagel',
            'description'=> 'Un bagel croustillant, garni de saumon tendre, fromage fondant, légumes frais et une sauce blanche.',
            'price' => 8.00,
            'image' => 'bagel_image',
            'categories' => 'categories_3',
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 10; $i++) {
            $data = self::PRODUCTS[array_rand(self::PRODUCTS)];

            $product = new Product();
            $product
                ->setName($data['name'])
                ->setDescription($data['description'])
                ->setPrice($data['price'])
                ->setIsApproved(true)
                ->setCategories($this->getReference($data['categories']))
                ->setRestaurant($this->getReference('restaurant_' . ($i % 4))); // Associe les produits aux restaurants en boucle

            // Associer une image unique à chaque produit
            $originalImage = $this->getReference($data['image']);
            $image = new Image();
            $image->setName($originalImage->getName());
            $image->setUpdatedAt(new \DateTimeImmutable());
            $image->setProduct($product);

            $product->setImage($image);

            // Ajouter une référence unique pour chaque produit
            $this->addReference('product_' . $i, $product);

            $manager->persist($image);
            $manager->persist($product);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ImageFixtures::class,
            RestaurantFixtures::class,
        ];
    }
}
