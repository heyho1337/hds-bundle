<?php

namespace App\Entity;

use App\Repository\LangsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LangsRepository::class)]
class Langs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $langs_id = null;

    #[ORM\Column(length: 50)]
    private ?string $langs_name = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $langs_leiras = null;

    #[ORM\Column]
    private ?bool $langs_default = null;

    #[ORM\Column]
    private ?bool $langs_aktiv = null;

    #[ORM\Column(length: 2)]
    private ?string $langs_code = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $langs_flag = null;

    #[ORM\Column]
    private ?int $langs_sort = null;

    public function getId(): ?int
    {
        return $this->langs_id;
    }

    public function getLangsName(): ?string
    {
        return $this->langs_name;
    }

    public function setLangsName(string $langs_name): static
    {
        $this->langs_name = $langs_name;

        return $this;
    }

    public function getLangsLeiras(): ?string
    {
        return $this->langs_leiras;
    }

    public function setLangsLeiras(?string $langs_leiras): static
    {
        $this->langs_leiras = $langs_leiras;

        return $this;
    }

    public function isLangsDefault(): ?bool
    {
        return $this->langs_default;
    }

    public function setLangsDefault(bool $langs_default): static
    {
        $this->langs_default = $langs_default;

        return $this;
    }

    public function isLangsAktiv(): ?bool
    {
        return $this->langs_aktiv;
    }

    public function setLangsAktiv(bool $langs_aktiv): static
    {
        $this->langs_aktiv = $langs_aktiv;

        return $this;
    }

    public function getLangsCode(): ?string
    {
        return $this->langs_code;
    }

    public function setLangsCode(string $langs_code): static
    {
        $this->langs_code = $langs_code;

        return $this;
    }

    public function getLangsFlag(): ?string
    {
        return $this->langs_flag;
    }

    public function setLangsFlag(?string $langs_flag): static
    {
        $this->langs_flag = $langs_flag;

        return $this;
    }

    public function getLangsSort(): ?int
    {
        return $this->langs_sort;
    }

    public function setLangsSort(int $langs_sort): static
    {
        $this->langs_sort = $langs_sort;

        return $this;
    }
}
