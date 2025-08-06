<?php

namespace App\State;

use ApiPlatform\State\ProviderInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Blog;
use App\Repository\BlogRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Cache\CacheInterface;

final class BlogBySlugStateProvider implements ProviderInterface
{
    public function __construct(
        private BlogRepository $blogRepository,
        private CacheInterface $cache
    )
    {

    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?Blog
    {
        

        if ($operation->getName() !== 'get_blog_by_slug') {
            return null;
        }

        if (!isset($uriVariables['slug'])) {
            throw new NotFoundHttpException('Slug parameter missing.');
        }

        $alias = $context['request']->attributes->get('slug');

        $cacheKey = sprintf('blog_by_slug_%s', $alias);
        $blog = $this->cache->get($cacheKey, function() use ($alias) {
            return $this->blogRepository->findOneBy(['slug' => $alias]);
        });

        if (!$blog) {
            throw new NotFoundHttpException('Blog not found.');
        }

        return $blog;
    }
}

