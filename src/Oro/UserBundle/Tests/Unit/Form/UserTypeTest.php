<?php

namespace Oro\UserBundle\Tests\Unit;

use Oro\UserBundle\Form\UserType;

class UserTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UserType
     */
    protected $type;

    protected function setUp()
    {
        //mock the EntityManager to return the mock of the repository
        $entityManager =
            $this->getMockBuilder('Doctrine\ORM\EntityManager')
                ->disableOriginalConstructor()
                ->setMethods(['getRepository', 'findAll'])
                ->getMock();
        $this->type = new UserType($entityManager);
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
        $this->assertEquals('oro_userbundle_user', $this->type->getName());
    }

    public function testBuildForm()
    {
        $expectedFields = array(
            'email'       => 'email',
            'username'    => 'text',
            'fullname'    => 'text',
            'avatar_path' => 'hidden',
            'password'    => 'repeated'
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

        $builder->expects($this->once())
            ->method('getData')
            ->will($this->returnValue(false));

        $this->type->buildForm($builder, array());
    }
}
