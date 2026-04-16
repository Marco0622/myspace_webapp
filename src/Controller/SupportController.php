<?php

namespace App\Controller;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;

final class SupportController extends AbstractController
{
    #[Route('/legal-notice', name: 'app_support_notice')]
    public function notice(): Response
    {
        return $this->render('support/notice.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }

    #[Route('/privacy-policy', name: 'app_support_policy')]
    public function policy(): Response
    {
        return $this->render('support/policy.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }

    #[Route('/contact', name: 'app_support_contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $strFormError = ""; 

        $fullName = $request->getPayload()->get('fullName','');
        $emailUser = $request->getPayload()->get('email','');
        $subject = $request->getPayload()->get('subject','');
        $message = $request->getPayload()->get('message','');

        if($request->isMethod('POST')) {

            $submittedToken = $request->getPayload()->get('_csrf_token');
            
            if (!$this->isCsrfTokenValid('contact_form', $submittedToken)) {
                $strFormError = "Jeton de sécurité invalide.";
            } elseif (empty($fullName) || empty($subject) || empty($message)) {
                $strFormError = "Tous les champs obligatoires ne sont pas remplis.";
            } elseif (!filter_var($emailUser, FILTER_VALIDATE_EMAIL)) {
                $strFormError = "L'adresse email n'est pas valide.";
            } else {

                $email = new TemplatedEmail()
                    ->from($emailUser)
                    ->to(new Address('slendsher48@gmail.com'))
                    ->subject('Contact MySpace - '. $fullName)
                    ->htmlTemplate('emails/contact.html.twig')
                    ->context([
                        'fullName' => $fullName,
                        'userEmail' => $emailUser,
                        'subject' => $subject,
                        'message' => $message,
                    ])
                ;
                $mailer->send($email);
                $this->addFlash('success', "L'email a bien été envoyé !");

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
