<?php

namespace App\Entity;

use App\Repository\StatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatusRepository::class)]
class Status
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    /**
     * @var Collection<int, Command>
     */
    #[ORM\OneToMany(targetEntity: Command::class, mappedBy: 'status')]
    private Collection $commands;

    /**
     * @var Collection<int, ProductSuspended>
     */
    #[ORM\OneToMany(targetEntity: ProductSuspended::class, mappedBy: 'status')]
    private Collection $productSuspendeds;

    public function __construct()
    {
        $this->commands = new ArrayCollection();
        $this->productSuspendeds = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection<int, Command>
     */
    public function getCommands(): Collection
    {
        return $this->commands;
    }

    public function addCommand(Command $command): static
    {
        if (!$this->commands->contains($command)) {
            $this->commands->add($command);
            $command->setStatus($this);
        }
        return $this;
    }

    public function removeCommand(Command $command): static
    {
        if ($this->commands->removeElement($command)) {
            // Set the owning side to null (unless already changed)
            if ($command->getStatus() === $this) {
                $command->setStatus(null);
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
            $productSuspended->setStatus($this);
        }
        return $this;
    }

    public function removeProductSuspended(ProductSuspended $productSuspended): static
    {
        if ($this->productSuspendeds->removeElement($productSuspended)) {
            // Set the owning side to null (unless already changed)
            if ($productSuspended->getStatus() === $this) {
                $productSuspended->setStatus(null);
            }
        }
        return $this;
    }
}
