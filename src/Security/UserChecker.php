<?php

namespace App\Security;

use App\Entity\User as AppUser;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof AppUser) {
            return;
        }
    }

    

    public function checkPostAuth(UserInterface $user, ?TokenInterface $token = null): void
    {
        if (!$user instanceof AppUser) {
            return;
        }

        //Est ce que le mail est vérifier?
        if (!$user->isVerified()) {
            throw new CustomUserMessageAccountStatusException('Votre compte n\'est pas encore vérifié. Veuillez consulter vos emails. <a href="/verify/resend-request">Renvoyer le mail</a>');
        }

        //Est ce que le compte a était supprimer?
        if (!empty($user->getDeletedAt())) {
            throw new CustomUserMessageAccountStatusException('Ce compte a été supprimé. Pour plus d\'informations, veuillez <a href="/page/contact">nous contacter</a>');
        }

         //Est ce que le compte était bannie?
        if (!empty($user->getBanAt())) {
            throw new CustomUserMessageAccountStatusException('Ce compte est banni. Pour plus d\'informations, veuillez <a href="/page/contact">nous contacter</a>');
        }
    }
}