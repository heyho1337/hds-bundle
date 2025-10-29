<?php

// src/Twig/AdminUrlGeneratorExtension.php
namespace App\Twig;

use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class AdminUrlGeneratorExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(private AdminUrlGenerator $adminUrlGenerator)
    {
    }

    public function getGlobals(): array
    {
        return [
            'adminUrlGenerator' => $this->adminUrlGenerator,
        ];
    }
}
