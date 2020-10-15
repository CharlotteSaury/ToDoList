<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Welcome to Symfony', $crawler->filter('#container h1')->text());
    }

    
    /**
     * Test access to homepage for authenticated user
     *
     * @return void
     */
    /*public function testHomepageAuthenticated()
    {
        $fixtures = $this->loadCustomFixtures();
        $this->login($this->client, $fixtures['user1']);
        $this->client->request('GET', '/');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('button', 'Se déconnecter');
    }*/
}
