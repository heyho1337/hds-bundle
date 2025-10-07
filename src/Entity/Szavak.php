<?php

namespace App\Entity;

use App\Repository\SzavakRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SzavakRepository::class)]
class Szavak
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $szavak_id = null;

    #[ORM\Column(type: Types::TEXT,nullable: true)]
    private ?string $szavak_hu = null;

    private ?string $szavak = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $szavak_en = null;

    #[ORM\Column(length: 255)]
    private ?string $szavak_code = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $modified_at = null;

    public function getId(): ?int
    {
        return $this->szavak_id;
    }

    public function getSzavakId(): ?int
    {
        return $this->szavak_id;
    }

    public function getSzavakHu(): ?string
    {
        return $this->szavak_hu;
    }

    public function setSzavakHu(string $szavak_hu): static
    {
        $this->szavak_hu = $szavak_hu;

        return $this;
    }

    public function getSzavak(): ?string
    {
        return $this->szavak;
    }

    public function setSzavak(?string $szavak): static
    {
        $this->szavak = $szavak;

        return $this;
    }

    public function getSzavakEn(): ?string
    {
        return $this->szavak_en;
    }

    public function setSzavakEn(?string $szavak_en): static
    {
        $this->szavak_en = $szavak_en;

        return $this;
    }

    public function getSzavakCode(): ?string
    {
        return $this->szavak_code;
    }

    public function setSzavakCode(string $szavak_code): static
    {
        $this->szavak_code = $szavak_code;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getModifiedAt(): ?\DateTimeImmutable
    {
        return $this->modified_at;
    }

    public function setModifiedAt(\DateTimeImmutable $modified_at): static
    {
        $this->modified_at = $modified_at;

        return $this;
    }
}
