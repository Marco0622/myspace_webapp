<?php

namespace App\Controller;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Gère les pages d'assistance et les informations légales de l'application.
 * Inclut les mentions légales, la politique de confidentialité et le formulaire de contact.
 */
final class SupportController extends AbstractController
{
    /**
     * Page d'accueil pour les utilisateurs non connectés.
     * 
     * @return Response
     */
    #[Route('/', name: 'app_support_home')]
    public function index(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_user_home');
        }    

        return $this->render('support/home.html.twig');
    }

    /**
     * Affiche la page des mentions légales.
     * 
     * @return Response
     */
    #[Route('/legal-notice', name: 'app_support_notice')]
    public function notice(): Response
    {
        return $this->render('support/notice.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }

    /**
     * Affiche la page de la politique de confidentialité.
     * 
     * @return Response
     */
    #[Route('/privacy-policy', name: 'app_support_policy')]
    public function policy(): Response
    {
        return $this->render('support/policy.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }

    /**
     * Gère l'affichage et le traitement du formulaire de contact avec envoi d'email.
     * 
     * @param Request $request
     * @param MailerInterface $mailer
     * @return Response
     */
    #[Route('/contact', name: 'app_support_contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $strFormError = "";

        $fullName = $request->getPayload()->get('fullName', '');
        $emailUser = $request->getPayload()->get('email', '');
        $subject = $request->getPayload()->get('subject', '');
        $message = $request->getPayload()->get('message', '');

        if ($request->isMethod('POST')) {

            $submittedToken = $request->getPayload()->get('_csrf_token');

            if (!$this->isCsrfTokenValid('contact_form', $submittedToken)) {
                $strFormError = "Jeton de sécurité invalide.";
            } elseif (empty($fullName) || empty($subject) || empty($message)) {
                $strFormError = "Tous les champs obligatoires ne sont pas remplis.";
            } elseif (!filter_var($emailUser, FILTER_VALIDATE_EMAIL)) {
                $strFormError = "L'adresse email n'est pas valide.";
            } else {

                $email = (new TemplatedEmail())
                    ->from(new Address('contact@marco-dev.fr', 'Contact Labority'))
                    ->to('slendsher48@gmail.com')
                    ->replyTo($emailUser)
                    ->subject('Contact Labority - ' . $fullName)
                    ->htmlTemplate('emails/contact.html.twig')
                    ->context([
                        'fullName' => $fullName,
                        'userEmail' => $emailUser,
                        'subject' => $subject,
                        'message' => $message,
                    ]);
                $mailer->send($email);
                $this->addFlash('success', 'L\'email a bien été envoyé !');

                if ($this->getUser()) {
                    return $this->redirectToRoute('app_user_home');
                }

                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('support/contact.html.twig', [
            'strFormError' => $strFormError,
            'fullName' => $fullName,
            'userEmail' => $emailUser,
            'subject' => $subject,
            'message' => $message,

        ]);
    }
}
