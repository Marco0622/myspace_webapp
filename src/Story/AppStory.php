<?php

namespace App\Story;

use App\Factory\UserFactory;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

#[AsFixture(name: 'main')]
final class AppStory extends Story
{
    public function build(): void
    {
        UserFactory::createMany(40);

        
        UserFactory::createOne([
            'email'     => 'user@gmail.com',
            'roles'     => [],
            'name'  => 'Doe',
            'firstname' => 'John'
        ]);

        UserFactory::createOne([
            'email'     => 'admin@gmail.com',
            'roles'     => ["ROLE_ADMIN"],
            'name'  => 'Doe',
            'firstname' => 'John'
        ]);

    }
}
