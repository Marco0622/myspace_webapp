<?php

namespace App\Story;

use App\Factory\AccessFactory;
use App\Factory\InvitationFactory;
use App\Factory\ReportFactory;
use App\Factory\SessionFactory;
use App\Factory\StorageFactory;
use App\Factory\UserFactory;
use DateTimeImmutable;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

#[AsFixture(name: 'main')]
final class AppStory extends Story
{
    public function build(): void
    {

        StorageFactory::createMany(9);

        SessionFactory::createMany(30);

        ReportFactory::createMany(60);
        
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
        $now = new DateTimeImmutable('now');
        
        $arrAccess = [
            
            ['joined_at' => $now, 'member' => UserFactory::find(['id' => 1]),  'role' => 'ROLE_OWNER',   'session' => SessionFactory::find(['id' => 1])],
            ['joined_at' => $now, 'member' => UserFactory::find(['id' => 2]),  'role' => 'ROLE_OWNER',   'session' => SessionFactory::find(['id' => 2])],
            ['joined_at' => $now, 'member' => UserFactory::find(['id' => 3]),  'role' => 'ROLE_OWNER',   'session' => SessionFactory::find(['id' => 3])],
            ['joined_at' => $now, 'member' => UserFactory::find(['id' => 4]),  'role' => 'ROLE_OWNER',   'session' => SessionFactory::find(['id' => 4])],
            ['joined_at' => $now, 'member' => UserFactory::find(['id' => 5]),  'role' => 'ROLE_OWNER',   'session' => SessionFactory::find(['id' => 5])],
            
            ['joined_at' => $now, 'member' => UserFactory::find(['id' => 11]), 'role' => 'ROLE_VISITOR', 'session' => SessionFactory::find(['id' => 1])],
            ['joined_at' => $now, 'member' => UserFactory::find(['id' => 12]), 'role' => 'ROLE_EDITOR',  'session' => SessionFactory::find(['id' => 2])],
            ['joined_at' => $now, 'member' => UserFactory::find(['id' => 13]), 'role' => 'ROLE_VISITOR', 'session' => SessionFactory::find(['id' => 3])],
            ['joined_at' => $now, 'member' => UserFactory::find(['id' => 14]), 'role' => 'ROLE_EDITOR',  'session' => SessionFactory::find(['id' => 4])],
            ['joined_at' => $now, 'member' => UserFactory::find(['id' => 15]), 'role' => 'ROLE_VISITOR', 'session' => SessionFactory::find(['id' => 5])],
        ];

        AccessFactory::createSequence($arrAccess);

        $arrInv = [
            ['send_at' => $now, 'sender_id' => UserFactory::find(['id' => 1]), 'receiver_id' => UserFactory::find(['id' => 20]), 'responce' => null, 'session' => SessionFactory::find(['id' => 1])],
            ['send_at' => $now, 'sender_id' => UserFactory::find(['id' => 2]), 'receiver_id' => UserFactory::find(['id' => 21]), 'responce' => null, 'session' => SessionFactory::find(['id' => 2])],
            ['send_at' => $now, 'sender_id' => UserFactory::find(['id' => 3]), 'receiver_id' => UserFactory::find(['id' => 22]), 'responce' => null, 'session' => SessionFactory::find(['id' => 3])],
            ['send_at' => $now, 'sender_id' => UserFactory::find(['id' => 4]), 'receiver_id' => UserFactory::find(['id' => 23]), 'responce' => null, 'session' => SessionFactory::find(['id' => 4])],
            ['send_at' => $now, 'sender_id' => UserFactory::find(['id' => 5]), 'receiver_id' => UserFactory::find(['id' => 24]), 'responce' => null, 'session' => SessionFactory::find(['id' => 5])],
            ['send_at' => $now, 'sender_id' => UserFactory::find(['id' => 1]), 'receiver_id' => UserFactory::find(['id' => 25]), 'responce' => null, 'session' => SessionFactory::find(['id' => 1])],
            ['send_at' => $now, 'sender_id' => UserFactory::find(['id' => 2]), 'receiver_id' => UserFactory::find(['id' => 26]), 'responce' => null, 'session' => SessionFactory::find(['id' => 2])],
            ['send_at' => $now, 'sender_id' => UserFactory::find(['id' => 3]), 'receiver_id' => UserFactory::find(['id' => 27]), 'responce' => null, 'session' => SessionFactory::find(['id' => 3])],
            ['send_at' => $now, 'sender_id' => UserFactory::find(['id' => 4]), 'receiver_id' => UserFactory::find(['id' => 28]), 'responce' => null, 'session' => SessionFactory::find(['id' => 4])],
            ['send_at' => $now, 'sender_id' => UserFactory::find(['id' => 5]), 'receiver_id' => UserFactory::find(['id' => 29]), 'responce' => null, 'session' => SessionFactory::find(['id' => 5])],
        ];

        InvitationFactory::createSequence($arrInv);

    }
}
