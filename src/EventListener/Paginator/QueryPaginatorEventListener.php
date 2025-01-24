<?php

namespace App\EventListener\Paginator;

use Doctrine\ORM\Query;
use Knp\Component\Pager\Event\ItemsEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener('knp_pager.items', priority: 1)]
class QueryPaginatorEventListener
{
    public function __invoke(ItemsEvent $event): void
    {
        $target = $event->target;
        if (!$target instanceof Query) {
            return;
        }

        $event->items = $target->getResult();
        $event->count = \count($event->items ?: []);

        $event->stopPropagation(); // prevents default behaviour
    }
}
