<?php

namespace Oro\IssueBundle\Tests\Unit;

use Oro\IssueBundle\Entity\Issue;
use Oro\UserBundle\Entity\User;

use Doctrine\Common\Collections\ArrayCollection;

class IssueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $property
     * @param string $value
     * @param string $expected
     * @dataProvider getSetDataProvider
     */
    public function testGetSet($property, $value, $expected)
    {
        $obj = new Issue();

        call_user_func_array(array($obj, 'set' . ucfirst($property)), array($value));
        $this->assertEquals($expected, call_user_func_array(array($obj, 'get' . ucfirst($property)), array()));
    }

    public function getSetDataProvider()
    {
        $user = $this->getMock('Oro\Bundle\UserBundle\Entity\User');
        $now = new \DateTime('now', new \DateTimeZone('UTC'));
        $issueType = $this->getMock('Oro\Bundle\IssueBundle\Entity\IssueType');
        $issuePriority = $this->getMock('Oro\Bundle\IssueBundle\Entity\IssuePriority');
        $issueResolution = $this->getMock('Oro\Bundle\IssueBundle\Entity\IssueResolution');
        $issueStatus = $this->getMock('Oro\Bundle\IssueBundle\Entity\IssueStatus');
        $project = $this->getMock('Oro\Bundle\ProjectBundle\Entity\Project');

        return array(
            'code'        => array('code', 'TEST_ISSUE', 'TEST_ISSUE'),
            'summary'     => array('summary', 'Test Issue summary', 'Test Issue summary'),
            'description' => array('description', 'Test Issue description', 'Test Issue description'),
            'createdAt'       => array('createdAt', $now, $now),
            'updatedAt'       => array('updatedAt', $now, $now),
            'issueType'       => array('issueType', $issueType, $issueType),
            'issuePriority'   => array('issuePriority', $issuePriority, $issuePriority),
            'issueStatus'     => array('issueStatus', $issueStatus, $issueStatus),
            'issueResolution' => array('issueResolution', $issueResolution, $issueResolution),
            'assignee'        => array('assignee', $user, $user),
            'project'         => array('project', $project, $project)
        );
    }


    public function testAddRemoveCollaborator()
    {
        $obj = new Issue();
        $user = new User();
        $obj->addCollaborator($user);
        $this->assertTrue($obj->getCollaborators()->contains($user));

        $obj->removeCollaborator($user);
        $this->assertFalse($obj->getCollaborators()->contains($user));
    }
}
