<?php

namespace Oro\IssueBundle\Tests\Unit;

use Oro\IssueBundle\Form\IssueCommentType;

class IssueCommentTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IssueType
     */
    protected $type;

    protected function setUp()
    {
        $this->type = new IssueCommentType();
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
        $this->assertEquals('oro_issuebundle_oro_comment', $this->type->getName());
    }

    public function testBuildForm()
    {
        $expectedFields = array(
            'content' => 'textarea',
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
