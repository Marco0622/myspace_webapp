<?php

namespace App\Security\Voter;

use App\Service\SessionManager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class SessionVoter extends Voter
{
    public const EDITOR = 'IS_EDITOR_SESSION';
    public const OWNER = 'IS_OWNER_SESSION';
    public const VISITOR = 'IS_VISITOR_SESSION';

    public function __construct(
        private SessionManager  $sessionManager,
    ) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDITOR, self::OWNER, self::VISITOR])
            && $subject instanceof \App\Entity\Session;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            $vote?->addReason('The user must be logged in to access this resource.');

            return false;
        }

        switch ($attribute) {
            case self::OWNER:
               
                return $this->sessionManager->isOwner($subject, $user);

                break;

            case self::EDITOR:

                 if($this->sessionManager->isEditor($subject, $user) || $this->sessionManager->isOwner($subject, $user)) return true;

                break;
            case self::VISITOR:

                 if($this->sessionManager->isEditor($subject, $user) || $this->sessionManager->isVisitor($subject, $user) || $this->sessionManager->isOwner($subject, $user)) return true;

                break;
        }

        return false;
    }
}
