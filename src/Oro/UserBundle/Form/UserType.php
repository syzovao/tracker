<?php

namespace Oro\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityManager;

class UserType extends AbstractType
{
     private $em;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email')
            ->add('username', 'text')
            ->add('fullname', 'text')
            ->add('avatar_path', 'hidden');
        $builder->add('password', 'repeated', array(
            'type' => 'password',
            'invalid_message' => 'user.validators.password_match',
            'options' => array('attr' => array('class' => 'password-field')),
            'required' => true,
            'first_options'  => array('label' => 'Password'),
            'second_options' => array('label' => 'Repeat Password'),
        ));
        $choices = $this->getRolesChoices();
        $builder->add('role', 'choice', array(
            'label' => 'Role:',
            'choices' => $choices
        ));
        $builder->add('avatar_file', 'file', array('required' => false));

        $data = $builder->getData();
        if($data && $data->getId()){
            $builder->add('save', 'submit', array('label' => 'Submit'));
        } else {
            $builder->add('save', 'submit', array('label' => 'Create'));
        }
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oro\UserBundle\Entity\User',
            'attr' => array(
                'class' => 'form-horizontal-from-default'
            )
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'oro_userbundle_user';
    }

    /**
     * Ger roles choices array
     *
     * @return array
     */
    public function getRolesChoices()
    {
        $choices = array();
        $repository = $this->em->getRepository('OroUserBundle:Role');
        if(is_object($repository)) {
            $roles = $repository->findAll();
            foreach ($roles as $role) {
                $choices[$role->getRole()] = $role->getName();
            }
        }
        return $choices;
    }
}
