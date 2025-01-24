<?php

namespace App\Mapper\Todo;

use App\DTO\Todo\TodoDto;
use App\Entity\Todo;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: Todo::class, to: TodoDto::class)]
class TodoEntityToDtoMapper implements MapperInterface
{
    public function __construct(
        private readonly PropertyAccessorInterface $pa,
    )
    {
    }

    public function load(object $from, string $toClass, array $context): object
    {
        \dump($from);

        $to = new $toClass();
        $this->pa->setValue($to, 'id', $this->pa->getValue($from, 'id'));
        return $to;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $this->pa->setValue(
            $to,
            'title',
            $from->getTitle(),
        );
        $this->pa->setValue(
            $to,
            'description',
            $from->getDescription(),
        );

        return $to;
    }
}
