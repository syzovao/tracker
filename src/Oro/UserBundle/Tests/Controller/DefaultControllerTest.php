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

    public function testMenu()
    {
        $this->initClient(array(), $this->generateBasicAuthHeader('user', 'userpass'));
        $client = $this->client;
        $url = $this->getUrl('dashboard');
        $crawler = $client->request('GET', $url);

        $content = $client->getResponse()->getContent();
        $this->assertNotContains($this->getTrans('menu.projects_create'), $content);

        $link = $crawler->selectLink($this->getTrans('menu.home'))->link();
        $crawler = $client->click($link);
        $this->assertCount(1, $crawler->filter('html:contains("Welcome, user!")'));

        $link = $crawler->selectLink($this->getTrans('menu.users_profile'))->link();
        $crawler = $client->click($link);
        $this->assertCount(1, $crawler->filter('html:contains("user@tracker.com")'));
        $link = $crawler->selectLink($this->getTrans('navigation.edit_my_profile'))->link();
        $crawler = $client->click($link);
        $content = $client->getResponse()->getContent();
        $this->assertContains($this->getTrans('user.update_page_title'), $content);

        $link = $crawler->selectLink($this->getTrans('menu.projects_list'))->link();
        $crawler = $client->click($link);
        $content = $client->getResponse()->getContent();
        $this->assertContains('PROJECT_1', $content);
        $this->assertNotContains('PROJECT_2', $content);
        $link = $crawler->filter('a.btn')->last()->link();
        $crawler = $client->click($link);
        $content = $client->getResponse()->getContent();
        $this->assertContains('ISSUE_2', $content);

        $link = $crawler->selectLink($this->getTrans('menu.issues_list'))->link();
        $crawler = $client->click($link);
        $content = $client->getResponse()->getContent();
        $this->assertContains('ISSUE_2', $content);
        $this->assertNotContains('ISSUE_1', $content);
        $link = $crawler->filter('a.actions_edit')->last()->link();
        $crawler = $client->click($link);
        $content = $client->getResponse()->getContent();
        $this->assertContains($this->getTrans('issue.edit_title'), $content);
    }
}
