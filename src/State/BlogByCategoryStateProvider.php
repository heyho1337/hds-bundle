<?php

namespace App\State;

use ApiPlatform\State\ProviderInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Blog;
use App\Repository\BlogRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Cache\CacheInterface;

final class BlogByCategoryStateProvider implements ProviderInterface
{

    public function __construct(
        private BlogRepository $blogRepository,
        private CacheInterface $cache
    )
    {

    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?BlogPaginator
    {
        if ($operation->getName() !== 'get_blog_by_category') {
            return null;
        }

        if (!isset($uriVariables['id'])) {
            throw new NotFoundHttpException('Category ID parameter missing.');
        }

        //dd($context);
        $limit = $_ENV['PAGINATION_PER_PAGE'];
        $page = $context['filters']['page'] ?? 0;
        $offset = 0;
        if($page > 1){
            $offset = $page * $limit;
        }

        $cacheKey = sprintf('blog_by_category_%d_%d_%d', $uriVariables['id'], $limit, $offset);

        $blogList = $this->cache->get($cacheKey, function() use ($uriVariables, $limit, $offset) {
            return $this->blogRepository->findByCategoryPaginated($uriVariables['id'], $limit, $offset);
        });

        $totalItemsCacheKey = sprintf('blog_total_by_category_%d', $uriVariables['id']);

        $totalItems = $this->cache->get($totalItemsCacheKey, function() use ($uriVariables) {
            return $this->blogRepository->countByCategory($uriVariables['id']);
        });

        if (!$blogList) {
            throw new NotFoundHttpException('BlogList not found.');
        }

        return new BlogPaginator($blogList, $totalItems);
    }

}
