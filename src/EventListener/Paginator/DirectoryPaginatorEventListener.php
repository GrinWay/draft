<?php

namespace App\EventListener\Paginator;

use Doctrine\ORM\Query;
use Knp\Component\Pager\Event\ItemsEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Finder\Finder;

#[AsEventListener('knp_pager.items', priority: 1)]
class DirectoryPaginatorEventListener
{
    public function __invoke(ItemsEvent $event): void
    {
        $target = $event->target;

        if (!\is_string($target) || !\is_dir($target)) {
            return;
        }

        $finder = new Finder();
        $finder
            ->in($target)
            ->ignoreDotFiles(false)
            ->ignoreVCS(false)
            ->depth('== 0')//
        ;

        $allItems = \iterator_to_array($finder);
        $event->items = \array_slice($allItems, $event->getOffset(), $event->getLimit());
        $event->count = \count($allItems);

        $event->stopPropagation();
    }
}
