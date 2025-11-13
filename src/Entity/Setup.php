<?php

namespace App\Entity;

use App\Repository\SetupRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Langs;

#[ORM\Entity(repositoryClass: SetupRepository::class)]
class Setup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $multilang = null;

    #[ORM\ManyToOne(inversedBy: 'config')]
    private ?User $user = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\Column]
    private ?bool $process = null;

    /**
     * @var Collection<int, Component>
     */
    #[ORM\ManyToMany(targetEntity: Component::class, inversedBy: 'config')]
    private Collection $component;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $step = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $default_language = null;

    #[ORM\Column(nullable: true)]
    private ?int $tab = null;

    #[ORM\Column]
    private array $langs = [];

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $schema_type = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $modified_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $db_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $db_pw = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $db_user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $site_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $site_url = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $smtp_prefix = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $smtp_user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $smtp_pw = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $smtp_server = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $smtp_port = null;

    #[ORM\Column(length: 5, nullable: true)]
    private ?string $db_port = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $db_server_version = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $smtp_option = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $internal_step = null;

    #[ORM\Column]
    private ?int $user_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $root_dir = null;

    public function __construct()
    {
        $this->component = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSchemaType(): ?string
    {
        return $this->schema_type;
    }

    public function setSchemaType(?string $schema_type): static
    {
        $this->schema_type = $schema_type;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

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

    public function isProcess(): ?bool
    {
        return $this->process;
    }

    public function setProcess(bool $process): static
    {
        $this->process = $process;

        return $this;
    }

    /**
     * @return Collection<int, Component>
     */
    public function getComponent(): Collection
    {
        return $this->component;
    }

    public function addComponent(Component $component): static
    {
        if (!$this->component->contains($component)) {
            $this->component->add($component);
        }

        return $this;
    }

    public function removeComponent(Component $component): static
    {
        $this->component->removeElement($component);

        return $this;
    }

    public function getStep(): ?string
    {
        return $this->step;
    }

    public function setStep(?string $step): static
    {
        $this->step = $step;

        return $this;
    }

    public function getDefaultLanguage(): ?string
    {
        return $this->default_language;
    }

    public function setDefaultLanguage(?string $default_language): static
    {
        $this->default_language = $default_language;

        return $this;
    }

    public function getTab(): ?int
    {
        return $this->tab;
    }

    public function setTab(?int $tab): static
    {
        $this->tab = $tab;

        return $this;
    }

    public function getLangs(): array
    {
        return $this->langs;
    }

    public function setLangs(array $langs): static
    {
        $this->langs = $langs;

        return $this;
    }

    public function getDbName(): ?string
    {
        return $this->db_name;
    }

    public function setDbName(?string $db_name): static
    {
        $this->db_name = $db_name;

        return $this;
    }

    public function getDbPw(): ?string
    {
        return $this->db_pw;
    }

    public function getGeneratedDbPw(): ?string
    {
        return $this->db_pw;
    }

    public function setDbPw(?string $db_pw): static
    {
        $this->db_pw = $db_pw;

        return $this;
    }

    public function getDbUser(): ?string
    {
        return $this->db_user;
    }

    public function setDbUser(?string $db_user): static
    {
        $this->db_user = $db_user;

        return $this;
    }

    public function getSiteName(): ?string
    {
        return $this->site_name;
    }

    public function getNormalizedSiteName(): ?string
    {
        if ($this->site_name === null) {
            return null;
        }
        
        // Remove accents and convert to ASCII
        $normalized = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $this->site_name);
        
        // Convert to lowercase
        $normalized = strtolower($normalized);
        
        // Remove special characters, keep only alphanumeric and underscores
        $normalized = preg_replace('/[^a-z0-9_]/', '', $normalized);
        
        // Remove consecutive underscores
        $normalized = preg_replace('/_+/', '_', $normalized);
        
        // Trim underscores from start and end
        $normalized = trim($normalized, '_');
        
        // Limit length to reasonable database name length (e.g., 50 chars)
        $normalized = substr($normalized, 0, 50);
        
        return $normalized;
    }

    public function getGeneratedSiteName(): ?string
    {
        if ($this->site_name === null) {
            return null;
        }
        
        // Add 6 random alphanumeric characters
        $randomSuffix = bin2hex(random_bytes(3)); // Generates 6 hex characters
        
        return $this->getNormalizedSiteName() . '_' . $randomSuffix;
    }

    public function setSiteName(?string $site_name): static
    {
        $this->site_name = $site_name;

        return $this;
    }

    public function getSiteUrl(): ?string
    {
        return $this->site_url;
    }

    public function setSiteUrl(?string $site_url): static
    {
        $this->site_url = $site_url;

        return $this;
    }

    public function getSmtpPrefix(): ?string
    {
        return $this->smtp_prefix;
    }

    public function setSmtpPrefix(?string $smtp_prefix): static
    {
        $this->smtp_prefix = $smtp_prefix;

        return $this;
    }

    public function getSmtpUser(): ?string
    {
        return $this->smtp_user;
    }

    public function setSmtpUser(?string $smtp_user): static
    {
        $this->smtp_user = $smtp_user;

        return $this;
    }

    public function getSmtpPw(): ?string
    {
        return $this->smtp_pw;
    }

    public function setSmtpPw(?string $smtp_pw): static
    {
        $this->smtp_pw = $smtp_pw;

        return $this;
    }

    public function getSmtpServer(): ?string
    {
        return $this->smtp_server;
    }

    public function setSmtpServer(?string $smtp_server): static
    {
        $this->smtp_server = $smtp_server;

        return $this;
    }

    public function getSmtpPort(): ?string
    {
        return $this->smtp_port;
    }

    public function setSmtpPort(?string $smtp_port): static
    {
        $this->smtp_port = $smtp_port;

        return $this;
    }

    public function getDbPort(): ?string
    {
        return $this->db_port;
    }

    public function setDbPort(?string $db_port): static
    {
        $this->db_port = $db_port;

        return $this;
    }

    public function getDbServerVersion(): ?string
    {
        return $this->db_server_version;
    }

    public function setDbServerVersion(?string $db_server_version): static
    {
        $this->db_server_version = $db_server_version;

        return $this;
    }

    public function getSmtpOption(): ?string
    {
        return $this->smtp_option;
    }

    public function setSmtpOption(?string $smtp_option): static
    {
        $this->smtp_option = $smtp_option;

        return $this;
    }

    public function getInternalStep(): ?string
    {
        return $this->internal_step;
    }

    public function setInternalStep(?string $internal_step): static
    {
        $this->internal_step = $internal_step;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getRootDir(): ?string
    {
        return $this->root_dir;
    }

    public function setRootDir(?string $root_dir): static
    {
        $this->root_dir = $root_dir;

        return $this;
    }
}
