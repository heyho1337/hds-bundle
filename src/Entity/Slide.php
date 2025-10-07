<?php

namespace App\Entity;

use App\Repository\SlideRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: SlideRepository::class)]
class Slide
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name_hu = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name_en = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $modified_at = null;

    #[ORM\Column(nullable: true)]
    private ?int $order_num = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title_hu = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title_en = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $alt_hu = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $alt_en = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slug_hu = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slug_en = null;

    #[ORM\ManyToOne]
    private ?MenuTarget $target = null;

    /*
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $file = null;
    */

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $text_hu = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $text_en = null;

    private ?string $name = null;
    private ?string $title = null;
    private ?string $alt = null;
    private ?string $slug = null;
    private ?string $text = null;

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

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

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

    public function getOrderNum(): ?int
    {
        return $this->order_num;
    }

    public function setOrderNum(?int $order_num): static
    {
        $this->order_num = $order_num;

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

    public function getAltHu(): ?string
    {
        return $this->alt_hu;
    }

    public function setAltHu(?string $alt_hu): static
    {
        $this->alt_hu = $alt_hu;

        return $this;
    }

    public function getAltEn(): ?string
    {
        return $this->alt_en;
    }

    public function setAltEn(?string $alt_en): static
    {
        $this->alt_en = $alt_en;

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

    public function getTarget(): ?MenuTarget
    {
        return $this->target;
    }

    public function setTarget(?MenuTarget $target): static
    {
        $this->target = $target;

        return $this;
    }

    /*
    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(?string $file): static
    {
        $this->file = $file;

        return $this;
    }
    */

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(?string $alt): static
    {
        $this->alt = $alt;
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

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getTextHu(): ?string
    {
        return $this->text_hu;
    }

    public function setTextHu(?string $text_hu): static
    {
        $this->text_hu = $text_hu;

        return $this;
    }

    public function getTextEn(): ?string
    {
        return $this->text_en;
    }

    public function setTextEn(?string $text_en): static
    {
        $this->text_en = $text_en;

        return $this;
    }

    public function getIdAsString(): string
    {
        return (string) $this->id;
    }
}
