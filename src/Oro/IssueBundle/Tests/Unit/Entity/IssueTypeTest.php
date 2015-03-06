<?php

namespace Oro\IssueBundle\Tests\Unit\Entity;

use Oro\IssueBundle\Entity\IssueType;

class IssueTypeTest extends \PHPUnit_Framework_TestCase
{
    protected $obj;
    protected $code = 'TESTCODE';
    protected $name = 'TESTNAME';

    protected function setUp()
    {
        $this->obj = new IssueType($this->code);
    }

    public function testGetCode()
    {
        $this->assertTrue($this->obj->getCode() == $this->code);
    }

    public function testGetSetName()
    {
        $this->obj->setName($this->name);
        $this->assertTrue($this->obj->getName() == $this->name);
    }

    public function testGetSetPriority()
    {
        $priority = 10;
        $this->obj->setPriority($priority);
        $this->assertTrue($this->obj->getPriority() == $priority);
    }
}
