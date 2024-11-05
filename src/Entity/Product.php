<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Doctrine\DBAL\Types\Types;
use App\Entity\Image;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[Vich\Uploadable]

class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categories $categories = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Restaurant $restaurant = null;

    /**
     * @var Collection<int, CommandLine>
     */
    #[ORM\OneToMany(targetEntity: CommandLine::class, mappedBy: 'product')]
    private Collection $commandLines;

    /**
     * @var Collection<int, ProductSuspended>
     */
    #[ORM\OneToMany(targetEntity: ProductSuspended::class, mappedBy: 'product')]
    private Collection $productSuspendeds;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $price = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;
    
    #[ORM\OneToOne(mappedBy: 'product', cascade: ['persist', 'remove'])]
    private ?Image $image = null;

  
    public function __construct()
    {
        $this->commandLines = new ArrayCollection();
        $this->productSuspendeds = new ArrayCollection();
    }
    public function getImage(): ?Image
    {
        return $this->image;
    }
    
    public function setImage(?Image $image): static
    {
        // Si une image est définie pour un produit, alors on s'assure de la liaison bi-directionnelle
        if ($image && $image->getProduct() !== $this) {
            $image->setProduct($this);
        }
    
        $this->image = $image;
    
        return $this;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategories(): ?Categories
    {
        return $this->categories;
    }

    public function setCategories(?Categories $categories): static
    {
        $this->categories = $categories;

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

    /**
     * @return Collection<int, CommandLine>
     */
    public function getCommandLines(): Collection
    {
        return $this->commandLines;
    }

    public function addCommandLine(CommandLine $commandLine): static
    {
        if (!$this->commandLines->contains($commandLine)) {
            $this->commandLines->add($commandLine);
            $commandLine->setProduct($this);
        }

        return $this;
    }

    public function removeCommandLine(CommandLine $commandLine): static
    {
        if ($this->commandLines->removeElement($commandLine)) {
            // set the owning side to null (unless already changed)
            if ($commandLine->getProduct() === $this) {
                $commandLine->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ProductSuspended>
     */
    public function getProductSuspendeds(): Collection
    {
        return $this->productSuspendeds;
    }

    public function addProductSuspended(ProductSuspended $productSuspended): static
    {
        if (!$this->productSuspendeds->contains($productSuspended)) {
            $this->productSuspendeds->add($productSuspended);
            $productSuspended->setProduct($this);
        }

        return $this;
    }

    public function removeProductSuspended(ProductSuspended $productSuspended): static
    {
        if ($this->productSuspendeds->removeElement($productSuspended)) {
            // set the owning side to null (unless already changed)
            if ($productSuspended->getProduct() === $this) {
                $productSuspended->setProduct(null);
            }
        }

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }


    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }
}
