<?php

namespace Oro\IssueBundle\Tests\Controller;

use Oro\TestBundle\Test\WebTestCase;

class IssueControllerTest extends WebTestCase
{
    const PROJECT_NAME = 'Project 1';
    const ISSUE_CODE = 'TEST_ISSUE_1';
    const ISSUE_CODE_NEW = 'TEST_ISSUE_1_1';
    const ISSUE_DESCRIPTION = 'Test Issue 1 Description';
    const ISSUE_DESCRIPTION_NEW = 'Test Issue 1 Description - Changed';
    const ISSUE_COMMENT = 'Test Comment 1 to Issue 1';

    protected function setUp()
    {
        $this->initClient(array(), $this->generateBasicAuthHeader());
    }

    public function testIndex()
    {
        // Create a new client to browse the application
        $client = $this->client;

        $crawler = $client->request('GET', $this->getUrl('oro_issue'));
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code for GET /issue/"
        );

        $content = $client->getResponse()->getContent();
        $this->assertContains($this->getTrans('buttons.create'), $content);
        $this->assertContains($this->getTrans('issue.label_my_issues'), $content);
        $this->assertCount(
            1,
            $crawler->filter('h1.page-header:contains("' . $this->getTrans('issue.label_issues') . '")')
        );
    }

    public function testView()
    {
        // Create a new client to browse the application
        $client = $this->client;
        $crawler = $client->request('GET', $this->getUrl('oro_issue'));

        $content = $client->getResponse()->getContent();
        $this->assertContains($this->getTrans('buttons.show'), $content);
        $this->assertCount(1, $crawler->filter('body:contains("' . $this->getTrans('issue.label_my_issues') . '")'));

        // click on the secure link
        $link = $crawler->selectLink($this->getTrans('buttons.show'))->link();
        $crawler = $client->click($link);
        $content = $client->getResponse()->getContent();
        $this->assertContains('ISSUE_1', $content);
        $this->assertContains($this->getTrans('issue.issue_title'), $content);

        // click on the secure link
        $link = $crawler->selectLink($this->getTrans('buttons.edit'))->link();
        $crawler = $client->click($link);

        $content = $client->getResponse()->getContent();
        $this->assertContains('ISSUE_1', $content);
        $this->assertCount(
            1,
            $crawler->filter('h1.page-header:contains("' . $this->getTrans('issue.edit_title') . '")')
        );
    }

    public function testCreate()
    {
        // Create a new client to browse the application
        $client = $this->client;
        $crawler = $client->request('GET', $this->getUrl('oro_issue_create'));

        // Fill in the form and submit it
        $form = $crawler->selectButton($this->getTrans('Create'))->form();
        $form->setValues(array(
            'oro_issuebundle_issue[code]' => self::ISSUE_CODE,
            'oro_issuebundle_issue[summary]' => self::ISSUE_DESCRIPTION,
            'oro_issuebundle_issue[description]' => self::ISSUE_DESCRIPTION,
            'oro_issuebundle_issue[issueType]' => 'task',
            'oro_issuebundle_issue[issuePriority]' => 'trivial',
            'oro_issuebundle_issue[issueStatus]' => 'open',
            'oro_issuebundle_issue[issueResolution]' => 'incomplete',
        ));
        $form['oro_issuebundle_issue[assignee]']->select(
            $crawler->filter('#oro_issuebundle_issue_assignee option:contains("admin")')->attr('value')
        );
        $form['oro_issuebundle_issue[project]']->select(
            $crawler
                ->filter('#oro_issuebundle_issue_project option:contains("' . self::PROJECT_NAME . '")')
                ->attr('value')
        );
        $client->submit($form);

        $content = $client->getResponse()->getContent();
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();

        // Check data in the show view
        $content = $client->getResponse()->getContent();
        $this->assertContains(self::ISSUE_CODE, $content);
        $this->assertContains(self::ISSUE_DESCRIPTION, $content);

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
        $crawler = $client->request('GET', $this->getUrl('oro_issue_update', array('id' => $id)));

        $form = $crawler->selectButton($this->getTrans('Update'))->form();

        // Fill in the form and submit it
        $form->setValues(array(
            'oro_issuebundle_issue[code]' => self::ISSUE_CODE_NEW,
            'oro_issuebundle_issue[description]' => self::ISSUE_DESCRIPTION_NEW,
        ));
        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $content = $client->getResponse()->getContent();
        $this->assertContains(self::ISSUE_CODE_NEW, $content);
        $this->assertContains(self::ISSUE_DESCRIPTION_NEW, $content);

        //check in list
        $this->client->request('GET', $this->getUrl('oro_issue'));
        $this->assertContains(self::ISSUE_CODE_NEW, $client->getResponse()->getContent());

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

        $crawler = $client->request('GET', $this->getUrl('oro_issue_update', array('id' => $id)));
        $form = $crawler->selectButton($this->getTrans('buttons.delete'))->form();
        $client->submit($form);
        $client->followRedirect();

        $content = $client->getResponse()->getContent();
        $this->assertNotContains(self::ISSUE_CODE_NEW, $content);
        $this->assertNotContains(self::ISSUE_DESCRIPTION_NEW, $content);
    }
}
