<?php

namespace Oro\IssueBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;


class IssueType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code')
            ->add('summary')
            ->add('description', 'textarea')
            ->add('issueType', 'entity', array(
                'property_path' => 'issueType',
                'class' => 'OroIssueBundle:IssueType',
                'property' => 'name',
                'multiple' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('i')->orderBy('i.priority', 'ASC');
                },
                'attr' => array('class'=>'form-control')
            ))
            ->add('issuePriority', 'entity', array(
                'property_path' => 'issuePriority',
                'class' => 'OroIssueBundle:IssuePriority',
                'property' => 'name',
                'multiple' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('i')->orderBy('i.priority', 'ASC');
                },
                'attr' => array('class'=>'form-control')
            ))
            ->add('issueStatus', 'entity', array(
                'property_path' => 'issueStatus',
                'class' => 'OroIssueBundle:IssueStatus',
                'property' => 'name',
                'multiple' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('i')->orderBy('i.priority', 'ASC');
                },
                'attr' => array('class'=>'form-control')
            ))
            ->add('issueResolution', 'entity', array(
                'property_path' => 'issueResolution',
                'class' => 'OroIssueBundle:IssueResolution',
                'property' => 'name',
                'multiple' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('i')->orderBy('i.priority', 'ASC');
                },
                'attr' => array('class'=>'form-control')
            ))
            ->add('assignee', 'entity', array(
                'property_path' => 'assignee',
                'class' => 'OroUserBundle:User',
                'property' => 'username',
                'multiple' => false,
                'attr' => array('class'=>'form-control')
            ));
        $builder->add('parent');
        $builder->add('project', 'entity', array(
                'required' => true,
                'property_path' => 'project',
                'class' => 'OroProjectBundle:Project',
                'property' => 'name',
                'multiple' => false,
                'attr' => array('class'=>'form-control')
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oro\IssueBundle\Entity\Issue'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'oro_issuebundle_issue';
    }
}
