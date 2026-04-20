<?php 

namespace App\Service;

use App\Repository\AccessRepository;
use App\Repository\InvitationRepository;

/**
 * Aide à la vérification des droits d'accès aux sessions utilisés dans votre session et invitation.
 */
class SessionManager 
{   
    /**
     * Injection des différents repositories.
     */
    public function __construct(
        private AccessRepository  $accesRepository,
        private InvitationRepository $invitationRepository
    ) {}

    /**
     * Vérification est ce que l'utilisateur connecter a le ROLE_OWNER et est ce que la session est bloqué.
     * 
     * @param object $objSession Session concernée.
     * @param object $objConnectUser Utilisateur connecté.
     * @return bool
     */
    public function isOwner(object $objSession, object $objConnectUser): bool
    {
        if($objSession->getIsBlocked()){
            return false;
        }

        $objUserAccess = $this->accesRepository->findOneBy([
            'session' => $objSession,
            'member' => $objConnectUser,
        ]);

        if(!empty($objUserAccess) && $objUserAccess->getRole() == 'ROLE_OWNER'){
            return true;
        } 

        return false;
    }


    /**
     * Vérification est ce que l'utilisateur connecter a le ROLE_EDITOR et est ce que la session est bloqué.
     * 
     * @param object $objSession Session concernée.
     * @param object $objConnectUser Utilisateur connecté.
     * @return bool
     */
    public function isEditor(object $objSession, object $objConnectUser): bool
    {
        if($objSession->getIsBlocked()){
            return false;
        }

        $objUserAccess = $this->accesRepository->findOneBy([
            'session' => $objSession,
            'member' => $objConnectUser,
        ]);

        if(!empty($objUserAccess) && $objUserAccess->getRole() == 'ROLE_EDITOR'){
            return true;
        } 

        return false;
    }

     /**
     * Vérification est ce que l'utilisateur connecter a le ROLE_VISITOR et est ce que la session est bloqué.
     * 
     * @param object $objSession Session concernée.
     * @param object $objConnectUser Utilisateur connecté.
     * @return bool
     */
    public function isVisitor(object $objSession, object $objConnectUser): bool
    {
        if($objSession->getIsBlocked()){
            return false;
        }

        $objUserAccess = $this->accesRepository->findOneBy([
            'session' => $objSession,
            'member' => $objConnectUser,
        ]);

        if(!empty($objUserAccess)  && $objUserAccess->getRole() == 'ROLE_VISITOR'){
            return true;
        } 

        return false;
    }

     /**
     * Vérification est-ce que l'utilisateur connecté a bien reçu l'invitation.
     * 
     * @param object $objSession Session concernée.
     * @param object $objConnectUser Utilisateur connecté.
     * @return bool
     */
    public function isReceiver(object $objSession, object $objConnectUser): bool
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