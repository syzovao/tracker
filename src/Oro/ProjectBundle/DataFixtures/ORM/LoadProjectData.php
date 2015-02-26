<?php

namespace Oro\ProjectBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use Oro\ProjectBundle\Entity\Project;
use Oro\IssueBundle\Entity\Issue;
use Oro\IssueBundle\Entity\IssuePriority;
use Oro\IssueBundle\Entity\IssueStatus;
use Oro\IssueBundle\Entity\IssueResolution;
use Oro\IssueBundle\Entity\IssueType;


class LoadProjectData extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * @return array
     */
    public function getDependencies()
    {
        return array(
            'Oro\UserBundle\DataFixtures\ORM\LoadUserData'
        );
    }
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        /** @var \Oro\UserBundle\Entity\User $userAdmin */
        $userAdmin = $this->getReference('user-admin');
        /** @var \Oro\UserBundle\Entity\User $userManager */
        $userManager = $this->getReference('user-manager');
        /** @var \Oro\UserBundle\Entity\User $userOperator1 */
        $userOperator1 = $this->getReference('user-operator1');
        /** @var \Oro\UserBundle\Entity\User $userOperator2 */
        $userOperator2 = $this->getReference('user-operator2');

        $project1 = new Project();
        $project1
            ->setCode('PROJECT_1')
            ->setName('Project 1')
            ->setDescription('Project 1 Description')
            ->addUser($userAdmin)
            ->addUser($userManager)
            ->addUser($userOperator1);
        $manager->persist($project1);

        $project2 = new Project();
        $project2
            ->setCode('PROJECT_2')
            ->setName('Project 2')
            ->setDescription('Project 2 Description')
            ->addUser($userAdmin)
            ->addUser($userManager)
            ->addUser($userOperator2);
        $manager->persist($project2);

        $manager->flush();

        $this->addReference('project1', $project1);
        $this->addReference('project2', $project2);
    }

    /**
     * The order in which fixtures will be loaded
     * {@inheritDoc}
     *
     * @return int
     */
    public function getOrder()
    {
        return 30;
    }
}
