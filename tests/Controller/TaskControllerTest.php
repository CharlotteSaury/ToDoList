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
    public function testUnaccessibleTaskPagesNotAuthenticated($method, $url)
    {
        $this->client->request($method, $url);
        $this->assertResponseRedirects('http://localhost/login');
    }

    public function provideAuthenticatedUserAccessibleUrls()
    {
        return [
            ['GET', '/tasks/done'],
            ['GET', '/tasks/todo'],
            ['GET', '/tasks/create'],
            ['GET', '/tasks/1/edit'],
            ['GET', '/tasks/1/toggle'],
            ['GET', '/tasks/1/delete']
        ];
    }

    /**
     * Test access to restricted pages related to tasks for authenticated user
     *
     * @return void
     */
    public function testRestrictedPageAccessAuthenticated()
    {
        $routes = [
            ['GET', '/tasks/todo'],
            ['GET', '/tasks/done'],
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
     * Test integration of to do task list page for authenticated user
     *
     * @return void
     */
    public function testIntegrationToDoTaskListActionAuthenticated()
    {
        $fixtures = $this->loadCustomFixtures();
        $this->login($this->client, $fixtures['user1']);
        $crawler = $this->client->request('GET', '/tasks/todo');
        $this->assertSame(1, $crawler->filter('a:contains("Se déconnecter")')->count());
        $this->assertSame(1, $crawler->filter('a:contains("Créer une tâche")')->count());

        $this->assertSelectorExists('.caption');
        $this->assertSelectorExists('.thumbnail h4 a');
        $this->assertSame(2, $crawler->filter('.thumbnail button:contains("Supprimer")')->count());
        $this->assertSelectorExists('.glyphicon-remove');
        $this->assertSame(2, $crawler->filter('.thumbnail button:contains("Marquer comme faite")')->count());
        $this->assertSelectorNotExists('.glyphicon-ok');
        $this->assertSame(0, $crawler->filter('.thumbnail button:contains("Marquer comme terminée")')->count());
    }

    /**
     * Test integration of done task list page for authenticated user
     *
     * @return void
     */
    public function testIntegrationDoneListActionAuthenticated()
    {
        $fixtures = $this->loadCustomFixtures();
        $this->login($this->client, $fixtures['user1']);
        $crawler = $this->client->request('GET', '/tasks/done');

        $this->assertSame(1, $crawler->filter('a:contains("Se déconnecter")')->count());
        $this->assertSame(1, $crawler->filter('a:contains("Créer une tâche")')->count());

        $this->assertSelectorExists('.caption');
        $this->assertSelectorExists('.thumbnail h4 a');
        $this->assertSame(1, $crawler->filter('.thumbnail button:contains("Supprimer")')->count());
        $this->assertSelectorExists('.glyphicon-ok');
        $this->assertSame(1, $crawler->filter('.thumbnail button:contains("Marquer non terminée")')->count());   
        $this->assertSelectorNotExists('.glyphicon-remove');   
        $this->assertSame(0, $crawler->filter('.thumbnail button:contains("Marquer comme faite")')->count());
    }

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

        $this->assertSame(1, $crawler->filter('a:contains("Se déconnecter")')->count());
        $this->assertSame(1, $crawler->filter('a:contains("Retour à la liste des tâches")')->count());
        $this->assertSelectorExists('form');
        $this->assertCount(2, $crawler->filter('input'));
        $this->assertCount(1, $crawler->filter('textarea'));
        $this->assertSame(1, $crawler->filter('html:contains("Title")')->count());
        $this->assertSame(1, $crawler->filter('html:contains("Content")')->count());
        $this->assertSame(1, $crawler->filter('button:contains("Ajouter")')->count());
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

        $this->assertResponseRedirects('/tasks/todo');
        $crawler = $this->client->followRedirect();
        $this->assertSame(1, $crawler->filter('div.alert.alert-success')->count());
        $this->assertSame(1, $crawler->filter('h4 a:contains("New Task")')->count());
        $this->assertSame(1, $crawler->filter('p:contains("New content")')->count());
    }

    /**
     * Test validity of edit task link
     *
     * @return void
     */
    public function testValidEditTaskLinkTasksPage()
    {
        $fixtures = $this->loadCustomFixtures();
        $this->login($this->client, $fixtures['user1']);
        $crawler = $this->client->request('GET', '/tasks/todo');
        $link = $crawler->selectLink('title1')->link();
        $crawler = $this->client->click($link);
        $this->assertSame(1, $crawler->filter('input[value="title1"]')->count());
    }

    /**
     * Test integration of task edition page for authenticated user
     *
     * @return void
     */
    public function testIntegrationTaskEditionPage()
    {
        $fixtures = $this->loadCustomFixtures();
        $this->login($this->client, $fixtures['user1']);
        $crawler = $this->client->request('GET', '/tasks/1/edit');

        $this->assertSame(1, $crawler->filter('a:contains("Se déconnecter")')->count());
        //$this->assertSelectorExists('a', 'Retour à la liste des tâches');
        $this->assertSelectorExists('form');
        $this->assertSame(1, $crawler->filter('label:contains("Title")')->count());
        $this->assertSame(1, $crawler->filter('label:contains("Content")')->count());
        $this->assertSame(1, $crawler->filter('input[value="title1"]')->count());
        $this->assertSame(1, $crawler->filter('textarea:contains("content1")')->count());
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

        $this->assertResponseRedirects('/tasks/todo');
        $crawler = $this->client->followRedirect();
        $this->assertSame(1, $crawler->filter('div.alert.alert-success')->count());
        $this->assertSame(1, $crawler->filter('h4 a:contains("updated title")')->count());
        $this->assertSame(1, $crawler->filter('p:contains("updated content")')->count());
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
        $this->assertResponseRedirects('/tasks/todo');
        $crawler = $this->client->followRedirect();
        $this->assertSame(1, $crawler->filter('div.alert.alert-success')->count());
        $this->assertSelectorNotExists('#task1');
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
        $this->assertResponseRedirects('/tasks/todo');

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
        $this->assertResponseRedirects('/tasks/todo');
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
