<?php

namespace App\Tests\Application\Controller;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Attribute\ResetDatabase;

/**
 * Test d'intégration pour le système d'authentification (Login).
 */
#[ResetDatabase]
class AuthControllerTest extends WebTestCase
{   
    /**
     * Vérification de l'affichage de la page de connexion.
     */
    public function testLoginPageShow(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        
        $this->assertResponseIsSuccessful();
    }

    /**
     * Test d'une connexion réussie avec des identifiants valides.
     */
    public function testLoginSuccessWithCorrectCredentials(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        
        $this->assertResponseIsSuccessful();

        $objUser = UserFactory::createOne();

        $client->submitForm('Se connecter', [
            '_username' => $objUser->getEmail(),
            '_password' => UserFactory::DEFAULT_PASSWORD
        ]);

        $this->assertResponseRedirects();

        $client->followRedirect();
        $this->assertRouteSame('app_user_home');
    }
    
    /**
     * Test d'un échec de connexion suite à un mot de passe incorrect.
     */
    public function testLoginFailedWithBadPassword(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        
        $this->assertResponseIsSuccessful();

        $objUser = UserFactory::createOne();

        $client->submitForm('Se connecter', [
            '_username' => $objUser->getEmail(),
            '_password' => "BadPassword"
        ]);

        $this->assertResponseRedirects();  

        $client->followRedirect();
        $this->assertRouteSame('app_login');    
        
        $this->assertAnySelectorTextContains('div', 'Identifiants invalides.');
    }
    
    /**
     * Test d'un échec de connexion avec un email qui n'existe pas en base de données.
     */
    public function testLoginFailedWithBadEmail(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        
        $this->assertResponseIsSuccessful();

        $client->submitForm('Se connecter', [
            '_username' => "NotExist@mail.com",
            '_password' => "BadPassword"
        ]);

        $this->assertResponseRedirects();  

        $client->followRedirect();
        $this->assertRouteSame('app_login');    
        
        $this->assertAnySelectorTextContains('div', 'Identifiants invalides.');
        
    }
}