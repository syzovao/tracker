<?php

namespace Oro\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Oro\IssueBundle\Entity\IssueResolution;

class LoadIssueResolutionData extends AbstractFixture implements FixtureInterface
{
    /**
     * @var array
     */
    protected $data = array(
        IssueResolution::RESOLUTION_UNRESOLVED => 'Unresolved',
        IssueResolution::RESOLUTION_DUPLICATE => 'Duplicate',
        IssueResolution::RESOLUTION_WONTFIX => 'Resolved',
        IssueResolution::RESOLUTION_FIXED => 'Won\'t fix',
        IssueResolution::RESOLUTION_DONE => 'Done'
    );

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $i = 10;
        foreach ($this->data as $key => $value) {
            $issueResolution = new IssueResolution($key);
            $issueResolution
                ->setName($value)
                ->setPriority($i);
            $manager->persist($issueResolution);
            $i += 10;
        }
        $manager->flush();
    }

    /**
     * The order in which fixtures will be loaded
     * {@inheritDoc}
     *
     * @return int
     */
    public function getOrder()
    {
        return 20;
    }
}
