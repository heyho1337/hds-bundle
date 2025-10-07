<?php

namespace App\Entity;

use App\Repository\MenuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: MenuRepository::class)]
class Menu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    private static string $currentLang = 'en';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name_hu = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name_en = null;

    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Gedmo\Slug(fields: ['name_hu'])]
    private ?string $slug_hu = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Gedmo\Slug(fields: ['name_en'])]
    private ?string $slug_en = null;

    private ?string $slug = null;

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
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameHu(): ?string
    {
        return $this->name_hu;
    }

    public function setNameHu(?string $name_hu): static
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

    public function getSlugHu(): ?string
    {
        return $this->slug_hu;
    }

    public function setSlugHu(?string $slug_hu): static
    {
        $this->slug_hu = $slug_hu;

        return $this;
    }

    public function getSlugEn(): ?string
    {
        return $this->slug_en;
    }

    public function setSlugEn(?string $slug_en): static
    {
        $this->slug_en = $slug_en;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

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
            // set the owning side to null (unless already changed)
            if ($menu->getParent() === $this) {
                $menu->setParent(null);
            }
        }

        return $this;
    }
}
