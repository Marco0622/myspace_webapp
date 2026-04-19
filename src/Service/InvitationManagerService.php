<?php

namespace App\Service;

use App\Entity\Session;
use App\Entity\User;
use App\Repository\AccessRepository;
use App\Repository\InvitationRepository;

/**
 * Service de vérification de l'éligibilité d'un utilisateur à rejoindre une session.
 * Vérifie qu'il n'est pas déjà membre et qu'il n'a pas d'invitation en attente.
 */
class InvitationManagerService
{
    public function __construct(
        private AccessRepository $accessRepository,
        private InvitationRepository $invitationRepository
    ) {}

    /**
     * Vérifie si l'utilisateur est déjà membre de la session.
     *
     * @param User $user
     * @param Session $session
     * @return bool
     */
    public function isAlreadyMember(User $user, Session $session): bool
    {
        $objAccess = $this->accessRepository->findOneBy([
            'member'  => $user,
            'session' => $session,
        ]);

        if(is_null($objAccess)){
            return false;
        }

        return true;
    }

    /**
     * Vérifie si l'utilisateur a déjà une invitation en attente (non refusée) pour cette session.
     *
     * @param User $user
     * @param Session $session
     * @return bool
     */
    public function hasPendingInvitation(User $user, Session $session): bool
    {
        $objInvitation = $this->invitationRepository->findOneBy([
            'receiver' => $user,
            'session'  => $session,
            'responce' => null,
        ]);

        if(is_null($objInvitation)){
            return false;
        }

        return true;
    }

    /**
     * Retourne un message d'erreur si l'utilisateur n'est pas éligible, null sinon.
     *
     * @param User $user
     * @param Session $session
     * @return string|null
     */
    public function verifySessionAndInvitation(User $user, Session $session): ?string
    {
        if ($this->isAlreadyMember($user, $session)) {
            return "Cet utilisateur est déjà membre de la session !";
        }

        if ($this->hasPendingInvitation($user, $session)) {
            return "Cet utilisateur a déjà une invitation en attente pour cette session !";
        }

        return null;
    }

}