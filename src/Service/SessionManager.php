<?php 

namespace App\Service;

use App\Repository\AccessRepository;
use App\Repository\InvitationRepository;

class SessionManager 
{
    public function __construct(
        private AccessRepository  $accesRepository,
        private InvitationRepository $invitationRepository
    ) {}

    public function isOwner(object $objSession, object $objConnectUser): bool
    {
        $objUserAccess = $this->accesRepository->findOneBy([
            'session' => $objSession,
            'member' => $objConnectUser,
        ]);

        if(!empty($objUserAccess) && $objUserAccess->getRole() == 'ROLE_OWNER'){
            return true;
        } 

        return false;
    }

    public function isEditor(object $objSession, object $objConnectUser): string
    {
        $objUserAccess = $this->accesRepository->findOneBy([
            'session' => $objSession,
            'member' => $objConnectUser,
        ]);

        if(!empty($objUserAccess) && $objUserAccess->getRole() == 'ROLE_EDITOR'){
            return true;
        } 

        return false;
    }

    public function isVisitor(object $objSession, object $objConnectUser): string
    {
        $objUserAccess = $this->accesRepository->findOneBy([
            'session' => $objSession,
            'member' => $objConnectUser,
        ]);

        if(!empty($objUserAccess)  && $objUserAccess->getRole() == 'ROLE_VISITOR'){
            return true;
        } 

        return false;
    }

    public function isReceiver(object $objSession, object $objConnectUser): string
    {
        $objUserInvitation = $this->invitationRepository->findOneBy([
            'session' => $objSession,
            'receiver' => $objConnectUser,
        ]);

        if(!empty($objUserInvitation)  && $objUserInvitation ){
            return true;
        } 

        return false;
    }

    


}