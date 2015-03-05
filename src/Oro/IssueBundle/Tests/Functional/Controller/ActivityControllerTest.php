<?php
namespace Oro\IssueBundle\Tests\Controller;

use Oro\TestBundle\Test\WebTestCase;

class ActivityControllerTest extends WebTestCase
{
    protected function setUp()
    {
        $this->initClient(array(), $this->generateBasicAuthHeader());
    }

    public function testIndex()
    {
        $client = $this->client;

        $crawler = $this->client->request('GET', $this->getUrl('oro_issue'));
        $content = $client->getResponse()->getContent();
        $this->assertContains($this->getTrans('issue.label_my_issues'), $content);
    }
}
