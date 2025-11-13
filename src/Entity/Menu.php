<?php

namespace App\Entity;

use App\Repository\MenuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MenuRepository::class)]
class Menu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    private static string $currentLang = 'en';

    // JSON translation storage
    #[ORM\Column(type: Types::JSON)]
    private array $name = [];

    #[ORM\Column(type: Types::JSON)]
    private array $slug = [];

    #[ORM\ManyToOne]
    private ?MenuType $type = null;

    #[ORM\Column(nullable: true)]
    private ?int $order_num = null;

    #[ORM\ManyToOne]
    private ?MenuTarget $target = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $modified_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $file = null;

    #[ORM\ManyToOne]
    private ?Category $blog_category = null;

    #[ORM\ManyToOne]
    private ?Blog $blog = null;

    #[ORM\ManyToOne(inversedBy: 'children')]
    private ?MenuPosition $position = null;

    #[ORM\ManyToOne]
    private ?Article $article = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\ManyToOne]
    private ?Tag $tag = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    private ?self $parent = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parent', cascade: ['remove'], orphanRemoval: true)]
    private Collection $children;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->name = [];
        $this->slug = [];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    // Smart getters/setters
    public function getName(?string $lang = null): ?string
    {
        $lang = $lang ?? self::$currentLang;
        return $this->name[$lang] ?? $this->name['en'] ?? null;
    }

    public function setName(?string $value, ?string $lang = null): static
    {
        $lang = $lang ?? self::$currentLang;
        $this->name[$lang] = $value;
        return $this;
    }

    public function getSlug(?string $lang = null): ?string
    {
        $lang = $lang ?? self::$currentLang;
        return $this->slug[$lang] ?? $this->slug['en'] ?? null;
    }

    public function setSlug(?string $value, ?string $lang = null): static
    {
        $lang = $lang ?? self::$currentLang;
        $this->slug[$lang] = $value;
        return $this;
    }

    // Methods to get/set all translations
    public function getNameTranslations(): array
    {
        return $this->name;
    }

    public function setNameTranslations(array $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getSlugTranslations(): array
    {
        return $this->slug;
    }

    public function setSlugTranslations(array $slug): static
    {
        $this->slug = $slug;
        return $this;
    }

    // All other getters/setters remain the same...
    public function getType(): ?MenuType
    {
        return $this->type;
    }

    public function setType(?MenuType $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getOrderNum(): ?int
    {
        return $this->order_num;
    }

    public function setOrderNum(?int $order_num): static
    {
        $this->order_num = $order_num;
        return $this;
    }

    public function getTarget(): ?MenuTarget
    {
        return $this->target;
    }

    public function setTarget(?MenuTarget $target): static
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

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(?string $file): static
    {
        $this->file = $file;
        return $this;
    }

    public function getBlogCategory(): ?Category
    {
        return $this->blog_category;
    }

    public function setBlogCategory(?Category $blog_category): static
    {
        $this->blog_category = $blog_category;
        return $this;
    }

    public function getBlog(): ?Blog
    {
        return $this->blog;
    }

    public function setBlog(?Blog $blog): static
    {
        $this->blog = $blog;
        return $this;
    }

    public function getPosition(): ?MenuPosition
    {
        return $this->position;
    }

    public function setPosition(?MenuPosition $position): static
    {
        $this->position = $position;
        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): static
    {
        $this->article = $article;
        return $this;
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

    public function getTag(): ?Tag
    {
        return $this->tag;
    }

    public function setTag(?Tag $tag): static
    {
        $this->tag = $tag;
        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addMenu(self $menu): static
    {
        if (!$this->children->contains($menu)) {
            $this->children->add($menu);
            $menu->setParent($this);
        }
        return $this;
    }

    public function removeMenu(self $menu): static
    {
        if ($this->children->removeElement($menu)) {
            if ($menu->getParent() === $this) {
                $menu->setParent(null);
            }
        }
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

    public function __toString(): string
    {
        return $this->getName() ?? '';
    }
}
