<?php

namespace App\Service\Modules;

use App\Entity\Words;
use App\Entity\Langs;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\LangsRepository;
use App\Repository\WordsRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Repository\EasyAdminGoogleLangsRepository;

class LangService
{
    private string $currentLang;
    private array $words;
    private array $langList;
    private Langs $default;
    private array $eagl;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LangsRepository $langsRepo,
        private readonly RequestStack $requestStack,
        private readonly WordsRepository $wordsRepo,
        private readonly EasyAdminGoogleLangsRepository $eaglRepo,
        private CacheService $cache,
    ) {

        $langRepo = $this->langsRepo;
        
        $this->default = $this->cache->getOrSet('default_lang', function () use ($langRepo) {
            return $langRepo->findOneBy(['active' => 1, 'default' => 1]);
        });
        $this->currentLang = $this->resolveLangFromCookie();

        $eaglsRepo = $this->eaglRepo;
        
        $this->eagl = $this->cache->getOrSet('eagl', function () use ($eaglsRepo) {
            return $eaglsRepo->findAll();
        });

        //$this->cache->set('words:' . $this->currentLang,$this->wordsRepo->findAll());

        $repo = $this->wordsRepo;
        $this->words = $this->cache->getOrSet('words:' . $this->currentLang, function () use ($repo) {
            return $repo->findAll();
        });

        $this->langList = $this->cache->getOrSet('langList', function () use ($langRepo) {
            return $langRepo->findBy(['active' => 1]);
        });
    }

    public function getWords(): array
    {
        return $this->words;
    }

    public function getEagl(): array
    {
        return $this->eagl;
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

    public function fordito(string $word_code): Words|string
    {
        foreach ($this->words as $word) {
            if ($word->getCode() === $word_code) {
                return $word;
            }
        }

        return '';
    }
}
