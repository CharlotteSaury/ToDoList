<?php

namespace App\Tests\Controller;

use App\Tests\Utils\NeedLogin;
use Symfony\Component\HttpFoundation\Response;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
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
            dirname(__DIR__).'/Fixtures/tasks.yaml'
        ]);
    }

    /**
     * Test redirection to login when not authenticated user ask for homepage
     *
     */
    public function testHomepageNotAuthenticated()
    {
        $this->client->request('GET', '/');
        $this->assertResponseRedirects('http://localhost/login');
    }

    /**
     * Test access to homepage for authenticated user
     *
     * @return void
     */
    public function testHomepageAuthenticated()
    {
        $fixtures = $this->loadCustomFixtures();
        $this->login($this->client, $fixtures['user1']);
        
        $crawler = $this->client->request('GET', '/');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSame(1, $crawler->filter('html:contains("Bienvenue sur Todo List, l\'application vous permettant de gérer l\'ensemble de vos tâches sans effort !")')->count());
        $this->assertSelectorExists('a', 'Se déconnecter');
        $this->assertSelectorExists('a', 'Créer une nouvelle tâche');
        $this->assertSelectorExists('a', 'Consulter la liste des tâches à faire');
        $this->assertSelectorExists('a', 'Consulter la liste des tâches terminées');
        $this->assertSelectorExists('a', 'Créer un utilisateur');
        
    }

    public function createCrawlerHomepage(string $user = 'user1')
    {
        $fixtures = $this->loadCustomFixtures();
        $this->login($this->client, $fixtures[$user]);
        $crawler = $this->client->request('GET', '/');
        return $crawler;
    }


    /**
     * Test validity of task creation link
     *
     * @return void
     */
    public function testValidTaskCreationLink()
    {
        $crawler = $this->createCrawlerHomepage();
        $link = $crawler->selectLink('Créer une nouvelle tâche')->link();
        $crawler = $this->client->click($link);
        $this->assertSelectorExists('form');
        $this->assertSelectorTextSame('button', 'Ajouter');
    }

    /**
     * Test validity of to do task list link
     *
     * @return void
     */
    public function testValidToDoTaskListLink()
    {
        $crawler = $this->createCrawlerHomepage();
        $link = $crawler->selectLink('Consulter la liste des tâches à faire')->link();
        $crawler = $this->client->click($link);
        //$this->assertSelectorExists('.thumbnail');
        $this->assertSame(4, $crawler->filter('.thumbnail')->count());
        $this->assertSame(4, $crawler->filter('.glyphicon-remove')->count());
        $this->assertSelectorNotExists('.glyphicon-ok');
    }

    /**
     * Test validity of done task list link
     *
     * @return void
     */
    public function testValidIsDoneTaskListLink()
    {
        $crawler = $this->createCrawlerHomepage();
        $link = $crawler->selectLink('Consulter la liste des tâches terminées')->link();
        $crawler = $this->client->click($link);
        $this->assertSame(1, $crawler->filter('.thumbnail')->count());
        $this->assertSame(1, $crawler->filter('.glyphicon-ok')->count());
        $this->assertSelectorNotExists('.glyphicon-remove');
    }

    /**
     * Test validity of create user link
     *
     * @return void
     */
    public function testValidCreateUserLink()
    {
        $crawler = $this->createCrawlerHomepage('admin1');
        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);
        $this->assertSelectorTextSame('h1', 'Créer un utilisateur');
    }

    /**
     * Test validity of logout link
     *
     * @return void
     */
    public function testValidLogoutLink()
    {
        $crawler = $this->createCrawlerHomepage();
        $link = $crawler->selectLink('Se déconnecter')->link();
        $crawler = $this->client->click($link);
        $this->assertResponseRedirects('http://localhost/');
    }
}
