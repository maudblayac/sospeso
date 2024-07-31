<?php

namespace App\Entity;

use App\Repository\VoucherRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VoucherRepository::class)]
class Voucher
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'vouchers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Sponsor $sponsor = null;

    #[ORM\ManyToOne(inversedBy: 'vouchers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CommandLine $commandLine = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSponsor(): ?Sponsor
    {
        return $this->sponsor;
    }

    public function setSponsor(?Sponsor $sponsor): static
    {
        $this->sponsor = $sponsor;

        return $this;
    }

    public function getCommandLine(): ?CommandLine
    {
        return $this->commandLine;
    }

    public function setCommandLine(?CommandLine $commandLine): static
    {
        $this->commandLine = $commandLine;

        return $this;
    }
}
