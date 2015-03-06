<?php
namespace Oro\IssueBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class IssueRepositoryTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testFindByProjectMember()
    {
        $user = $this->em->getRepository('OroUserBundle:User')->findOneByEmail('admin@tracker.com');
        $activities = $this->em
                ->getRepository('OroIssueBundle:IssueActivity')->findByProjectMember($user->getId());
        $this->assertGreaterThan(0, $activities);
    }

    public function testFindByParams()
    {
        $user = $this->em->getRepository('OroUserBundle:User')->findOneByEmail('admin@tracker.com');
        $activities = $this->em
            ->getRepository('OroIssueBundle:IssueActivity')->findByParams(null, null, $user->getId());
        $this->assertGreaterThan(0, $activities);

        /** @var \Doctrine\Common\Collections\ArrayCollection $projects */
        $projects = $user->getProjects();
        /** @var \Oro\ProjectBundle\Entity\Project $project */
        $project = $projects->first();
        $projectId = $projects->first()->getId();
        /** @var \Oro\IssueBundle\Entity\Issue $issue */
        $issue = $project->getIssues()->first();

        $activities = $this->em
            ->getRepository('OroIssueBundle:IssueActivity')->findByParams($projectId, null, null);
        $this->assertGreaterThan(0, $activities);

        $activities = $this->em
            ->getRepository('OroIssueBundle:IssueActivity')->findByParams(null, $issue->getId(), null);
        $this->assertGreaterThan(0, $activities);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

}
