<?php

namespace Oro\IssueBundle\Tests\Controller;

use Oro\TestBundle\Test\WebTestCase;

class CommentControllerTest extends WebTestCase
{
    const TEST_COMMENT = 'TestComment1';
    const TEST_COMMENT_UPDATED = 'TestComment1 updated';

    protected function setUp()
    {
        $this->initClient(array(), $this->generateBasicAuthHeader());
    }

    public function testIndex()
    {
        // Create a new client to browse the application
        $client = $this->client;

        $crawler = $client->request('GET', $this->getUrl('oro_issue'));
        $content = $client->getResponse()->getContent();
        $this->assertContains($this->getTrans('issue.label_my_issues'), $content);

        // click on the secure link
        $link = $crawler->selectLink($this->getTrans('buttons.show'))->link();
        $crawler = $client->click($link);

        $content = $client->getResponse()->getContent();
        $this->assertContains('ISSUE_1', $content);
        $this->assertContains($this->getTrans('issue.comment.comment_new_title'), $content);

        $url = $client->getHistory()->current()->getUri();
        $issueId = $this->getIdFromUrl($url);
        $this->assertNotNull($issueId);
        return $issueId;
    }

    /**
     * @param int $issueId
     * @depends testIndex
     * @return mixed
     */
    public function testCreate($issueId)
    {
        // Create a new client to browse the application
        $client = $this->client;
        $crawler = $client->request('GET', $this->getUrl('oro_issue_view', array('id' => $issueId)));
        $content = $client->getResponse()->getContent();
        $this->assertContains($this->getTrans('issue.comment.comment_new_title'), $content);

        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form(array(
            'oro_issuebundle_oro_comment[content]'  => self::TEST_COMMENT,
        ));
        $client->submit($form);
        $client->followRedirect();

        // Check data in the show view
        $content = $client->getResponse()->getContent();
        $this->assertContains(self::TEST_COMMENT, $content);

        // click on the secure link
        $link = $crawler->filter('a.comment_edit')->last()->link();
        $crawler = $client->click($link);

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

        $crawler = $client->request('GET', $this->getUrl('oro_comment_update', array('id' => $id)));
        $content = $client->getResponse()->getContent();

        // Fill in the form and submit it
        $form = $crawler->selectButton('Update')->form(array(
            'oro_issuebundle_oro_comment[content]'  => self::TEST_COMMENT_UPDATED,
        ));
        $client->submit($form);
        $client->followRedirect();

        // Check data in the show view
        $content = $client->getResponse()->getContent();
        $this->assertContains(self::TEST_COMMENT_UPDATED, $content);
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
        $crawler = $client->request('GET', $this->getUrl('oro_comment_delete', array('id' => $id)));
        $content = $client->getResponse()->getContent();
        $client->followRedirect();
        $content = $client->getResponse()->getContent();
        $this->assertNotContains(self::TEST_COMMENT_UPDATED, $content);
    }
}
