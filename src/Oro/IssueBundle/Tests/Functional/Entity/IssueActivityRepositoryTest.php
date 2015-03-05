<?php
namespace Oro\IssueBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class IssueActivityRepositoryTest extends KernelTestCase
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

    public function testFindByUserCollaborator()
    {
        $user = $this->em->getRepository('OroUserBundle:User')->findOneByEmail('admin@tracker.com');
        $issues = $this->em
                ->getRepository('OroIssueBundle:Issue')->findByUserCollaborator($user->getId());
        $this->assertGreaterThan(0, $issues);
    }

    public function testFindByUser()
    {
        $user = $this->em->getRepository('OroUserBundle:User')->findOneByEmail('admin@tracker.com');
        $issues = $this->em
            ->getRepository('OroIssueBundle:Issue')->findByUser($user->getId());
        $this->assertGreaterThan(0, $issues);
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
