<?php

namespace App\Tests\Controller;

use App\Tests\Utils\NeedLogin;
use Symfony\Component\HttpFoundation\Response;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
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
     * @dataProvider provideAuthenticatedUserAccessibleUrls
     */
    public function testUnaccessiblePagesNotAuthenticated($method, $url)
    {
        $this->client->request($method, $url);
        $this->assertResponseRedirects('http://localhost/login');
    }

    public function provideAuthenticatedUserAccessibleUrls()
    {
        return [
            ['GET', '/tasks'],
            ['GET', '/tasks/create'],
            ['GET', '/tasks/1/edit'],
            ['GET', '/tasks/1/toggle'],
            ['GET', '/tasks/1/delete']
        ];
    }

    /**
     * Test access to task creation page for authenticated user
     *
     * @return void
     */
    public function testRestrictedPageAccessAuthenticated()
    {
        $routes = [
            ['GET', '/tasks'],
            ['GET', '/tasks/create'],
            ['GET', '/tasks/1/edit']
        ];
        $fixtures = $this->loadCustomFixtures();
        $this->login($this->client, $fixtures['user1']);
        foreach ($routes as $route) {
            $this->client->request($route[0], $route[1]);
            $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        }
    }

    /**
     * Test integration of done task list page for authenticated user
     *
     * @return void
     */
    public function testIntegrationListActionAuthenticated()
    {
        $fixtures = $this->loadCustomFixtures();
        $this->login($this->client, $fixtures['user1']);
        $this->client->request('GET', '/tasks');
        $this->assertSelectorExists('a', 'Se déconnecter');
        $this->assertSelectorExists('a', 'Créer une tâche');
        $this->assertSelectorExists('.caption');
        $this->assertSelectorExists('.glyphicon-ok');
        $this->assertSelectorExists('.thumbnail h4 a');
        $this->assertSelectorExists('.thumbnail button', 'Marquer comme non terminée');
        $this->assertSelectorExists('.thumbnail button', 'Supprimer');
        $this->assertSelectorNotExists('.glyphicon-remove');
        $this->assertSelectorNotExists('.thumbnail button', 'Marquer comme faite');
    }

    /**
     * Test integration of not done task list page for authenticated user
     *
     * @return void
     */
    /*public function testIntegrationNotDoneListActionAuthenticated()
    {
        $fixtures = $this->loadCustomFixtures();
        $this->login($this->client, $fixtures['user1']);
        $this->client->request('GET', '/tasks');
        $this->assertSelectorExists('a', 'Se déconnecter');
        $this->assertSelectorExists('a', 'Créer une tâche');
        $this->assertSelectorExists('.caption');
        $this->assertSelectorExists('.thumbnail h4 a');
        $this->assertSelectorExists('.thumbnail button', 'Supprimer');
        $this->assertSelectorExists('.glyphicon-remove');
        $this->assertSelectorExists('.thumbnail button', 'Marquer comme faite');
        $this->assertSelectorNotExists('.glyphicon-ok');
        $this->assertSelectorNotExists('.thumbnail button', 'Marquer comme non terminée');
        
    }*/

    /**
     * Test integration of task creation page for authenticated user
     *
     * @return void
     */
    public function testIntegrationTaskCreationPage()
    {
        $fixtures = $this->loadCustomFixtures();
        $this->login($this->client, $fixtures['user1']);
        $crawler = $this->client->request('GET', '/tasks/create');

        $this->assertSelectorExists('a', 'Se déconnecter');
        $this->assertSelectorExists('a', 'Retour à la liste des tâches');
        $this->assertSelectorExists('form');
        $this->assertCount(2, $crawler->filter('input'));
        $this->assertCount(1, $crawler->filter('textarea'));
        $this->assertSame(1, $crawler->filter('html:contains("Title")')->count());
        $this->assertSame(1, $crawler->filter('html:contains("Content")')->count());
        $this->assertSelectorExists('button', 'Ajouter');
    }

    /**
     * Test new task creation
     *
     * @return void
     */
    public function testTaskCreation()
    {
        $fixtures = $this->loadCustomFixtures();
        $this->login($this->client, $fixtures['user1']);
        $crawler = $this->client->request('GET', '/tasks/create');

        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'New Task';
        $form['task[content]'] = 'New content';
        $this->client->submit($form);

        $this->assertResponseRedirects('/tasks');
        $crawler = $this->client->followRedirect();
        $this->assertSame(1, $crawler->filter('div.alert.alert-success')->count());
        $this->assertSelectorExists('h4 a', 'New Task');
        $this->assertSelectorExists('p', 'New content');
    }

    /**
     * Test integration of task creation page for authenticated user
     *
     * @return void
     */
    public function testIntegrationTaskEditionPage()
    {
        $fixtures = $this->loadCustomFixtures();
        $this->login($this->client, $fixtures['user1']);
        $crawler = $this->client->request('GET', '/tasks/1/edit');

        $this->assertSelectorExists('a', 'Se déconnecter');
        //$this->assertSelectorExists('a', 'Retour à la liste des tâches');
        $this->assertSelectorExists('form');
        $this->assertCount(2, $crawler->filter('input'));
        $this->assertCount(1, $crawler->filter('textarea'));
        $this->assertSame(1, $crawler->filter('html:contains("Title")')->count());
        $this->assertSame(1, $crawler->filter('html:contains("Content")')->count());
        $this->assertSelectorExists('button', 'Modifier');
        $this->assertInputValueNotSame('task[title]', '');
    }

    /**
     * Test new task edition
     *
     * @return void
     */
    public function testTaskEdition()
    {
        $fixtures = $this->loadCustomFixtures();
        $this->login($this->client, $fixtures['user1']);
        $crawler = $this->client->request('GET', '/tasks/1/edit');

        $form = $crawler->selectButton('Modifier')->form();
        $form['task[title]'] = 'updated title';
        $form['task[content]'] = 'updated content';
        $this->client->submit($form);

        $this->assertResponseRedirects('/tasks');
        $crawler = $this->client->followRedirect();
        $this->assertSame(1, $crawler->filter('div.alert.alert-success')->count());
        $this->assertSelectorExists('h4 a', 'updated title');
        $this->assertSelectorExists('p', 'updated content');
    }

    /**
     * Test toggle action - set task1 is_done to true
     *
     * @return void
     */
    public function testToggleActionSetIsDone()
    {
        $fixtures = $this->loadCustomFixtures();
        $this->login($this->client, $fixtures['user1']);
        $crawler = $this->client->request('GET', '/tasks/1/toggle');
        $this->assertResponseRedirects('/tasks');
        $crawler = $this->client->followRedirect();
        $this->assertSame(1, $crawler->filter('div.alert.alert-success')->count());
        $this->assertSelectorExists('#task1 .glyphicon-ok');
        $this->assertSelectorNotExists('#task1 .glyphicon-remove');
    }

    /**
     * Test toggle action - set task3 is_done to false
     *
     * @return void
     */
    public function testToggleActionSetIsNotDone()
    {
        $fixtures = $this->loadCustomFixtures();
        $this->login($this->client, $fixtures['user1']);
        $crawler = $this->client->request('GET', '/tasks/3/toggle');
        $this->assertResponseRedirects('/tasks');

        $crawler = $this->client->followRedirect();
        $this->assertSame(1, $crawler->filter('div.alert.alert-success')->count());
        $this->assertSelectorExists('#task3 .glyphicon-remove');
        $this->assertSelectorNotExists('#task3 .glyphicon-ok');
    }

    /**
     * Test delete action
     *
     * @return void
     */
    public function testDeleteAction()
    {
        $fixtures = $this->loadCustomFixtures();
        $this->login($this->client, $fixtures['user1']);
        $crawler = $this->client->request('GET', '/tasks/1/delete');
        $this->assertResponseRedirects('/tasks');
        $crawler = $this->client->followRedirect();
        $this->assertSame(1, $crawler->filter('div.alert.alert-success')->count());
        $this->assertSelectorNotExists('#task1');
    }

    /**
     * Test 404 error response when action with unexisting resource
     *
     * @return void
     */
    public function testUnexistingTaskAction()
    {
        $routes = [
            ['GET', '/tasks/10/edit'],
            ['GET', '/tasks/10/toggle'],
            ['GET', '/tasks/10/delete']
        ];
        $fixtures = $this->loadCustomFixtures();
        $this->login($this->client, $fixtures['user1']);

        foreach ($routes as $route) {
            $this->client->request($route[0], $route[1]);
            $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        }
    }
}
