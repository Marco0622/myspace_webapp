<?php

namespace App\Security;

use App\Entity\User as AppUser;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Vérifie le statut du compte utilisateur lors de l'authentification.
 * Contrôle si l'email est vérifié et si le compte n'est ni supprimé, ni banni avant d'autoriser l'accès.
 */
class UserChecker implements UserCheckerInterface
{   
    /**
     * Vérifications effectuées avant l'authentification par rapport à l'email.
     * 
     * @param UserInterface $user
     */
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof AppUser) {
            return;
        }
    }

    /**
     * Vérifications effectuées après l'authentification(mot de passe, e-mail correct) (vérification email, ban, suppression).
     * 
     * @param UserInterface $user
     * @param TokenInterface|null $token
     */
    public function checkPostAuth(UserInterface $user, ?TokenInterface $token = null): void
    {
        if (!$user instanceof AppUser) {
            return;
        }

        if (!$user->isVerified()) {
            throw new CustomUserMessageAccountStatusException('Votre compte n\'est pas encore vérifié. Veuillez consulter vos emails. <a href="/verify/resend-request">Renvoyer le mail</a>');
        }

        if (!empty($user->getDeletedAt())) {
            throw new CustomUserMessageAccountStatusException('Ce compte a été supprimé. Pour plus d\'informations, veuillez <a href="/page/contact">nous contacter</a>');
        }

        if (!empty($user->getBanAt())) {
            throw new CustomUserMessageAccountStatusException('Ce compte est banni. Pour plus d\'informations, veuillez <a href="/page/contact">nous contacter</a>');
        }
    }
}