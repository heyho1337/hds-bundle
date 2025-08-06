<?php
// src/State/CategoryBySlugStateProvider.php

namespace App\State;

use ApiPlatform\State\ProviderInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Cache\CacheInterface;

final class CategoryBySlugStateProvider implements ProviderInterface
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private CacheInterface $cache
    )
    {

    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?Category
    {
        

        if ($operation->getName() !== 'get_category_by_slug') {
            return null;
        }

        if (!isset($uriVariables['slug'])) {
            throw new NotFoundHttpException('Slug parameter missing.');
        }

        $alias = $context['request']->attributes->get('slug');
        $cacheKey = sprintf('category_by_slug_%s', $alias);
        $category = $this->cache->get($cacheKey, function() use ($alias) {
            return $this->categoryRepository->findOneBy(['slug' => $alias]);
        });

        if (!$category) {
            throw new NotFoundHttpException('Category not found.');
        }

        return $category;
    }
}

