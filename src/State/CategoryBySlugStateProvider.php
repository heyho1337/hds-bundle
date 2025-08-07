<?php
// src/State/CategoryBySlugStateProvider.php

namespace App\State;

use ApiPlatform\State\ProviderInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;


final class CategoryBySlugStateProvider implements ProviderInterface
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private CacheInterface $cache,
        private readonly LoggerInterface $logger
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

        $request = $context['request'] ?? null;

        if ($request) {
            // Get all headers as an array
            $headers = $request->headers->all();
            $this->logger->info('fasz');
            $this->logger->info('PHP $_SERVER HTTP_AUTHORIZATION: ' . ($_SERVER['HTTP_AUTHORIZATION'] ?? 'not set'));
            $this->logger->info('Context keys: ' . implode(', ', array_keys($context)));
            $this->logger->info('Request URI: ' . $request->getRequestUri());
            $this->logger->info('Request method: ' . $request->getMethod());

            $headers = $request->headers->all();
            $this->logger->info('Authorization header: ' . $request->headers->get('Authorization', 'none'));

            // Optionally, log specific important headers or at most headers keys
            $this->logger->info('Request header keys: ' . implode(', ', array_keys($headers)));
        } else {
            // $request object not found in context
        }

        $alias = $request->attributes->get('slug');
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

