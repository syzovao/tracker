<?php
/**
 * Created by PhpStorm.
 * User: dnk-comp-003
 * Date: 02.03.2015
 * Time: 10:38
 */

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
