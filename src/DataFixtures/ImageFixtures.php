<?php

namespace App\DataFixtures;

use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Filesystem\Filesystem;

class ImageFixtures extends Fixture
{
    public const IMAGES = [
        'cafe.jpg' => 'cafe_image',
        'cookie.jpg' => 'cookie_image',
        'sandwich.jpg' => 'sandwich_image',
        'bagel.jpg' => 'bagel_image',
    ];

    public function load(ObjectManager $manager): void
    {
        $sourceDir = dirname(__DIR__, 2) . '/assets/images/restaurant';
        $destinationDir = dirname(__DIR__, 2) . '/public/uploads/products';
        
        $filesystem = new Filesystem();
        
        // Créer le dossier de destination s'il n'existe pas
        if (!$filesystem->exists($destinationDir)) {
            $filesystem->mkdir($destinationDir, 0755);
        }

        foreach (self::IMAGES as $filename => $referenceName) {
            $sourceFilePath = $sourceDir . '/' . $filename;
            $destinationFilePath = $destinationDir . '/' . $filename;

            // Copier le fichier source vers le dossier de destination
            if (is_file($sourceFilePath)) {
                $filesystem->copy($sourceFilePath, $destinationFilePath, true);

                // Créer l'objet Image et définir le nom du fichier
                $image = new Image();
                $image->setName($filename);
                $image->setUpdatedAt(new \DateTimeImmutable());
                
                // Enregistrer une référence pour associer aux produits
                $this->addReference($referenceName, $image);
                
                $manager->persist($image);
            }
        }

        // $manager->flush();
    }
}
