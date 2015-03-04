<?php

namespace Oro\UserBundle\Tests\Controller;

use Oro\TestBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    protected function setUp()
    {
        $this->initClient(array(), $this->generateBasicAuthHeader());
    }

    public function testIndex()
    {
        $client = $this->client;

        $crawler = $client->request('GET', $this->getUrl('oro_user_index'));
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code for GET /user/"
        );

        $content = $client->getResponse()->getContent();
        $this->assertContains($this->getTrans('navigation.view_user_profile'), $content);
        $this->assertCount(
            1,
            $crawler->filter('h1.page-header:contains("' . $this->getTrans('user.index_page_title') . '")')
        );
    }

    public function testView()
    {
        $client = $this->client;
        $crawler = $client->request('GET', $this->getUrl('oro_user_index'));

        $content = $client->getResponse()->getContent();
        $this->assertContains($this->getTrans('navigation.view_user_profile'), $content);

        // click on the secure link
        $link = $crawler->selectLink($this->getTrans('navigation.view_user_profile'))->link();
        $crawler = $client->click($link);
        $content = $client->getResponse()->getContent();
        $this->assertContains('admin@tracker.com', $content);

        // click on the secure link
        $link = $crawler->selectLink($this->getTrans('navigation.edit_my_profile'))->link();
        $crawler = $client->click($link);
        $this->assertContains('admin@tracker.com', $client->getResponse()->getContent());
        $this->assertCount(
            1,
            $crawler->filter('h1.page-header:contains("' . $this->getTrans('user.update_page_title') . '")')
        );
    }

    public function testCreate()
    {
        $client = $this->client;
        $crawler = $client->request('GET', $this->getUrl('oro_user_create'));

        $form = $crawler->selectButton($this->getTrans('Create'))->form();
        // Fill in the form and submit it
        $form->setValues(array(
            'oro_userbundle_user[email]' => 'test_user3@tracker.com',
            'oro_userbundle_user[username]' => 'test_user3',
            'oro_userbundle_user[fullname]' => 'Bruce 3',
            'oro_userbundle_user[role]' => 'ROLE_USER',
            'oro_userbundle_user[password][first]' => 'qa123123',
            'oro_userbundle_user[password][second]' => 'qa123123',

        ));
        $client->submit($form);
        $content = $client->getResponse()->getContent();
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();

        // Check data in the show view
        $content = $client->getResponse()->getContent();
        $this->assertContains('test_user3@tracker.com', $content);

        $url = $client->getHistory()->current()->getUri();
        $id = $this->getIdFromUrl($url);
        $this->assertNotNull($id);

        return $id;
    }

    /**
     * @param int $id
     * @depends testCreate
     */
    public function testUpdate($id)
    {
        $client = $this->client;
        $crawler = $client->request('GET', $this->getUrl('oro_user_update', array('id' => $id)));

        $form = $crawler->selectButton($this->getTrans('Submit'))->form();
        // Fill in the form and submit it
        $form->setValues(array(
            'oro_userbundle_user[fullname]' => 'Bruce 4',
            'oro_userbundle_user[email]' => 'test_user4@tracker.com',
            'oro_userbundle_user[password][first]' => 'qa123123',
            'oro_userbundle_user[password][second]' => 'qa123123',
        ));
        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $content = $client->getResponse()->getContent();
        $this->assertContains('Bruce 4', $content);
        $this->assertContains('test_user4@tracker.com', $content);

        //check in users list
        $this->client->request('GET', $this->getUrl('oro_user_index'));
        $this->assertContains('test_user4@tracker.com', $client->getResponse()->getContent());

        $this->removeTestEntity('test_user4@tracker.com');
    }

    /**
     * Delete user by email
     * @param $email
     */
    protected function removeTestEntity($email)
    {
        $container = $this->getContainer();
        $em = $container->get('doctrine')->getManager();
        $repository = $container->get('doctrine')->getRepository('OroUserBundle:User');
        $testUser = $repository->findOneByEmail($email);
        $em->remove($testUser);
        $em->flush();
    }
}
