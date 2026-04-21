<?php

namespace App\Controller;

use App\Entity\Invitation;
use App\Entity\Session;
use App\Entity\User;
use App\Service\InvitationManagerService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Gère la gestion des invitations : suppression, création, acceptation et refus.
 * Accès contrôlé par des Voters (INVITATION_RESPONCE, INVITATION_CANCEL, INVITATION_SEND).
 */
#[Route('/invitation', name: 'app_invitation_')]
final class InvitationController extends AbstractController
{

    /**
     * Refuse une invitation reçue.
     * 
     * @param Invitation $invitation
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/decline/{id<\d+>}', name: 'decline', methods: ['POST'])]
    #[IsGranted('INVITATION_RESPONCE', subject: 'invitation', message: "Droit insuffisant pour refuser l'invitation !")]
    public function decline(Invitation $invitation, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('decline_invitation', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $invitation->setResponce(false);
        $entityManager->flush();

        $this->addFlash('success', 'Vous avez décliné l\'invitation !');

        return $this->redirectToRoute('app_user_home');
    }

    /**
     * Accepte une invitation et redirige vers l'ajout d'accès.
     * 
     * @param Invitation $invitation
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/accept/{id<\d+>}', name: 'accept', methods: ['POST'])]
    #[IsGranted('INVITATION_RESPONCE', subject: 'invitation', message: "Droit insuffisant pour accepter l'invitation !")]
    public function accept(Invitation $invitation, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('accept_invitation', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $invitation->setResponce(true);
        $entityManager->flush();

        return $this->redirectToRoute('app_access_add', [
            'id' => $invitation->getSession()->getId(),
        ]);
    }

    /**
     * Supprime ou annule une invitation existante.
     * 
     * @param Invitation $invitation
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/delete/{id<\d+>}', name: 'delete', methods: ['POST'])]
    #[IsGranted('INVITATION_CANCEL', subject: 'invitation', message: "Droit insuffisant pour supprimer l'invitation !")]
    public function cancel(Invitation $invitation, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete_invitation', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $id = $request->request->get('sessionId');

        $entityManager->remove($invitation);
        $entityManager->flush();

        $this->addFlash('success', 'L\'invitation a été supprimé !');

        return $this->redirectToRoute('app_session_home', [
            'id' => $id,
        ]);

        return $this->render('invitation/index.html.twig', []);
    }

    /**
     * Création et envoi d'une invitation par email à un utilisateur via son code unique.
     * 
     * @param Session $session
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param MailerInterface $mailer
     * @return Response
     */
    #[Route('/create/{id<\d+>}', name: 'create', methods: ['POST'])]
    #[IsGranted('INVITATION_SEND', subject: 'session', message: "Droit insuffisant pour inviter un utilisateur !")]
    public function create(
        Session $session,
        EntityManagerInterface $entityManager,
        Request $request,
        MailerInterface $mailer,
        InvitationManagerService $invitationManager
    ): Response {
        if (!$this->isCsrfTokenValid('create_invitation', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }


        $codeReceiver = $request->request->get('code');
        $objUser = $entityManager->getRepository(User::class)->findOneBy([
            'code' => $codeReceiver
        ]);

        if ($objUser == $this->getUser()) {
            $this->addFlash('warning', 'Vous ne pouvez pas vous auto-envoyer une invitation !');

            return $this->redirectToRoute('app_session_home', [
                'id' => $session->getId(),
            ]);
        }

        if (is_null($objUser)) {
            $this->addFlash('warning', 'Erreur, Aucun utilisateur trouvé !');
            return $this->redirectToRoute('app_session_home', [
                'id' => $session->getId(),
            ]);
        }

        $reason = $invitationManager->verifySessionAndInvitation($objUser, $session);
        if ($reason !== null) {
            $this->addFlash('warning', $reason);
            return $this->redirectToRoute('app_session_home', [
                'id' => $session->getId()
            ]);
        }


        $objInvitation = new Invitation();

        $objInvitation->setSession($session);
        $objInvitation->setSender($this->getUser());
        $objInvitation->setReceiver($objUser);
        $objInvitation->setSendAt(new DateTimeImmutable('now'));


        $email = (new TemplatedEmail())
            ->from(new Address('contact@marco-dev.fr', 'Contact MySpace'))
            ->to($objUser->getEmail())
            ->subject('Nouvelle invitation - MySpace')
            ->htmlTemplate('invitation/email.html.twig')
            ->context([
                'user'    => $this->getUser(),
                'session' => $session,
            ]);

        $mailer->send($email);

        $entityManager->persist($objInvitation);
        $entityManager->flush();

        $this->addFlash('success', 'L\'invitation a été envoyée !');

        return $this->redirectToRoute('app_session_home', [
            'id' => $session->getId(),
        ]);
    }
}
