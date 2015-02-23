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
            ->setRole($roleAdmin->getRole());
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

        $user2 = new User();
        $user2
            ->setEmail('user2@tracker.com')
            ->setUsername('user2')
            ->setFullname('Plusha2')
            ->setRole($roleUser->getRole());
        $encoder2 = $this->container
            ->get('security.encoder_factory')
            ->getEncoder($user2);
        $user2->setPassword($encoder2->encodePassword('userpass2', $user2->getSalt()));
        $manager->persist($user2);

        $manager->flush();

        $this->addReference('user-admin', $userAdmin);
        $this->addReference('user-manager', $userManager);
        $this->addReference('user-operator1', $user);
        $this->addReference('user-operator2', $user2);
    }

    /**
     * The order in which fixtures will be loaded
     * {@inheritDoc}
     *
     * @return int
     */
    public function getOrder()
    {
        return 1;
    }
}
