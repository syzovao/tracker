<?php

namespace Oro\ProjectBundle\Tests\Unit;

use Oro\ProjectBundle\Form\ProjectType;

class ProjectTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ProjectType
     */
    protected $type;

    protected function setUp()
    {
        $this->type = new ProjectType();
    }

    public function testSetDefaultOptions()
    {
        $resolver = $this->getMock('Symfony\Component\OptionsResolver\OptionsResolverInterface');
        $resolver->expects($this->once())
            ->method('setDefaults')
            ->with($this->isType('array'));
        $this->type->setDefaultOptions($resolver);
    }

    public function testGetName()
    {
        $this->assertEquals('oro_projectbundle_project', $this->type->getName());
    }

    public function testBuildForm()
    {
        $expectedFields = array(
            'code'        => 'text',
            'name'        => 'text',
            'description' => 'textarea',
            'users'       => 'entity'
        );

        $builder = $this->getMockBuilder('Symfony\Component\Form\FormBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $counter = 0;
        foreach ($expectedFields as $fieldName => $formType) {
            $builder->expects($this->at($counter))
                ->method('add')
                ->with($fieldName, $formType)
                ->will($this->returnSelf());
            $counter++;
        }

        $this->type->buildForm($builder, array());
    }
}
