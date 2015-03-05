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

    public function findByProjectMember()
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
            ->getRepository('OroIssueBundle:IssueActivity')->findByParams(null, $user->getId());
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
