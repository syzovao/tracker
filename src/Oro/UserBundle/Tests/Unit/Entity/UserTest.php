<?php

namespace Oro\UserBundle\Tests\Unit;

use Oro\UserBundle\Entity\User;

use Doctrine\Common\Collections\ArrayCollection;

class UserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $property
     * @param string $value
     * @param string $expected
     * @dataProvider getSetDataProvider
     */
    public function testGetSet($property, $value, $expected)
    {
        $obj = new User();

        call_user_func_array(array($obj, 'set' . ucfirst($property)), array($value));
        $this->assertEquals($expected, call_user_func_array(array($obj, 'get' . ucfirst($property)), array()));
    }

    public function getSetDataProvider()
    {
        $project = $this->getMock('Oro\ProjectBundle\Entity\Project');

        return array(
            'username'   => array('username', 'Test username', 'Test username'),
            'fullname'   => array('fullname', 'Test fullname', 'Test fullname'),
            'email'      => array('email', 'Test email', 'Test email'),
            'password'   => array('password', 'qa123123', 'qa123123'),
            'role'       => array('role', 'ROLE_MANAGER', 'ROLE_MANAGER'),
            'avatarpath' => array('avatarpath', 'avatar_path', 'avatar_path'),
            'project'    => array('projects', new ArrayCollection(array($project)), new ArrayCollection(array($project)))
        );
    }
}
