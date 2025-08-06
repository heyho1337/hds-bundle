<?php

namespace App\Entity;

use App\Repository\BlogRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use App\State\BlogByCategoryStateProvider;
use App\State\BlogBySlugStateProvider;

#[ApiResource(
    normalizationContext: ['groups' => ['blogpost:read']],
    cacheHeaders: [
        'max_age' => 3600,
        'shared_max_age' => 3600,
        'public' => true,
    ],
    operations: [
        new Get(),
        new GetCollection(),
        new Get(
            name: 'get_blog_by_category',
            uriTemplate: '/blogs/category/{id}',
            uriVariables: [
                'id' => new Link(),
            ],
            provider: BlogByCategoryStateProvider::class,
            //read: false
        ),
        new Get(
            name: 'get_blog_by_slug',
            uriTemplate: '/blogs/slug/{slug}',
            uriVariables: [
                'slug' => new Link(),
            ],
            provider: BlogBySlugStateProvider::class,
            //read: false
        ),
    ],
    security: "is_granted('ROLE_ADMIN')"
)]
#[ORM\Entity(repositoryClass: BlogRepository::class)]
class Blog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['blogpost:read'])]
    private ?int $id = null;

    #[Groups(['blogpost:read'])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Groups(['blogpost:read'])]
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[Groups(['blogpost:read'])]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $text = null;

    #[Groups(['blogpost:read'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $meta_desc = null;

    #[Groups(['blogpost:read'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $short_desc = null;

    #[Groups(['blogpost:read'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column]
    #[Groups(['blogpost:read'])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    #[Groups(['blogpost:read'])]
    private ?\DateTimeImmutable $modified_at = null;

    #[ORM\ManyToOne(inversedBy: 'articles', targetEntity: Category::class)]
    #[Groups(['blogpost:read'])]
    private ?Category $category = null;

    #[Groups(['blogpost:read'])]
    private ?string $category_slug = null;

    /**
     * @var Collection<int, Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'articles')]
    #[Groups(['blogpost:read'])]
    private Collection $tags;

    #[Gedmo\Slug(fields: ['name'])]
    #[ORM\Column(length: 255)]
    #[Groups(['blogpost:read'])]
    private ?string $slug = null;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategorySlug(): ?string
    {
        $this->category_slug = $this->getCategory()->getSlug();
        return $this->category_slug;
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): static
    {
        $this->text = $text;

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

    public function getShortDesc(): ?string
    {
        return $this->short_desc;
    }

    public function setShortDesc(?string $short_desc): static
    {
        $this->short_desc = $short_desc;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(Tag $tag): static
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

}
