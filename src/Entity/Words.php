<?php

namespace App\Entity;

use App\Repository\WordsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WordsRepository::class)]
class Words
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // ✅ Type is array, not string
    #[ORM\Column(type: Types::JSON)]
    private array $label = [];

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $modified_at = null;

    private static string $currentLang = 'en';

    // ✅ Initialize in constructor
    public function __construct()
    {
        $this->label = [];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    // ✅ Getter returns string for current language
    public function getLabel(?string $lang = null): ?string
    {
        $lang = $lang ?? self::$currentLang;
        return $this->label[$lang] ?? $this->label['en'] ?? null;
    }

    // ✅ Setter accepts value and optional language
    public function setLabel(?string $value, ?string $lang = null): static
    {
        $lang = $lang ?? self::$currentLang;
        $this->label[$lang] = $value;
        return $this;
    }

    // ✅ Methods to get/set all translations
    public function getLabelTranslations(): array
    {
        return $this->label;
    }

    public function setLabelTranslations(array $label): static
    {
        $this->label = $label;
        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;
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

    public static function getCurrentLang(): string
    {
        return self::$currentLang;
    }
}