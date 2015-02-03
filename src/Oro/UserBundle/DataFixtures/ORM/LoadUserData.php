<?php

namespace Oro\UserBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Oro\UserBundle\Entity\User;
use Oro\UserBundle\Entity\Role;

class LoadUserData extends AbstractFixture implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $roleAdmin = new Role();
        $roleAdmin
            ->setRole('ROLE_ADMIN')
            ->setName('Administrator');
        $manager->persist($roleAdmin);

        $userAdmin = new User();
        $userAdmin
            ->setEmail('admin@tracker.com')
            ->setUsername('admin')
            ->setFullname('Bruce')
            ->setRoles($roleAdmin->getRole());
        $encoder = $this->container
            ->get('security.encoder_factory')
            ->getEncoder($userAdmin);
        $userAdmin->setPassword($encoder->encodePassword('adminpass', $userAdmin->getSalt()));
        $manager->persist($userAdmin);


        $roleManager = new Role();
        $roleManager
            ->setRole('ROLE_MANAGER')
            ->setName('Manager');
        $manager->persist($roleManager);

        $userManager = new User();
        $userManager
            ->setEmail('manager@tracker.com')
            ->setUsername('manager')
            ->setFullname('Barsik')
            ->setRole($roleManager->getRole());
        $encoder = $this->container
            ->get('security.encoder_factory')
            ->getEncoder($userManager);
        $userManager->setPassword($encoder->encodePassword('managerpass', $userManager->getSalt()));
        $manager->persist($userManager);

        $roleUser = new Role();
        $roleUser
            ->setRole('ROLE_USER')
            ->setName('Operator');
        $manager->persist($roleUser);

        $user = new User();
        $user
            ->setEmail('user@tracker.com')
            ->setUsername('user')
            ->setFullname('Plusha')
            ->setRole($roleUser->getRole());
        $encoder = $this->container
            ->get('security.encoder_factory')
            ->getEncoder($user);
        $user->setPassword($encoder->encodePassword('userpass', $user->getSalt()));
        $manager->persist($user);

        $manager->flush();
    }
}
