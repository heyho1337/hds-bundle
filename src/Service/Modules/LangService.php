<?php

namespace App\Service\Modules;

use App\Entity\Szavak;
use App\Entity\Langs;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\LangsRepository;
use App\Repository\SzavakRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class LangService
{
    private string $currentLang;
    private array $szavak;
    private array $langList;
    private Langs $default;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LangsRepository $langsRepo,
        private readonly RequestStack $requestStack,
        private readonly SzavakRepository $szavakRepo,
        private CacheService $cache,
    ) {

        $langRepo = $this->langsRepo;
        
        $this->default = $this->cache->getOrSet('default_lang', function () use ($langRepo) {
            return $langRepo->findOneBy(['active' => 1, 'default' => 1]);
        });

        $this->currentLang = $this->resolveLangFromCookie();

        $repo = $this->szavakRepo;
        $this->szavak = $this->cache->getOrSet('szavak:' . $this->currentLang, function () use ($repo) {
            return $repo->findAll();
        });

        $this->langList = $this->cache->getOrSet('langList', function () use ($langRepo) {
            return $langRepo->findBy(['active' => 1]);
        });
    }

    public function getSzavak(): array
    {
        return $this->szavak;
    }

    private function resolveLangFromCookie(): string
    {

        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return $this->getDefault();
        }


        $langHeader = $request->headers->get('x-language');
        if ($langHeader) {
            return $langHeader;
        }

        $lang = $request->cookies->get('lang');
        if ($lang) {
            return $lang;
        }

        return $this->getDefault();
    }

    public function setLang(string $lang): void
    {
        $this->currentLang = $lang;
    }

    public function getCurrentLang(): string
    {
        return $this->currentLang;
    }

    public function getCurrentLangForMethods(): string
    {
        return ucfirst($this->currentLang);
    }

    public function getDefaultObject(): Langs
    {
        return $this->default;
    }

    public function getDefault(): string
    {
        return $this->default->getCode();
    }

    public function getLangs(): array
    {
        return $this->langList;
    }

    public function fordito(string $szo_code): Szavak|string
    {
        foreach ($this->szavak as $szo) {
            if ($szo->getSzavakCode() === $szo_code) {
                return $szo;
            }
        }

        return '';
    }
}
