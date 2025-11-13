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

    private static string $currentLang = 'en';

    // JSON translation storage
    #[ORM\Column(type: Types::JSON)]
    private array $title = [];

    #[ORM\Column(type: Types::JSON)]
    private array $meta_desc = [];

    // Standard columns
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

    #[ORM\Column]
    private ?bool $multilang = null;

    public function __construct()
    {
        $this->title = [];
        $this->meta_desc = [];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    // Smart getters/setters
    public function getTitle(?string $lang = null): ?string
    {
        $lang = $lang ?? self::$currentLang;
        return $this->title[$lang] ?? $this->title['en'] ?? null;
    }

    public function setTitle(?string $value, ?string $lang = null): static
    {
        $lang = $lang ?? self::$currentLang;
        $this->title[$lang] = $value;
        return $this;
    }

    public function getMetaDesc(?string $lang = null): ?string
    {
        $lang = $lang ?? self::$currentLang;
        return $this->meta_desc[$lang] ?? $this->meta_desc['en'] ?? null;
    }

    public function setMetaDesc(?string $value, ?string $lang = null): static
    {
        $lang = $lang ?? self::$currentLang;
        $this->meta_desc[$lang] = $value;
        return $this;
    }

    // Methods to get/set all translations
    public function getTitleTranslations(): array
    {
        return $this->title;
    }

    public function setTitleTranslations(array $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getMetaDescTranslations(): array
    {
        return $this->meta_desc;
    }

    public function setMetaDescTranslations(array $meta_desc): static
    {
        $this->meta_desc = $meta_desc;
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

    public function isMultilang(): ?bool
    {
        return $this->multilang;
    }

    public function setMultilang(bool $multilang): static
    {
        $this->multilang = $multilang;
        return $this;
    }

    public static function setCurrentLang(string $lang): void
    {
        self::$currentLang = $lang;
    }

    public static function getCurrentLang(): string
    {
        return self::$currentLang;
    }
}
