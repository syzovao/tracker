<?php

namespace Oro\UserBundle\Tests\Controller;

use Oro\TestBundle\Test\WebTestCase;

class SecuredControllerTest extends WebTestCase
{
    /**
     * Init client
     */
    protected function setUp()
    {
        $this->initClient();
    }

    public function testLogin()
    {
        $client = $this->client;
        $crawler = $client->request('GET', '/login');

        // check that the page is the right one
        $this->assertCount(1, $crawler->filter('html:contains("' . $this->getTrans('user.login_page_title') . '")'));

        // submits the login form
        $form = $crawler->selectButton($this->getTrans('buttons.login'))->form();
        // Fill in the form and submit it
        $form->setValues(array('_username' => 'admin','_password' => 'adminpass'));
        $client->submit($form);
        // redirect to the original page (but now authenticated)
        $crawler = $client->followRedirect();
        $crawler = $client->followRedirect();

        // check that the page is the right one
        $content = $client->getResponse()->getContent();
        $this->assertContains($this->getTrans('user.index_page_title'), $content);

        // click on the secure link
        $link = $crawler->selectLink('Home')->link();
        $crawler = $client->click($link);

        // check that the page is the right one
        $this->assertCount(1, $crawler->filter('html:contains("Welcome, admin!")'));
    }

    public function testLoginCheck()
    {
        $client = $this->client;
        $crawler = $client->request('GET', '/login_check');
    }

    public function testLogout()
    {
        $client = $this->client;
        $crawler = $client->request('GET', '/logout');
        $crawler = $client->followRedirect();
        $crawler = $client->followRedirect();
        $content = $client->getResponse()->getContent();
        $this->assertContains($this->getTrans('user.login_page_title'), $content);
    }
}
