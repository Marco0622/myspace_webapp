<?php

namespace App\Service;

use App\Repository\UserRepository;


class CodeInvitationGenerator
{
    /**
     * Injection des différents repositories.
     */
    public function __construct(
        private UserRepository  $userRepository,
    ) {}

    /**
     * Permet de s'assurer du fait que le code d'invitation de l'utilisateur soit unique.
     */
    public function newCode(): string
    {

        do {
            $code = bin2hex(random_bytes(32));
        } while ($this->userRepository->findOneBy(['code' => $code]));

        return $code;
    }
}
