<?php

namespace Oro\IssueBundle\Tests\Unit;

use Oro\IssueBundle\Entity\IssueComment;

class IssueCommentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $property
     * @param string $value
     * @param string $expected
     * @dataProvider getSetDataProvider
     */
    public function testGetSet($property, $value, $expected)
    {
        $obj = new IssueComment();

        call_user_func_array(array($obj, 'set' . ucfirst($property)), array($value));
        $this->assertEquals($expected, call_user_func_array(array($obj, 'get' . ucfirst($property)), array()));
    }

    public function getSetDataProvider()
    {
        $issue = $this->getMock('Oro\Bundle\IssueBundle\Entity\Issue');
        $user = $this->getMock('Oro\Bundle\UserBundle\Entity\User');

        return array(
            'content'   => array('content', 'Test Issue comment', 'Test Issue comment'),
            'user'      => array('user', $user, $user),
            'issue'     => array('issue', $issue, $issue)
        );
    }
}
