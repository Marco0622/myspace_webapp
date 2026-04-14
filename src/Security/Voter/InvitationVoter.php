<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class InvitationVoter extends Voter
{
    public const RESPONSE = 'INVITATION_RESPONCE';
    public const SEND = 'INVITATION_SEND';
    public const CANCEL = 'INVITATION_CANCEL';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute === self::SEND) {
            return $subject instanceof \App\Entity\Session;
        }

        return in_array($attribute, [self::RESPONSE, self::CANCEL])
            && $subject instanceof \App\Entity\Invitation;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            $vote?->addReason('The user must be logged in to access this resource.');

            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::RESPONSE:
                
                if($user == $subject->getReceiver()){
                    return true;
                } 
                return false;
                break;

            case self::SEND:

                foreach ($subject->getSessionAccesses() as $access) {
                    if ($access->getMember() === $user && $access->getRole() === 'ROLE_OWNER') { 
                        return true;
                    }
                }
                
               
                return false;
                break;

            case self::CANCEL:

                $session = $subject->getSession();

                foreach ($session->getSessionAccesses() as $access) {
                    if ($access->getMember() === $user && $access->getRole() === 'ROLE_OWNER') { 
                        return true;
                    }
                }
                
                return false;
                break;
        }

        return false;
    }
}
