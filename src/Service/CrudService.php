<?php 

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class CrudService
{
    public function setEntity(EntityManagerInterface $entityManager, object $entityInstance): void
    {
        $entityInstance->setCreatedAt(new \DateTimeImmutable());
        $entityInstance->setModifiedAt(new \DateTimeImmutable());

        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }
}