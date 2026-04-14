<?php

namespace App\Controller;

use App\Entity\Invitation;
use App\Entity\Session;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @todo faire un voter pour la gestion des invitation
 */

#[Route('/invitation', name: 'app_invitation_')]
final class InvitationController extends AbstractController
{
    public function __construct(
        private LoggerInterface $logger
    ) {}

    #[Route('/decline/{id<\d+>}', name: 'decline')]
    public function decline(Invitation $invitation, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('decline_invitation', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $invitation->setResponce(false);
        $entityManager->flush();

        $this->addFlash('success', "Vous avez décliné l'invitation !");

        return $this->redirectToRoute('app_user_home');
    }

    #[Route('/accept/{id<\d+>}', name: 'accept')]
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

    #[Route('/cancel/{id<\d+>}', name: 'cancel')]
    public function cancel(): Response
    {
        return $this->render('invitation/index.html.twig', []);
    }

    #[Route('/create/{id<\d+>}', name: 'create')]
    public function create(
        Session $session,
        EntityManagerInterface $entityManager,
        Request $request,
        MailerInterface $mailer
    ): Response {
        if (!$this->isCsrfTokenValid('create_invitation', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        try {
            $codeReceiver = $request->request->get('code');
            $objUser = $entityManager->getRepository(User::class)->findOneBy([
                'code' => $codeReceiver
            ]);

            $objInvitation = new Invitation();

            $objInvitation->setSession($session);
            $objInvitation->setSenderId($this->getUser());
            $objInvitation->setReceiverId($objUser);
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

            $this->addFlash('success', "L'invitation a été envoyée !");

            return $this->redirectToRoute('app_session_home', [
                'id' => $session->getId(),
            ]);
        } catch (\Exception $e) {

            $this->logger->error("Erreur lors de la création d'une invitation", [
                'id' => $this->getUser(),
                'msg' => $e->getMessage()
            ]);


            $this->addFlash('danger', "L'action n'a pas pu être effectuée.");

            return $this->redirectToRoute('app_session_home', [
                'id' => $session->getId(),
            ]);
        }
    }
}
