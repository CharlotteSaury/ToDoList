<?php

namespace App\Tests\Controller;

use App\Tests\Utils\NeedLogin;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{
    use NeedLogin;
    use FixturesTrait;

    private $client = null;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    /**
     * Load fixtures files.
     *
     * @return array
     */
    public function loadCustomFixtures()
    {
        return $this->loadFixtureFiles([
            dirname(__DIR__).'/Fixtures/users.yaml',
        ]);
    }

    /**
     * Test login page not authenticated user.
     *
     * @return void
     */
    public function testLoginPage()
    {
        $crawler = $this->client->request('GET', '/login');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('form');
        $this->assertSame(1, $crawler->filter('html:contains("Nom d\'utilisateur :")')->count());
        $this->assertSame(1, $crawler->filter('html:contains("Mot de passe :")')->count());
        $this->assertCount(3, $crawler->filter('input'));
        $this->assertSelectorTextSame('button', 'Se connecter');
    }

    /**
     * Test login with valid credentials.
     *
     * @return void
     */
    public function testLoginValidCredentials()
    {
        $this->loadCustomFixtures();
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form();
        $form['_username'] = 'username1';
        $form['_password'] = 'password';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();
        $this->assertSame(1, $crawler->filter('html:contains("Bienvenue sur Todo List, l\'application vous permettant de gérer l\'ensemble de vos tâches sans effort !")')->count());
        $this->assertSelectorExists('a', 'Créer une nouvelle tâche');
        $this->assertSelectorExists('a', 'Consulter la liste des tâches à faire');
        $this->assertSelectorExists('a', 'Consulter la liste des tâches terminées');
        $this->assertSelectorNotExists('.alert.alert-danger', 'Invalid credentials.');
    }

    /**
     * Test login with invalid credentials.
     *
     * @return void
     */
    public function testLoginInvalidCredentials()
    {
        $this->loadCustomFixtures();
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form();
        $form['_username'] = 'invalidusername';
        $form['_password'] = 'invalidpass';
        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(302);
        $crawler = $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger', 'Invalid credentials.');
        $this->assertSelectorTextSame('button', 'Se connecter');
    }

    public function testLogout()
    {
        $fixtures = $this->loadCustomFixtures();
        $this->login($this->client, $fixtures['user1']);
        $this->client->request('GET', '/logout');
        $this->assertResponseRedirects('http://localhost/');
    }
}
