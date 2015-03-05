<?php
namespace Oro\ProjectBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProjectRepositoryTest extends KernelTestCase
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
        $projects = $this->em
                ->getRepository('OroProjectBundle:Project')
                ->findByProjectMember($user->getId());
        $this->assertGreaterThan(0, $projects);
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
