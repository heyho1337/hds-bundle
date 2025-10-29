<?php

namespace App\Entity;

use App\Repository\ConfigRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConfigRepository::class)]
class Config
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title_hu = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title_en = null;

    private ?string $meta_desc = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $meta_desc_hu = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $meta_desc_en = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $favicon = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $apple_icon = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $modified_at = null;

    #[ORM\ManyToOne]
    private ?Schema $schema_type = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $zip_code = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $schema_text = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTitleHu(): ?string
    {
        return $this->title_hu;
    }

    public function setTitleHu(?string $title_hu): static
    {
        $this->title_hu = $title_hu;

        return $this;
    }

    public function getTitleEn(): ?string
    {
        return $this->title_en;
    }

    public function setTitleEn(?string $title_en): static
    {
        $this->title_en = $title_en;

        return $this;
    }

    public function getMetaDesc(): ?string
    {
        return $this->meta_desc;
    }

    public function setMetaDesc(?string $meta_desc): static
    {
        $this->meta_desc = $meta_desc;

        return $this;
    }

    public function getMetaDescHu(): ?string
    {
        return $this->meta_desc_hu;
    }

    public function setMetaDescHu(?string $meta_desc_hu): static
    {
        $this->meta_desc_hu = $meta_desc_hu;

        return $this;
    }

    public function getMetaDescEn(): ?string
    {
        return $this->meta_desc_en;
    }

    public function setMetaDescEn(?string $meta_desc_en): static
    {
        $this->meta_desc_en = $meta_desc_en;

        return $this;
    }

    public function getFavicon(): ?string
    {
        return $this->favicon;
    }

    public function setFavicon(?string $favicon): static
    {
        $this->favicon = $favicon;

        return $this;
    }

    public function getAppleIcon(): ?string
    {
        return $this->apple_icon;
    }

    public function setAppleIcon(?string $apple_icon): static
    {
        $this->apple_icon = $apple_icon;

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

    public function getSchemaType(): ?Schema
    {
        return $this->schema_type;
    }

    public function setSchemaType(?Schema $schema_type): static
    {
        $this->schema_type = $schema_type;

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

    public function getZipCode(): ?string
    {
        return $this->zip_code;
    }

    public function setZipCode(?string $zip_code): static
    {
        $this->zip_code = $zip_code;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getSchemaText(): ?string
    {
        return $this->schema_text;
    }

    public function setSchemaText(?string $schema_text): static
    {
        $this->schema_text = $schema_text;

        return $this;
    }
}
