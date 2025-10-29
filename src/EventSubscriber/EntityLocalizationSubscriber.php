<?php

namespace App\EventSubscriber;

use Doctrine\ORM\Events;
use Symfony\Bridge\Doctrine\Attribute\AsDoctrineListener;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\Event\PostLoadEventArgs;

#[AsDoctrineListener(event: Events::postLoad)]
class EntityLocalizationSubscriber
{
    private string $target;

    private array $entitiesToLocalize = [
        'Szavak','Category','Tag','Blog','Accordion','AccordionItem','Gallery','GalleryImage','Article','Slide'
    ];

    public function __construct(
        private readonly RequestStack $requestStack,
    )
    {
        $this->target = $this->resolveLangFromCookie();
    }

    public function __invoke(PostLoadEventArgs $args): void
    {
        //dump("postLoad called");
        $entity = $args->getObject();
        if (!$this->shouldAutoLocalize($entity)) {
            return;
        }

        $this->localizeEntity($entity);
    }

    private function shouldAutoLocalize(object $entity): bool
    {
        $class = (new \ReflectionClass($entity))->getShortName();
        return in_array($class, $this->entitiesToLocalize, true);
    }

    private function resolveLangFromCookie(): string
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return $_ENV['DEFAULT_LANG'];
        }

        $langHeader = $request->headers->get('x-language');
        if ($langHeader) {
            return $langHeader;
        }

        $lang = $request->cookies->get('lang');
        if ($lang) {
            return $lang;
        }

        return $_ENV['DEFAULT_LANG'];
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


                        //dd($property,$this->target);
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
