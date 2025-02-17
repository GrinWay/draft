<?php

namespace App\EventListener\Paginator;

use Doctrine\ORM\Query;
use Knp\Component\Pager\ArgumentAccess\ArgumentAccessInterface;
use Knp\Component\Pager\Event\ItemsEvent;
use Knp\Component\Pager\Event\Subscriber\Filtration\Doctrine\ORM\Query\WhereWalker;
use Knp\Component\Pager\Event\Subscriber\Filtration\Doctrine\ORM\QuerySubscriber;
use Knp\Component\Pager\Event\Subscriber\Paginate\Doctrine\ORM\Query\Helper as QueryHelper;
use Knp\Component\Pager\Exception\InvalidValueException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use function Symfony\Component\String\u;

#[AsEventListener('knp_pager.items', priority: 1)]
class QueryFilterPaginatorEventListener
{
    public function __construct(private readonly ArgumentAccessInterface $argumentAccess)
    {
    }

    public function __invoke(ItemsEvent $event): void
    {
        if (!$event->target instanceof Query) {
            return;
        }
        if (!$this->hasQueryParameter($event->options[PaginatorInterface::FILTER_VALUE_PARAMETER_NAME])) {
            return;
        }
        $filterValue = $this->getQueryParameter($event->options[PaginatorInterface::FILTER_VALUE_PARAMETER_NAME]);
        if ((empty($filterValue))) {
            return;
        }
        $filterName = null;
        if ($this->hasQueryParameter($event->options[PaginatorInterface::FILTER_FIELD_PARAMETER_NAME])) {
            $filterName = $this->getQueryParameter($event->options[PaginatorInterface::FILTER_FIELD_PARAMETER_NAME]);
        }
        if (!empty($filterName)) {
            $columns = $filterName;
        } elseif (!empty($event->options[PaginatorInterface::DEFAULT_FILTER_FIELDS])) {
            $columns = $event->options[PaginatorInterface::DEFAULT_FILTER_FIELDS];
        } else {
            return;
        }
        $value = $this->getQueryParameter($event->options[PaginatorInterface::FILTER_VALUE_PARAMETER_NAME]);
        if (str_contains($value, '*')) {
            $value = str_replace('*', '%', $value);
        }
        if (is_string($columns) && str_contains($columns, ',')) {
            $columns = explode(',', $columns);
        }
        $columns = (array)$columns;
        if (isset($event->options[PaginatorInterface::FILTER_FIELD_ALLOW_LIST])) {
            foreach ($columns as $column) {
                if (!in_array($column, $event->options[PaginatorInterface::FILTER_FIELD_ALLOW_LIST])) {
                    throw new InvalidValueException("Cannot filter by: [$column] this field is not in allow list");
                }
            }
        }

        if (\is_string($value)) {
            $value = (string) u($value)->ensureStart('%')->ensureEnd('%');
        }

        $event->target
            ->setHint(WhereWalker::HINT_PAGINATOR_FILTER_VALUE, $value)
            ->setHint(WhereWalker::HINT_PAGINATOR_FILTER_COLUMNS, $columns);

        QueryHelper::addCustomTreeWalker($event->target, WhereWalker::class);
    }

    protected function hasQueryParameter(string $name): bool
    {
        return $this->argumentAccess->has($name);
    }

    protected function getQueryParameter(string $name): ?string
    {
        return \trim($this->argumentAccess->get($name));
    }
}
