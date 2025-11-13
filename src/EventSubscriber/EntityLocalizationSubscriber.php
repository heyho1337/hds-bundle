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
        'Words', 'Category', 'Tag', 'Blog', 'Accordion', 'AccordionItem', 
        'Gallery', 'GalleryImage', 'Article', 'Slide', 'Form', 'FormInput', 
        'FormType', 'Menu', 'MenuPosition', 'MenuTarget', 'MenuType', 
        'Schema', 'Config', 'Words'
    ];

    public function __construct(
        private readonly RequestStack $requestStack,
    ) {
        $this->target = $this->resolveLangFromCookie();
    }

    public function __invoke(PostLoadEventArgs $args): void
    {
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

            if($name === 'currentLang'){
                $property->setValue($this->resolveLangFromCookie());
            }
        }
    }
}
