<?php

namespace Oro\UserBundle\Tests\Unit;

use Oro\UserBundle\Entity\Role;

class RoleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $property
     * @param string $value
     * @param string $expected
     * @dataProvider getSetDataProvider
     */
    public function testGetSet($property, $value, $expected)
    {
        $obj = new Role();

        call_user_func_array(array($obj, 'set' . ucfirst($property)), array($value));
        $this->assertEquals($expected, call_user_func_array(array($obj, 'get' . ucfirst($property)), array()));
    }

    /**
     * @return array
     */
    public function getSetDataProvider()
    {
        return array(
            'role' => array('role', 'ROLE_MANAGER', 'ROLE_MANAGER'),
            'name' => array('name', 'Test Role manager', 'Test Role manager'),
        );
    }
}
