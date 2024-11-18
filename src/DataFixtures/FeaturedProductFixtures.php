<?php

namespace App\DataFixtures;

use App\Entity\FeaturedProduct;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class FeaturedProductFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 6; $i++) {
            // Obtenir un produit et son restaurant associé pour maintenir la cohérence
            $product = $this->getReference('product_' . $i);
            $restaurant = $product->getRestaurant(); // Récupérer directement le restaurant du produit

            $featuredProduct = new FeaturedProduct();
            $featuredProduct->setRestaurant($restaurant);
            $featuredProduct->setProduct($product);

            $manager->persist($featuredProduct);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            RestaurantFixtures::class,
            ProductFixtures::class,
        ];
    }
}
