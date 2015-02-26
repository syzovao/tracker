<?php

namespace Oro\IssueBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

//use Oro\ProjectBundle\Entity\Project;
use Oro\IssueBundle\Entity\Issue;
use Oro\IssueBundle\Entity\IssuePriority;
use Oro\IssueBundle\Entity\IssueStatus;
use Oro\IssueBundle\Entity\IssueResolution;
use Oro\IssueBundle\Entity\IssueType;


class LoadIssueData extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * @return array
     */
    public function getDependencies()
    {
        return array(
            'Oro\UserBundle\DataFixtures\ORM\LoadUserData',
            'Oro\ProjectBundle\DataFixtures\ORM\LoadProjectData',
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

        $project1 = $this->getReference('project1');
        $project2 = $this->getReference('project2');

        /** @var \Oro\IssueBundle\Entity\Issue $issue1 */
        $issue1 = new Issue();
        $issue1
            ->setCode('ISSUE_1')
            ->setSummary('Issue 1 Summary')
            ->setDescription('Issue 1 Description')
            ->setCreatedAt()
            ->setUpdatedBy($userAdmin)
            ->setReporter($userAdmin)
            ->setAssignee($userAdmin)
            ->setIssuePriority(
                $manager->getRepository('OroIssueBundle:IssuePriority')
                    ->findOneByCode(IssuePriority::PRIORITY_MAJOR)
            )
            ->setIssueStatus(
                $manager->getRepository('OroIssueBundle:IssueStatus')
                    ->findOneByCode(IssueStatus::STATUS_OPEN)
            )
            ->setIssueType(
                $manager->getRepository('OroIssueBundle:IssueType')
                    ->findOneByCode(IssueType::TYPE_TASK)
            )
            ->setIssueResolution(
                $manager->getRepository('OroIssueBundle:IssueResolution')
                    ->findOneByCode(IssueResolution::RESOLUTION_INCOMPLETE)
            )
            ->setProject($project1);
        $manager->persist($issue1);


        $issue2 = new Issue();
        $issue2
            ->setCode('ISSUE_2')
            ->setSummary('Issue 2 Summary')
            ->setDescription('Issue 2 Description')
            ->setCreatedAt()
            ->setUpdatedBy($userAdmin)
            ->setReporter($userManager)
            ->setAssignee($userOperator1)
            ->setIssuePriority(
                $manager->getRepository('OroIssueBundle:IssuePriority')
                    ->findOneByCode(IssuePriority::PRIORITY_MAJOR)
            )
            ->setIssueStatus(
                $manager->getRepository('OroIssueBundle:IssueStatus')
                    ->findOneByCode(IssueStatus::STATUS_OPEN)
            )
            ->setIssueType(
                $manager->getRepository('OroIssueBundle:IssueType')
                    ->findOneByCode(IssueType::TYPE_STORY)
            )
            ->setIssueResolution(
                $manager->getRepository('OroIssueBundle:IssueResolution')
                    ->findOneByCode(IssueResolution::RESOLUTION_INCOMPLETE)
            )
            ->setProject($project1);
        $manager->persist($issue2);
        $manager->flush();

        $issue3 = new Issue();
        $issue3
            ->setCode('ISSUE_3')
            ->setSummary('Issue 3 Summary')
            ->setDescription('Issue 3 Description')
            ->setCreatedAt()
            ->setUpdatedBy($userAdmin)
            ->setReporter($userManager)
            ->setAssignee($userOperator1)
            ->setParent($issue2)
            ->setIssuePriority(
                $manager->getRepository('OroIssueBundle:IssuePriority')
                    ->findOneByCode(IssuePriority::PRIORITY_MAJOR)
            )
            ->setIssueStatus(
                $manager->getRepository('OroIssueBundle:IssueStatus')
                    ->findOneByCode(IssueStatus::STATUS_OPEN)
            )
            ->setIssueType(
                $manager->getRepository('OroIssueBundle:IssueType')
                    ->findOneByCode(IssueType::TYPE_SUBTASK)
            )
            ->setIssueResolution(
                $manager->getRepository('OroIssueBundle:IssueResolution')
                    ->findOneByCode(IssueResolution::RESOLUTION_INCOMPLETE)
            )
            ->setProject($project1);
        $manager->persist($issue3);

        $issue4 = new Issue();
        $issue4
            ->setCode('ISSUE_4')
            ->setSummary('Issue 4 Summary')
            ->setDescription('Issue 4 Description')
            ->setCreatedAt()
            ->setUpdatedBy($userAdmin)
            ->setReporter($userManager)
            ->setAssignee($userOperator2)
            ->setIssuePriority(
                $manager->getRepository('OroIssueBundle:IssuePriority')
                    ->findOneByCode(IssuePriority::PRIORITY_MAJOR)
            )
            ->setIssueStatus(
                $manager->getRepository('OroIssueBundle:IssueStatus')
                    ->findOneByCode(IssueStatus::STATUS_OPEN)
            )
            ->setIssueType(
                $manager->getRepository('OroIssueBundle:IssueType')
                    ->findOneByCode(IssueType::TYPE_TASK)
            )
            ->setIssueResolution(
                $manager->getRepository('OroIssueBundle:IssueResolution')
                    ->findOneByCode(IssueResolution::RESOLUTION_INCOMPLETE)
            )
            ->setProject($project2);
        $manager->persist($issue4);

        $manager->flush();

        $this->addReference('issue1', $issue1);
        $this->addReference('issue2', $issue2);
        $this->addReference('issue3', $issue3);
        $this->addReference('issue4', $issue4);
    }

    /**
     * The order in which fixtures will be loaded
     * {@inheritDoc}
     *
     * @return int
     */
    public function getOrder()
    {
        return 40;
    }
}
