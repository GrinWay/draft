<?php

namespace App\Factory;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<User>
 */
final class UserFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher,
    )
    {
    }

    public static function class(): string
    {
        return User::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'email' => self::faker()->email(),
            'password' => '123',
            'roles' => [self::faker()->randomElement(['ROLE_ADMIN', 'ROLE_SUBSCRIBED', 'ROLE_TEACHER'])],
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        $userPasswordHasher = $this->userPasswordHasher;

        return $this->afterInstantiate(static function (User $user) use ($userPasswordHasher): void {
            $hashedPassword = $userPasswordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
        });
    }
}
