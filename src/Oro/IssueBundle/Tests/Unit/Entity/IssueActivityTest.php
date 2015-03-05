<?php

namespace Oro\IssueBundle\Tests\Unit;

use Oro\IssueBundle\Entity\IssueActivity;

class IssueActivityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $property
     * @param string $value
     * @param string $expected
     * @dataProvider getSetDataProvider
     */
    public function testGetSet($property, $value, $expected)
    {
        $obj = new IssueActivity();

        call_user_func_array(array($obj, 'set' . ucfirst($property)), array($value));
        $this->assertEquals($expected, call_user_func_array(array($obj, 'get' . ucfirst($property)), array()));
    }

    public function getSetDataProvider()
    {
        $now = new \DateTime('now', new \DateTimeZone('UTC'));
        $issue = $this->getMock('Oro\Bundle\IssueBundle\Entity\Issue');
        $user = $this->getMock('Oro\Bundle\UserBundle\Entity\User');

        return array(
            'description' => array('description', 'Test Activity Description', 'Test Activity Description'),
            'createdAt'  => array('createdAt', $now, $now),
            'code'  => array('code', IssueActivity::ACTIVITY_ISSUE_COMMENTED, IssueActivity::ACTIVITY_ISSUE_COMMENTED),
            'user'  => array('user', $user, $user),
            'issue'  => array('issue', $issue, $issue)
        );
    }
}
