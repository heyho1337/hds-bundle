<?php

namespace App\Entity;

use App\Repository\MenuTargetRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MenuTargetRepository::class)]
class MenuTarget
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    private static string $currentLang = 'en';

    #[ORM\Column(length: 255,nullable: true)]
    private ?string $name_hu = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name_en = null;

    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $target = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $modified_at = null;

    #[ORM\Column]
    private ?bool $active = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameHu(): ?string
    {
        return $this->name_hu;
    }

    public function setNameHu(string $name_hu): static
    {
        $this->name_hu = $name_hu;

        return $this;
    }

    public function getNameEn(): ?string
    {
        return $this->name_en;
    }

    public function setNameEn(?string $name_en): static
    {
        $this->name_en = $name_en;

        return $this;
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

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function setTarget(string $target): static
    {
        $this->target = $target;

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

    public static function setCurrentLang(string $lang): void
    {
        self::$currentLang = $lang;
    }

    public function __toString(): string
    {
        $getter = 'getName' . self::$currentLang;
        if (method_exists($this, $getter)) {
            return (string) $this->$getter();
        }

        return '';
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }
}
