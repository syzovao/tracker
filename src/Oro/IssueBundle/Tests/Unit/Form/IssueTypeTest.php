<?php

namespace Oro\IssueBundle\Tests\Unit;

use Oro\IssueBundle\Form\IssueType;

class IssueTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IssueType
     */
    protected $type;

    protected function setUp()
    {
        $currentUser = $this->getMockBuilder('Oro\UserBundle\Entity\User')
            ->disableOriginalConstructor()->getMock();
        $this->type = new IssueType($currentUser);
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
        $this->assertEquals('oro_issuebundle_issue', $this->type->getName());
    }

    public function testBuildForm()
    {
        $expectedFields = array(
            'code'            => 'text',
            'summary'         => 'text',
            'description'     => 'textarea',
            'issueType'       => 'entity',
            'issuePriority'   => 'entity',
            'issueStatus'     => 'entity',
            'issueResolution' => 'entity',
            'assignee'        => 'entity',
            'parent'          => 'entity',
            'project'         => 'entity'
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
