<?php

namespace App\Service\Modules;

use Stichoza\GoogleTranslate\GoogleTranslate;
use App\Entity\Translate;
use App\Entity\Words;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Modules\LangService;
use App\Repository\WordsRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class TranslateService
{
    private string $source;
    private string $target;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly GoogleTranslate $translate,
        private readonly LangService $langService,
        private readonly WordsRepository $wordsRepo,
        private CacheService $cache,
        private ParameterBagInterface $params,
    ) {
        $this->source = $this->langService->getDefault();
        $this->translate->setSource($this->source);
    }

    public function getAvailableLangs(): array
    {
        $availableLaguages = $this->langService->getEagl();

        $choices = [];
        foreach ($availableLaguages as $lang) {
            $choices[$lang->getName()] = $lang->getCode();
        }
        $this->entityManager->flush();

        return $choices;
    }

    public function setLangs(string $target): void
    {
        $this->target = $target;
        $this->translate->setTarget($target);
        
        // ✅ Also set current language on Words entity
        Words::setCurrentLang($target);
    }

    public function translateWords(string $code, $value = ''): string
    {
        $szo = $this->langService->fordito($code);
        
        if (!$szo) {
            $newWord = new Words();
            $newWord->setCode($code);

            // ✅ Build the translations array
            $translations = [];
            
            foreach ($this->langService->getLangs() as $lang) {
                $langCode = $lang->getCode();
                $this->translate->setTarget($langCode);
                
                if ($value != '') {
                    $translatedValue = $this->translate->translate($value);
                } else {
                    $translatedValue = $this->translate->translate($code);
                }
                
                // ✅ Add to translations array
                $translations[$langCode] = $translatedValue;
            }
            
            // ✅ Set the entire translations array at once
            $newWord->setLabelTranslations($translations);

            $newWord->setCreatedAt(new \DateTimeImmutable());
            $newWord->setModifiedAt(new \DateTimeImmutable());

            $this->entityManager->persist($newWord);
            $this->entityManager->flush();

            foreach ($this->langService->getLangs() as $lang) {
                $langCode = $lang->getCode();
                $cacheKey = 'words:' . $langCode;
                
                $this->cache->delete($cacheKey);
                
                $freshWords = $this->wordsRepo->findAll();
                $this->cache->set($cacheKey, $freshWords);
            }

            // ✅ Return the translated value for the current target language
            return ucfirst($translations[$this->target] ?? $translations[$this->source] ?? $code);
        }

        // ✅ Use the smart getter with current language
        return ucfirst($szo->getLabel($this->target));
    }

    public function localizeEntity(object $entity): void
    {
        // ✅ No longer needed - entities handle this internally with static $currentLang
        // This method can be deprecated or removed
    }

    public function localizePersistEntity(object $entity): void
    {
        $reflection = new \ReflectionObject($entity);

        // ✅ Detect JSON translation fields (properties that are arrays storing translations)
        $this->localizeJsonTranslations($entity, $reflection);

        $key = strtolower($reflection->getShortName()) . ":";
        $this->cache->deleteByPattern($key);
    }

    /**
     * Handle JSON translation fields (label, title, name, text, etc. that are JSON arrays)
     */
    private function localizeJsonTranslations(object $entity, \ReflectionObject $reflection): void
    {
        foreach ($reflection->getProperties() as $property) {
            $name = $property->getName();
            $property->setAccessible(true);
            
            // Skip if not initialized
            if (!$property->isInitialized($entity)) {
                continue;
            }
            
            $value = $property->getValue($entity);
            
            // ✅ Check if this is a JSON translation field (an array with language keys)
            if (!is_array($value) || empty($value)) {
                continue;
            }
            
            // Check if it looks like a translation array (has language codes as keys)
            $keys = array_keys($value);
            $looksLikeTranslations = !empty($keys) && is_string($keys[0]) && strlen($keys[0]) === 2;
            
            if (!$looksLikeTranslations) {
                continue;
            }
            
            // Get the source language value
            $sourceValue = $value[$this->source] ?? null;
            
            if (!$sourceValue) {
                continue; // Skip if no source value exists
            }
            
            // Auto-translate to all other languages if they're empty
            $updated = false;
            foreach ($this->langService->getLangs() as $lang) {
                $langCode = $lang->getCode();
                
                if ($langCode === $this->source) {
                    continue; // Skip source language
                }
                
                // Only translate if target language is empty
                if (empty($value[$langCode])) {
                    $this->translate->setTarget($langCode);
                    
                    if (is_array($sourceValue)) {
                        // Handle array values (like options)
                        $translatedArray = [];
                        foreach ($sourceValue as $item) {
                            $translatedArray[] = $this->translate->translate($item);
                        }
                        $value[$langCode] = $translatedArray;
                    } else {
                        // Handle string values
                        $value[$langCode] = $this->translate->translate($sourceValue);
                    }
                    $updated = true;
                }
            }
            
            // Update the entity if translations were added
            if ($updated) {
                $property->setValue($entity, $value);
            }
        }
        
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }
}
