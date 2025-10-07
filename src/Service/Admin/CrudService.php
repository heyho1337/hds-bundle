<?php 

namespace App\Service\Admin;

use Doctrine\ORM\EntityManagerInterface;
use App\Service\Modules\TranslateService;
use App\Service\Modules\LangService;

class CrudService
{

    public function __construct(
        private TranslateService $translateService,
        private readonly LangService $langService
    )
    {
        $this->translateService->setLangs($this->langService->getCurrentLang());
    }

    public function setEntity(EntityManagerInterface $entityManager, object $entityInstance): void
    {
        $entityInstance->setCreatedAt(new \DateTimeImmutable());
        $entityInstance->setModifiedAt(new \DateTimeImmutable());

        $entityManager->persist($entityInstance);
        $entityManager->flush();

        $this->translateService->localizePersistEntity($entityInstance);

        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }
}