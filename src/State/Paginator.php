<?php

namespace App\State;

use ApiPlatform\State\Pagination\PaginatorInterface;

class Paginator implements \IteratorAggregate, PaginatorInterface
{
    private array $items;
    private float $totalItems;

    public function __construct(array $items, float $totalItems)
    {
        $this->items = $items;
        $this->totalItems = $totalItems;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->items);
    }

    public function getTotalItems(): float
    {
        return $this->totalItems;
    }

    // Optionally, implement these for full paginator compatibility
    public function getCurrentPage(): float { return 1; /* add logic */ }
    public function getLastPage(): float { return 1; /* add logic */ }
    public function getItemsPerPage(): float { return count($this->items); }
    public function count(): int { return count($this->items); }
}
