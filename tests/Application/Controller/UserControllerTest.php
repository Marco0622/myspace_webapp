<?php

namespace App\Tests\Application\Controller;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Attribute\ResetDatabase;

/**
 * Test d'intégration pour le contrôleur UserController.
 * Couvre la gestion du profil, l'administration, et les actions de modération.
 */
#[ResetDatabase]
class UserControllerTest extends WebTestCase
{
    /**
     * Test que l'accès à la page d'accueil utilisateur nécessite une connexion.
     */
    public function testIndexRedirectsToLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseRedirects('/login');
    }

    /**
     * Test de l'affichage de la page d'accueil pour un utilisateur connecté.
     */
    public function testIndexIsSuccessfulWhenLoggedIn(): void
    {
        $client = static::createClient();
        $user = UserFactory::createOne();
        $client->loginUser($user);

        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Bonjour,'); 
    }

    /**
     * Test de la mise à jour des informations de profil par l'utilisateur lui-même.
     */
    public function testUpdateProfileSuccess(): void
    {
        $client = static::createClient();
        $user = UserFactory::createOne(['name' => 'AncienNom']);
        $client->loginUser($user);

        $crawler = $client->request('GET', '/user/' . $user->getId());
        $this->assertResponseIsSuccessful();

        $client->submitForm('Enregistrer', [
            'user_info_form[name]' => 'NouveauNom',
            'user_info_form[firstname]' => 'Jean',
            'user_info_form[email]' => $user->getEmail(),
            'user_info_form[birthdate]' => $user->getBirthdate()->format('Y-m-d'),
        ]);

        $this->assertResponseRedirects();

        $client->followRedirect();
        $this->assertRouteSame('app_user_home');
    }

    /**
     * Test du bannissement d'un utilisateur par un administrateur.
     */
    public function testBanUserAsAdmin(): void
    {
        $client = static::createClient();
        
        $admin = UserFactory::createOne(['roles' => ['ROLE_ADMIN']]);
        $targetUser = UserFactory::createOne();
        
        $client->loginUser($admin);
        $crawler = $client->request('GET', '/dashboard/users'); 

        $client->request('POST', '/user/ban/' . $targetUser->getId(), [
            '_token' => $client->getContainer()->get('security.csrf.token_manager')->getToken('ban')->getValue(),
        ]);

        $this->assertResponseRedirects('/dashboard/users');
        $this->assertNotNull($targetUser->refresh()->getBanAt());
    }

    /**
     * Test de la génération d'un nouveau code d'invitation.
     */
    public function testGenerateNewCodeInvitation(): void
    {
        $client = static::createClient();
        $user = UserFactory::createOne(['code' => 'OLD-CODE']);
        $client->loginUser($user);

        $token = $client->getContainer()->get('security.csrf.token_manager')->getToken('new_code')->getValue();
        
        $client->request('POST', '/user/new-code/' . $user->getId(), [
            '_token' => $token
        ]);

        $this->assertResponseRedirects('/user/' . $user->getId());

        $this->assertNotEquals('OLD-CODE', $user->refresh()->getCode());
        $this->assertNotNull($user->getCode());
    }

    /**
     * Test de sécurité : un utilisateur ne peut pas modifier le rôle d'un autre utilisateur.
     */
    public function testAccessDeniedForNonAdminOnRoles(): void
    {
        $client = static::createClient();
        $user = UserFactory::createOne(['roles' => ['ROLE_USER']]);
        $otherUser = UserFactory::createOne();
        
        $client->loginUser($user);

        $client->request('GET', '/user/role/' . $otherUser->getId());

        $this->assertResponseStatusCodeSame(403);
    }

    /**
     * Test de la suppression (soft delete).
     */
    public function testDeleteUser(): void
    {
        $client = static::createClient();
        $user = UserFactory::createOne();
        $client->loginUser($user);

        $token = $client->getContainer()->get('security.csrf.token_manager')->getToken('delete')->getValue();

        $client->request('POST', '/user/delete/' . $user->getId(), [
            '_token' => $token
        ]);

        $this->assertResponseRedirects('/dashboard/users');
        $this->assertNotNull($user->refresh()->getDeletedAt());
    }
}