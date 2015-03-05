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
        $this->assertTrue($this->object->getCode() == $this->code);
    }

    public function testGetSetName()
    {
        $this->object->setName($this->name);
        $this->assertTrue($this->object->getName() == $this->name);
    }

    public function testGetSetPriority()
    {
        $priority = 10;
        $this->object->setPriority($priority);
        $this->assertTrue($this->object->getPriority() == $priority);
    }
}
