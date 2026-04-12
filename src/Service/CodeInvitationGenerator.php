<?php 

namespace App\Service;

use App\Repository\UserRepository;


class CodeInvitationGenerator 
{
    public function __construct(
        private UserRepository  $userRepository,
    ) {}

    public function newCode(): string
    {

        do {
            $code = bin2hex(random_bytes(32));   
        } while ($this->userRepository->findOneBy(['code' => $code]));

        return $code;
    }


}