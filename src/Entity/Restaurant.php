<?php

namespace App\Entity;

use App\Repository\RestaurantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use App\Entity\Image;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: RestaurantRepository::class)]
#[Vich\Uploadable]
class Restaurant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 80, nullable: true)]
    private ?string $firstname = null;

    #[ORM\Column(length: 80, nullable: true)]
    private ?string $lastname = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phoneNumber = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(length: 8, nullable: true)]
    private ?string $postalCode = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateOfBirth = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $website = null;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\OneToMany(mappedBy: 'restaurant', targetEntity: Product::class, cascade: ['persist', 'remove'])]
    private Collection $products;

    /**
     * @var Collection<int, Favorite>
     */
    #[ORM\OneToMany(targetEntity: Favorite::class, mappedBy: 'restaurant')]
    private Collection $favorites;

    /**
     * @var Collection<int, Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class, mappedBy: 'Tag')]
    private Collection $tags;

    #[ORM\OneToOne(inversedBy: 'restaurant', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    #[ORM\OneToOne(mappedBy: 'restaurant', cascade: ['persist', 'remove'])]
    private ?Image $image = null;

    #[ORM\Column(type: 'boolean')]
    private bool $hasListing = false; 
    
    #[ORM\Column(type: 'boolean')]
    private bool $isApproved = false;


    #[ORM\Column(type: 'boolean')]
    private bool $isPaused = false;


    #[ORM\OneToMany(mappedBy: 'restaurant', targetEntity: FeaturedProduct::class, cascade: ['persist', 'remove'])]
    private Collection $featuredProducts;


    // #[ORM\Column(type: 'boolean')]
    // private bool $isVerified = false; 

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->favorites = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->featuredProducts = new ArrayCollection();
    }

    // Getters and setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function hasListing(): bool
    {
        return $this->hasListing;
    }

    public function setHasListing(bool $hasListing): self
    {
        $this->hasListing = $hasListing;
        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): static
    {
        $this->firstname = $firstname;
        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): static
    {
        $this->lastname = $lastname;
        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;
        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): static
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(?\DateTimeInterface $dateOfBirth): static
    {
        $this->dateOfBirth = $dateOfBirth;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): static
    {
        $this->website = $website;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }
    
    public function setImage(?Image $image): static
    {
        // Si une image est définie pour un produit, alors on s'assure de la liaison bi-directionnelle
        if ($image && $image->getRestaurant() !== $this) {
            $image->setRestaurant($this);
        }
    
        $this->image = $image;
    
        return $this;
    }

   /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
            $tag->addTag($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): static
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removeTag($this);
        }

        return $this;
    }


    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setRestaurant($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            if ($product->getRestaurant() === $this) {
                $product->setRestaurant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Favorite>
     */
    public function getFavorites(): Collection
    {
        return $this->favorites;
    }

    public function addFavorite(Favorite $favorite): static
    {
        if (!$this->favorites->contains($favorite)) {
            $this->favorites->add($favorite);
            $favorite->setRestaurant($this);
        }

        return $this;
    }

    public function removeFavorite(Favorite $favorite): static
    {
        if ($this->favorites->removeElement($favorite)) {
            if ($favorite->getRestaurant() === $this) {
                $favorite->setRestaurant(null);
            }
        }

        return $this;
    }
    public function getFeaturedProducts(): Collection
    {
        return $this->featuredProducts;
    }

    public function addFeaturedProduct(FeaturedProduct $featuredProduct): self
    {
        if (!$this->featuredProducts->contains($featuredProduct)) {
            $this->featuredProducts->add($featuredProduct);
            $featuredProduct->setRestaurant($this);
        }
        return $this;
    }

    public function removeFeaturedProduct(FeaturedProduct $featuredProduct): self
    {
        if ($this->featuredProducts->removeElement($featuredProduct)) {
            if ($featuredProduct->getRestaurant() === $this) {
                $featuredProduct->setRestaurant(null);
            }
        }
        return $this;
    }
    public function isPaused(): bool
    {
        return $this->isPaused;
    }

    public function setIsPaused(bool $isPaused): self
    {
        $this->isPaused = $isPaused;
        return $this;
    }
    public function isApproved(): bool
    {
        return $this->isApproved;
    }

    public function setIsApproved(bool $isApproved): self
    {
        $this->isApproved = $isApproved;

        return $this;
    }


}
