<?php

namespace App\Security\Voter;


use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserVoter extends Voter
{
    public const EDIT_DELETE_VIEW   = 'USER_RIGHT';
    public const BAN                = 'USER_BAN';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT_DELETE_VIEW, self::BAN])
            && $subject instanceof \App\Entity\User;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();
        $targetUser = $subject;

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            $vote?->addReason('The user must be logged in to access this resource.');

            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            // Vérification des infos privées d'un profil. Est-ce que c'est son profil ou un administrateur qui modifie un utilisateur ?
            case self::EDIT_DELETE_VIEW:
                
                $isOwner = ($user === $targetUser);
                $isAdmin = in_array('ROLE_ADMIN', $user->getRoles());

                $targetIsAdmin = in_array('ROLE_ADMIN', $targetUser->getRoles());
                
                if (($isOwner || $isAdmin) && !$targetIsAdmin) {
                    return true;
                }

                if ($isOwner && $isAdmin) {
                    return true;
                }
                return false;
                break;
            // Vérifie que seul un administrateur puisse bannir un utilisateur et l'empêcher de s'auto-bannir.
            case self::BAN:

                $isAdmin = in_array('ROLE_ADMIN', $user->getRoles());
                $isOwner = ($user === $targetUser);
                $targetIsAdmin = in_array('ROLE_ADMIN', $targetUser->getRoles());

                if($isAdmin && !$isOwner && !$targetIsAdmin){
                    return true;
                }
                return false;
                break;
        }

        return false;
    }
}
