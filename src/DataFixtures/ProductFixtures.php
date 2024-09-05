<?php

namespace App\DataFixtures;

use App\Entity\Product;
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
            'picture' => '/images/restaurant/cafe.jpg',
            'categories' => 'categories_1',
        ],
        [
            'name' => 'Cookie',
            'description'=> 'Un excellent cookie qui donnera de la force.',
            'price' => 2.50,
            'picture' => '/images/restaurant/cookie.jpg',
            'categories' => 'categories_2',
        ],
        [
            'name' => 'Sandwich',
            'description'=> 'Un sandwich croustillant, garni de viande tendre, fromage fondant, légumes frais et une sauce onctueuse.',
            'price' => 4.80,
            'picture' => '/images/restaurant/sandwich.jpg',
            'categories' => 'categories_3',
        ],
        [
            'name' => 'Bagel',
            'description'=> 'Un bagel croustillant, garni de saumon tendre, fromage fondant, légumes frais et une sauce blanche.',
            'price' => 8.00,
            'picture' => '/images/restaurant/bagel.jpg',
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
                ->setPicture($data['picture'])
                ->setCategories($this->getReference($data['categories']))
                ->setRestaurant($this->getReference('restaurant_' . rand(0, 4))); 
            $this->addReference('product_' . $i, $product);
            $manager->persist($product);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            RestaurantFixtures::class, 
        ];
    }
}
