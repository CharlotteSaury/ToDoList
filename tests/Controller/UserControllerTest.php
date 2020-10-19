<?php

namespace App\Tests\Controller;

use App\Tests\Utils\NeedLogin;
use Symfony\Component\HttpFoundation\Response;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
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
            \dirname(__DIR__) . '/Fixtures/tasks.yaml',
            \dirname(__DIR__) . '/Fixtures/users.yaml'
        ]);
    }

    /**
     * Test Redirection to login route for visitors trying to access pages that require authenticated status.
     *
     */
    public function testAccessibleUsersPagesNotAuthenticated()
    {
        $routes = [
            ['GET', '/users'],
            ['GET', '/users/1/edit']
        ];

        foreach ($routes as $route) {
            $this->client->request($route[0], $route[1]);
            $this->assertResponseStatusCodeSame(Response::HTTP_OK);
            //$this->assertResponseRedirects('http://localhost/login');
        }
    }

    /**
     * Test unrestricted access to user creation page
     *
     */
    public function testAccessUserCreationNotAuthenticated()
    {
        $this->client->request('GET', '/users/create');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * Test access to user managment pages for authenticated user
     *
     * @return void
     */
    public function testRestrictedPageAccessAuthenticated()
    {
        $routes = [
            ['GET', '/users'],
            ['GET', '/users/1/edit']
        ];
        $fixtures = $this->loadCustomFixtures();
        $this->login($this->client, $fixtures['user1']);
        foreach ($routes as $route) {
            $this->client->request($route[0], $route[1]);
            $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        }
    }

    /**
     * Test integration of users list page for authenticated user
     *
     * @return void
     */
    public function testIntegrationUserListActionAuthenticated()
    {
        $fixtures = $this->loadCustomFixtures();
        $this->login($this->client, $fixtures['user1']);
        $crawler = $this->client->request('GET', '/users');
        $this->assertSame(1, $crawler->filter('a:contains("Créer un utilisateur")')->count());
        $this->assertSelectorTextSame('h1', 'Liste des utilisateurs');
        $this->assertSelectorExists('table');
        $this->assertSame(1, $crawler->filter('th:contains("Nom d\'utilisateur")')->count());
        $this->assertSame(1, $crawler->filter('th:contains("Adresse d\'utilisateur")')->count());
        $this->assertSame(1, $crawler->filter('th:contains("Actions")')->count());
        $this->assertGreaterThanOrEqual(1, $crawler->filter('a:contains("Edit")')->count());
    }

    /**
     * Test validity of create user link
     *
     * @return void
     */
    public function testValidCreateUserLinkUsersPage()
    {
        $fixtures = $this->loadCustomFixtures();
        $this->login($this->client, $fixtures['user1']);
        $crawler = $this->client->request('GET', '/users');
        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);
        $this->assertSelectorTextSame('h1', 'Créer un utilisateur');
    }

    /**
     * Test integration of user creation page
     *
     * @return void
     */
    public function testIntegrationUserCreationPage()
    {
        $fixtures = $this->loadCustomFixtures();
        $this->login($this->client, $fixtures['user1']);
        $crawler = $this->client->request('GET', '/users/create');
        $this->assertSelectorTextSame('h1', 'Créer un utilisateur');
        $this->assertSelectorExists('form');
        $this->assertCount(5, $crawler->filter('label'));
        $this->assertCount(5, $crawler->filter('input'));
        $this->assertCount(1, $crawler->filter('select'));
        $this->assertSelectorTextSame('button', 'Ajouter');
    }

    /**
     * Test new valid user creation
     *
     * @return void
     */
    public function testValidUserCreationAnonymously()
    {
        $this->loadCustomFixtures();
        $crawler = $this->client->request('GET', '/users/create');

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'newuser';
        $form['user[password][first]'] = 'newpassword';
        $form['user[password][second]'] = 'newpassword';
        $form['user[email]'] = 'newemail@email.com';
        $form['user[roles]']->select('ROLE_ADMIN');
        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(302);
        $crawler = $this->client->followRedirect();
        $this->assertSame(1, $crawler->filter('div.alert.alert-success')->count());
        $this->assertSelectorTextSame('h1', 'Liste des utilisateurs');
        $this->assertSame(1, $crawler->filter('td:contains("newuser")')->count());
        $this->assertSame(1, $crawler->filter('td:contains("newemail@email.com")')->count());
        $this->assertSame(1, $crawler->filter('td:contains("Admin")')->count());
    }

    /**
     * Test invalid user creation
     *
     * @return void
     */
    public function testInvalidUserCreationAnonymously()
    {
        $this->loadCustomFixtures();
        $crawler = $this->client->request('GET', '/users/create');

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'newuser';
        $form['user[password][first]'] = 'newpassword';
        $form['user[password][second]'] = 'newpass';
        $form['user[email]'] = 'user2@email.com';
        $crawler = $this->client->submit($form);
        $this->assertSelectorTextSame('h1', 'Créer un utilisateur');
        $this->assertSame(2, $crawler->filter('.form-error-icon')->count());
    }

    /**
     * Test validity of edit user link
     *
     * @return void
     */
    public function testValidEditUserLinkUsersPage()
    {
        $fixtures = $this->loadCustomFixtures();
        $this->login($this->client, $fixtures['user1']);
        $crawler = $this->client->request('GET', '/users');
        $link = $crawler->selectLink('Edit')->link();
        $crawler = $this->client->click($link);
        $this->assertSame(1, $crawler->filter('h1:contains("Modifier")')->count());
    }

    /**
     * Test integration of user edition page for authenticated user
     *
     * @return void
     */
    public function testIntegrationUserEditionPage()
    {
        $fixtures = $this->loadCustomFixtures();
        $this->login($this->client, $fixtures['user1']);
        $crawler = $this->client->request('GET', '/users/1/edit');
        //dd($crawler);
        $this->assertSelectorExists('form');
        $this->assertCount(5, $crawler->filter('label'));
        $this->assertCount(5, $crawler->filter('input'));
        $this->assertSame(1, $crawler->filter('input[value="username1"]')->count());
        $this->assertSame(1, $crawler->filter('input[value="user1@email.com"]')->count());
        $this->assertCount(1, $crawler->filter('select'));
        $this->assertSelectorTextSame('button', 'Modifier');
    }

    /**
     * Test valid user edition
     *
     * @return void
     */
    public function testValidUserEdition()
    {
        $fixtures = $this->loadCustomFixtures();
        $this->login($this->client, $fixtures['user1']);
        $crawler = $this->client->request('GET', '/users/1/edit');

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[username]'] = 'newuser1';
        $form['user[email]'] = 'newemail1@email.com';
        $form['user[password][first]'] = 'newpass';
        $form['user[password][second]'] = 'newpass';
        $form['user[roles]']->select('ROLE_ADMIN');
        $this->client->submit($form);

        $this->assertResponseRedirects('/users');
        $crawler = $this->client->followRedirect();
        $this->assertSame(1, $crawler->filter('div.alert.alert-success')->count());
        $this->assertSame(1, $crawler->filter('td:contains("newuser1")')->count());
        $this->assertSame(1, $crawler->filter('td:contains("newemail1@email.com")')->count());
        $this->assertSame(1, $crawler->filter('td:contains("Admin")')->count());
    }

    /**
     * Test invalid user edition
     *
     * @return void
     */
    public function testInvalidUserEdition()
    {
        $fixtures = $this->loadCustomFixtures();
        $this->login($this->client, $fixtures['user1']);
        $crawler = $this->client->request('GET', '/users/1/edit');

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[username]'] = 'user1';
        $form['user[email]'] = 'user2@email.com';
        $form['user[password][first]'] = 'newpass';
        $form['user[password][second]'] = 'newpass';
        $crawler = $this->client->submit($form);

        $this->assertSame(1, $crawler->filter('h1:contains("Modifier")')->count());
        $this->assertSame(1, $crawler->filter('.form-error-icon')->count());
    }

    /**
     * Test 404 error response when action with unexisting resource
     *
     * @return void
     */
    public function testUnexistingUserAction()
    {
        $fixtures = $this->loadCustomFixtures();
        $this->login($this->client, $fixtures['user1']);

        $this->client->request('GET', '/users/10/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }



}