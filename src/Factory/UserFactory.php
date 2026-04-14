<?php

namespace App\Factory;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;


final class UserFactory extends PersistentObjectFactory
{
    public const DEFAULT_PASSWORD = "P@ssw0rd";

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher         //< Injection de la dépendance pour la classe
    )
    {
    }

    #[\Override]
    public static function class(): string
    {
        return User::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'email'         => self::faker()->email(),
            'firstname'     => self::faker()->firstName(),
            'name'          => self::faker()->lastName(),
            'password'      => $this->userPasswordHasher->hashPassword(new User(), self::DEFAULT_PASSWORD),
            'isVerified'    => self::faker()->boolean(),
            'birthdate'     => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'created_at'    => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'roles'         => [],
            'code'          => self::faker()->unique()->regexify('[A-Z0-9]{64}'),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(User $user): void {})
        ;
    }
}