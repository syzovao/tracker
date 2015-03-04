<?php

namespace Oro\UserBundle\Tests\Controller;

use Oro\TestBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    /**
     * Init client
     */
    protected function setUp()
    {
        $this->initClient(array(), $this->generateBasicAuthHeader());
    }

    public function testDashboard()
    {
        // Create a new client to browse the application
        $client = $this->client;
        $url = $this->getUrl('dashboard');
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertContains($this->getTrans('user.homepage_title'), $content);
        $this->assertContains($this->getTrans('user.label_user'), $content);
        $this->assertContains($this->getTrans('user.label_my_issues'), $content);
        $this->assertContains($this->getTrans('user.label_my_activities'), $content);
    }
}
