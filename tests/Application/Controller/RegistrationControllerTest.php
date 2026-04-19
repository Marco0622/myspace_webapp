<?php

namespace App\Tests\Application\Controller;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Attribute\ResetDatabase;

/**
 * Test unitaire pour l'inscription.
 */
#[ResetDatabase]
class RegistrationControllerTest extends WebTestCase
{
    /**
     * Vérification de l'affichage de la page register.
     */
    public function testRegisterPageShow(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
    }

    /**
     * Test du bon fonctionnement du formulaire d'inscription avec l'envoi du mail de vérification.
     */
    public function testRegisterSuccess(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();



        $client->submitForm("Valider", [
            'registration_form[email]'                  => "john.doe@email.com",
            'registration_form[name]'                   => "Doe",
            'registration_form[firstname]'              => "John",
            'registration_form[birthdate]'              => "2000-04-05",
            'registration_form[plainPassword][first]'   => "M?sth3erbe!!yyyyyyyyy",
            'registration_form[plainPassword][second]'  => "M?sth3erbe!!yyyyyyyyy",
            'registration_form[agreeTerms]'             => true,
        ]);

        $this->assertResponseRedirects();

        $this->assertEmailCount(1);

        $email = $this->getMailerMessage();

        $this->assertEmailAddressContains($email, 'To', 'john.doe@email.com');
        $this->assertEmailSubjectContains($email, "Confirmez votre adresse email - MySpace");
        $client->followRedirect();
        $this->assertRouteSame('app_verify_email_pending');
    }

    /**
     * Test de la tentative d'inscription avec un email déjà présent en base de données.
     */
    public function testRegisterWithExistingEmail(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();

        $objExtistingUser = UserFactory::createOne();

        $client->submitForm("Valider", [
            'registration_form[email]'                  => $objExtistingUser->getEmail(),
            'registration_form[name]'                   => "Doe",
            'registration_form[firstname]'              => "John",
            'registration_form[birthdate]'              => "2000-04-05",
            'registration_form[plainPassword][first]'   => "M?sth3erbe!!yyyyyyyyy",
            'registration_form[plainPassword][second]'  => "M?sth3erbe!!yyyyyyyyy",
            'registration_form[agreeTerms]'             => true,
        ]);

        $this->assertResponseStatusCodeSame(422);

        $this->assertAnySelectorTextContains('div', "Identifiants invalides ou déjà utilisés.");

        $this->assertEmailCount(0);
    }

    /**
     * Test de l'inscription avec deux mots de passe différents dans les champs de confirmation.
     */
    public function testRegisterWithMismatchPassword(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();

        $client->submitForm("Valider", [
            'registration_form[email]'                  => "john.doe@email.com",
            'registration_form[name]'                   => "Doe",
            'registration_form[firstname]'              => "John",
            'registration_form[birthdate]'              => "2000-04-05",
            'registration_form[plainPassword][first]'   => "M?sth3erbe!!yyyyyyyyy",
            'registration_form[plainPassword][second]'  => "M?sth3erbe!!zzzzzzzzz",
            'registration_form[agreeTerms]'             => true,
        ]);

        $this->assertResponseStatusCodeSame(422);

        $this->assertAnySelectorTextContains('div', "Les champs doivent être identiques");

        $this->assertEmailCount(0);
    }

    /**
     * Test du refus d'inscription si les conditions générales ne sont pas acceptées.
     */
    public function testRegisterWithoutAgreeTerms(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();

        $client->submitForm("Valider", [
            'registration_form[email]'                  => "john.doe@email.com",
            'registration_form[name]'                   => "Doe",
            'registration_form[firstname]'              => "John",
            'registration_form[birthdate]'              => "2000-04-05",
            'registration_form[plainPassword][first]'   => "M?sth3erbe!!yyyyyyyyy",
            'registration_form[plainPassword][second]'  => "M?sth3erbe!!yyyyyyyyy",
            'registration_form[agreeTerms]'             => false,
        ]);

        $this->assertResponseStatusCodeSame(422);

        $this->assertAnySelectorTextContains('div', "Vous devez accepter les conditions");

        $this->assertEmailCount(0);
    }
}
