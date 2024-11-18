<?php
namespace App\DTO;

use App\Entity\Categories;
use Symfony\Component\Validator\Constraints as Assert;

class RestaurantSearchDto
{
    private ?string $search = null;

    private ?Categories $categories = null;

    private ?string $city = null;

    #[Assert\Positive()]
    #[Assert\LessThanOrEqual(propertyPath:'maxPrice')]
    private ?int $minPrice = null;

    #[Assert\Positive()]
    #[Assert\GreaterThanOrEqual(propertyPath:'minPrice')]
    private ?int $maxPrice = null;

    // Getters and Setters for each property

    public function getSearch(): ?string
    {
        return $this->search;
    }

    public function setSearch(?string $search): void
    {
        $this->search = $search;
    }

    public function getCategories(): ?Categories
    {
        return $this->categories;
    }

    public function setCategories(?Categories $categories): void
    {
        $this->categories = $categories;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function getMinPrice(): ?int
    {
        return $this->minPrice;
    }

    public function setMinPrice(?int $minPrice): void
    {
        $this->minPrice = $minPrice;
    }

    public function getMaxPrice(): ?int
    {
        return $this->maxPrice;
    }

    public function setMaxPrice(?int $maxPrice): void
    {
        $this->maxPrice = $maxPrice;
    }
}
