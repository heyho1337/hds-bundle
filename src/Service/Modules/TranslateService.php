<?php

namespace App\Service\Modules;

use Stichoza\GoogleTranslate\GoogleTranslate;
use App\Entity\Translate;
use App\Entity\Szavak;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Modules\LangService;
use App\Repository\SzavakRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Intl\Languages;

class TranslateService
{
    private string $source;
    private string $target;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly GoogleTranslate $translate,
        private readonly LangService $langService,
        private readonly SzavakRepository $szavakRepo,
        private CacheService $cache,
        private ParameterBagInterface $params
    ) {
        $this->source = $this->langService->getDefault();
        $this->translate->setSource($this->source);
    }

    public function getEasyAdminAvailableLangs(): array
    {
        $dir = __DIR__ . '/../../../vendor/easycorp/easyadmin-bundle/translations';
        $pattern = $dir . '/EasyAdminBundle.*.php';
        $files = glob($pattern);

        $langs = [];
        foreach ($files as $file) {
            // Extract the language code from the filename (e.g., "EasyAdminBundle.en.php" => "en")
            if (preg_match('/EasyAdminBundle\.([a-zA-Z\-_]+)\.php$/', $file, $matches)) {
                $langs[] = $matches[1];
            }
        }
        return $langs; // ['en', 'de', ...]
    }

    public function getAvailableLangs(): array
    {
        $easyAdminLangs = $this->getEasyAdminAvailableLangs(); // ['en', 'de', ...]
        $googleLangs = $this->translate->languages(); // ['en', 'de', ...]

        $choices = [];
        foreach ($easyAdminLangs as $code) {
            if (in_array($code, $googleLangs, true)) {
                // Use Symfony Intl to get readable language name
                // Default to $code if name not found
                $langName = \Symfony\Component\Intl\Languages::getName($code, 'en') ?? $code;
                $choices[$langName] = $code;
            }
        }
        return $choices;
    }

    public function setLangs(string $target): void
    {
        $this->target = $target;
        $this->translate->setTarget($target);
    }

    public function translateSzavak(string $code,$value = ''): string
    {   
        $szo = $this->langService->fordito($code);
        //dd($szo);
        if(!$szo){
            //dd($this->langService->getSzavak(),$szo,$code);
            $newWord = new Szavak();
            $newWord->setSzavakCode($code);

            foreach($this->langService->getLangs() as $lang){
                $szavakMethod = "setSzavak".ucfirst($lang->getCode());
                $this->translate->setTarget($lang->getCode());
                if($value != ''){
                    $translatedValue = $this->translate->translate($value);
                }
                else{
                    $translatedValue = $this->translate->translate($code);
                }
                $newWord->$szavakMethod($translatedValue);
            }

            $newWord->setCreatedAt(new \DateTimeImmutable());
            $newWord->setModifiedAt(new \DateTimeImmutable());

            $this->entityManager->persist($newWord);
            $this->entityManager->flush();

            foreach ($this->langService->getLangs() as $lang) {
                $langCode = $lang->getCode();
                $cacheKey = 'szavak:' . $langCode;
                
                // Delete old cache key
                $this->cache->delete($cacheKey);
                
                // Repopulate cache immediately
                $freshSzavak = $this->szavakRepo->findAll();
                $this->cache->set($cacheKey, $freshSzavak);
            }

            return ucfirst($translatedValue);
        }
        else{
            
        }

        return ucfirst($szo->getSzavak());
    }

    public function localizeEntity(object $entity): void
    {
        $reflection = new \ReflectionObject($entity);
                foreach ($reflection->getProperties() as $property) {
                    $name = $property->getName();
                    
                    // Match fields ending with _{lang}, e.g., name_en
                    if (preg_match('/^(.*)_' . $this->target . '$/', $name, $matches)) {
                        $base = $matches[1];
                        $property->setAccessible(true);
                        $localizedValue = $property->getValue($entity);

                        // Preferred: use setter, e.g., setName()
                        $setter = 'set' . ucfirst($base);
                        if (method_exists($entity, $setter)) {
                            $entity->$setter($localizedValue);
                        } elseif ($reflection->hasProperty($base)) {
                            // Fallback: set base property directly, e.g., $name
                            $baseProperty = $reflection->getProperty($base);
                            $baseProperty->setAccessible(true);
                            $baseProperty->setValue($entity, $localizedValue);
                        } else {
                            // Fallback: dynamic property assignment
                            $entity->$base = $localizedValue;
                        }
                    }
                }
    }

    public function localizePersistEntity(object $entity): void
    {
        $reflection = new \ReflectionObject($entity);

        foreach($this->langService->getLangs() as $lang){
            if($lang->getCode() !== $this->source){
                $langCode = $lang->getCode();
                $this->translate->setTarget($langCode);
                foreach ($reflection->getProperties() as $property) {
                    $name = $property->getName();
                    // Match fields ending with _{lang}, e.g., name_en
                    if (preg_match('/^(.*)_' . $langCode . '$/', $name, $matches)) {
                        $base = $matches[1];
                        $property->setAccessible(true);
                        $localizedValue = $property->getValue($entity);
                        if(!$localizedValue){
                            $fallbackProperty = $reflection->getProperty($base."_".$this->source);
                            if($fallbackProperty->getValue($entity)){
                                if(is_array($fallbackProperty->getValue($entity))){
                                    $newOptions = [];
                                    foreach($fallbackProperty->getValue($entity) as $item){
                                        $tranlatedItem = $this->translate->translate($item);
                                        array_push($newOptions,$tranlatedItem);
                                    }
                                    $localizedValue = $newOptions;
                                }
                                else{
                                    $localizedValue = $this->translate->translate($fallbackProperty->getValue($entity));
                                }
                                $property->setValue($entity,$localizedValue);
                                $this->entityManager->persist($entity);
                                $this->entityManager->flush();
                            }
                        }
                        

                        // Preferred: use setter, e.g., setName()
                        $setter = 'set' . ucfirst($base);
                        if (method_exists($entity, $setter)) {
                            $entity->$setter($localizedValue);
                        } elseif ($reflection->hasProperty($base)) {
                            // Fallback: set base property directly, e.g., $name
                            $baseProperty = $reflection->getProperty($base);
                            $baseProperty->setAccessible(true);
                            $baseProperty->setValue($entity, $localizedValue);
                        } else {
                            // Fallback: dynamic property assignment
                            $entity->$base = $localizedValue;
                        }
                    }
                }
            }
        }
        $key = strtolower($reflection->getShortName()).":";
        $this->cache->deleteByPattern($key);
    }
}