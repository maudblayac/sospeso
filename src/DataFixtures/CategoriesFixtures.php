<?php 
namespace App\DataFixtures;

use App\Entity\Categories;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoriesFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categoriesData = [
            1 => 'Boisson',
            2 => 'Dessert',
            3 => 'Plat',
        ];

        foreach ($categoriesData as $id => $name) {
            $category = new Categories();
            $category->setName($name);

            $manager->persist($category);
            $this->addReference('categories_' . $id, $category);
        }

        $manager->flush();
    }
}
