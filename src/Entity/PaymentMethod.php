<?php

namespace App\Entity;

use App\Repository\PaymentMethodRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentMethodRepository::class)]
class PaymentMethod
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
    #[ORM\OneToMany(targetEntity: Command::class, mappedBy: 'paymentMethod')]
    private Collection $commands;

    public function __construct()
    {
        $this->commands = new ArrayCollection();
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
            $command->setPaymentMethod($this);
        }

        return $this;
    }

    public function removeCommand(Command $command): static
    {
        if ($this->commands->removeElement($command)) {
            // set the owning side to null (unless already changed)
            if ($command->getPaymentMethod() === $this) {
                $command->setPaymentMethod(null);
            }
        }

        return $this;
    }
}
