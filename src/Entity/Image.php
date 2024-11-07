<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


#[ORM\Entity(repositoryClass: ImageRepository::class)]
#[Vich\Uploadable]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    // Champ pour stocker l'image de produit
    #[Vich\UploadableField(mapping: 'product_images', fileNameProperty: 'name')]
    private ?File $productImageFile = null;

    // Champ pour stocker l'image de restaurant
    #[Vich\UploadableField(mapping: 'restaurant_images', fileNameProperty: 'name')]
    private ?File $restaurantImageFile = null;

    #[ORM\OneToOne(inversedBy: 'image', targetEntity: Product::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Product $product = null;

    #[ORM\OneToOne(inversedBy: 'image', targetEntity: Restaurant::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Restaurant $restaurant = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getProductImageFile(): ?File
    {
        return $this->productImageFile;
    }

    public function setProductImageFile(?File $productImageFile = null): static
    {
        $this->productImageFile = $productImageFile;

        if ($productImageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function getRestaurantImageFile(): ?File
    {
        return $this->restaurantImageFile;
    }

    public function setRestaurantImageFile(?File $restaurantImageFile = null): static
    {
        $this->restaurantImageFile = $restaurantImageFile;

        if ($restaurantImageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;
        return $this;
    }

    public function getRestaurant(): ?Restaurant
    {
        return $this->restaurant;
    }

    public function setRestaurant(?Restaurant $restaurant): static
    {
        $this->restaurant = $restaurant;
        return $this;
    }
    // public function __sleep()
    // {
    //     // Exclure `imageFile` pour éviter la sérialisation de l'objet File
    //     return ['id', 'name', 'updatedAt', 'product', 'restaurant'];
    // }
}
