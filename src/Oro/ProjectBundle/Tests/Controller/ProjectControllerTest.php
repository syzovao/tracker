<?php

namespace Oro\ProjectBundle\Tests\Controller;

use Oro\TestBundle\Test\WebTestCase;

class ProjectControllerTest extends WebTestCase
{
    const PROJECT_NAME = 'Test Project 1';
    const PROJECT_NAME_NEW = 'Test Project 2';
    const PROJECT_DESCRIPTION = 'Test Project 1 DESCRIPTION';
    const PROJECT_DESCRIPTION_NEW = 'Test Project 2 DESCRIPTION';
    const PROJECT_CODE = 'TEST_PROJECT_1';
    const PROJECT_CODE_NEW = 'TEST_PROJECT_2';

    protected function setUp()
    {
        $this->initClient(array(), $this->generateBasicAuthHeader());
    }

    public function testIndex()
    {
        // Create a new client to browse the application
        $client = $this->client;

        $crawler = $client->request('GET', $this->getUrl('oro_project'));
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code for GET /project/"
        );

        $content = $client->getResponse()->getContent();
        $this->assertContains($this->getTrans('buttons.view'), $content);
        $this->assertCount(
            1,
            $crawler->filter('h1.page-header:contains("' . $this->getTrans('project.project_list_header') . '")')
        );
    }

    public function testView()
    {
        // Create a new client to browse the application
        $client = $this->client;
        $crawler = $client->request('GET', $this->getUrl('oro_project'));

        $content = $client->getResponse()->getContent();
        $this->assertContains($this->getTrans('buttons.view'), $content);
        $this->assertCount(
            1,
            $crawler->filter('h1.page-header:contains("' . $this->getTrans('project.label_project') . '")')
        );

        // click on the secure link
        $link = $crawler->selectLink($this->getTrans('buttons.view'))->link();
        $crawler = $client->click($link);
        $content = $client->getResponse()->getContent();
        $this->assertContains('PROJECT_1', $content);

        // click on the secure link
        $link = $crawler->selectLink($this->getTrans('buttons.edit'))->link();
        $crawler = $client->click($link);

        $content = $client->getResponse()->getContent();
        $this->assertContains('PROJECT_1', $content);
        $this->assertCount(
            1,
            $crawler->filter('h1.page-header:contains("' . $this->getTrans('project.project_edit_header') . '")')
        );
    }

    public function testCreate()
    {
        // Create a new client to browse the application
        $client = $this->client;
        $crawler = $client->request('GET', $this->getUrl('oro_project_create'));

        $form = $crawler->selectButton($this->getTrans('Create'))->form();
        // Fill in the form and submit it
        $form->setValues(array(
            'oro_projectbundle_project[code]' => self::PROJECT_CODE,
            'oro_projectbundle_project[name]' => self::PROJECT_NAME,
            'oro_projectbundle_project[description]' => self::PROJECT_DESCRIPTION,
        ));
        $form['oro_projectbundle_project[users]'][0]->tick();
        $client->submit($form);

        $content = $client->getResponse()->getContent();
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();

        // Check data in the show view
        $content = $client->getResponse()->getContent();
        $this->assertContains(self::PROJECT_CODE, $content);
        $this->assertContains(self::PROJECT_NAME, $content);

        $url = $client->getHistory()->current()->getUri();
        $id = $this->getIdFromUrl($url);
        $this->assertNotNull($id);

        return $id;
    }

    /**
     * @param int $id
     * @depends testCreate
     * @return mixed
     */
    public function testUpdate($id)
    {
        // Create a new client to browse the application
        $client = $this->client;
        $crawler = $client->request('GET', $this->getUrl('oro_project_update', array('id' => $id)));

        $form = $crawler->selectButton($this->getTrans('Update'))->form();

        // Fill in the form and submit it
        $form->setValues(array(
            'oro_projectbundle_project[code]' => self::PROJECT_CODE_NEW,
            'oro_projectbundle_project[name]' => self::PROJECT_NAME_NEW,
            'oro_projectbundle_project[description]' => self::PROJECT_DESCRIPTION_NEW,
        ));
        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $content = $client->getResponse()->getContent();
        $this->assertContains(self::PROJECT_CODE_NEW, $content);
        $this->assertContains(self::PROJECT_NAME_NEW, $content);

        //check in list
        $this->client->request('GET', $this->getUrl('oro_project'));
        $this->assertContains(self::PROJECT_CODE_NEW, $client->getResponse()->getContent());

        return $id;
    }

    /**
     * Delete test project by id
     *
     * @param int $id
     * @depends testUpdate
     * @return int
     */
    public function testDelete($id)
    {
        // Create a new client to browse the application
        $client = $this->client;

        $crawler = $client->request('GET', $this->getUrl('oro_project_update', array('id' => $id)));
        $form = $crawler->selectButton($this->getTrans('buttons.delete'))->form();
        $client->submit($form);
        $client->followRedirect();

        $content = $client->getResponse()->getContent();
        $this->assertNotContains(self::PROJECT_NAME_NEW, $content);
        $this->assertNotContains(self::PROJECT_CODE_NEW, $content);
    }

}
