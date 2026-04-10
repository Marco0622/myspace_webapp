<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_user_home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            $user->setCreatedAt(new DateTimeImmutable('now'));

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('contact@marco-dev.fr', 'Contact MySpace'))
                    ->to((string) $user->getEmail())
                    ->subject('Confirmez votre adresse email - MySpace')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
                    ->context([
                        'user' => $user, 
                    ]),
                ['id' => $user->getId()]//ajoute des info a l'url de vérife mon tableau $extraparams
            );

            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_verify_email_pending');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        
        $id = $request->query->get('id'); 

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        
        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Votre adresse email a été vérifiée. Vous pouvez vous connecter.');

        return $this->redirectToRoute('app_login');
    }

    #[Route('/register/verify-pending', name: 'app_verify_email_pending')]
    public function verifyEmailPending(): Response
    {
        return $this->render('registration/verify_email_pending.html.twig');
    }

    #[Route('/verify/resend-request', name: 'app_resend_verification_email_request')]
    public function resendVerificationRequest(Request $request, UserRepository $userRepository, EmailVerifier $emailVerifier): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $user = $userRepository->findOneBy(['email' => $email]);

            if ($user && !$user->isVerified()) {
                
                $emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                    (new TemplatedEmail())
                        ->from(new Address('contact@marco-dev.fr', 'Contact MySpace'))
                        ->to($user->getEmail())
                        ->subject('Confirmez votre adresse email - MySpace')
                        ->htmlTemplate('registration/confirmation_email.html.twig')
                        ->context([
                            'user' => $user, 
                        ]),
                    ['id' => $user->getId()]
                );
            }

            
            $this->addFlash('success', 'Si ce compte existe et n\'est pas vérifié, un mail a été envoyé.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/resend_verification_request.html.twig');
    }
}
