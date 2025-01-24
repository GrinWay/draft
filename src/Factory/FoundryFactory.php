<?php

namespace App\Factory;

use App\Entity\Foundry;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use function Symfony\Component\String\u;

/**
 * @extends PersistentProxyObjectFactory<Foundry>
 */
final class FoundryFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Foundry::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'title' => self::faker()->words(2, asText: true),
            'description' => self::faker()->text(255),
            'owner' => FoundryOwnerFactory::new(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
//            ->truncatedTitle(10)
//            ->afterInstantiate(static function (Foundry $foundry): void {
//            $foundry->setTitle(\sha1($foundry->getTitle()));
//            })
            ;
    }

    public function truncatedTitle(int $truncateTo): static
    {
        return $this->getTruncated('title', $truncateTo);
    }

    public function truncatedDescription(int $truncateTo): static
    {
        return $this->getTruncated('description', $truncateTo);
    }

    /**
     * Helper
     */
    private function getTruncated(string $field, int $truncateTo)
    {
        if (5 > $truncateTo) {
            $truncateTo = 5;
        }

        return $this->with([
            $field => (string)u(self::faker()->text(255))->truncate($truncateTo, '...'),
        ]);
    }
}
