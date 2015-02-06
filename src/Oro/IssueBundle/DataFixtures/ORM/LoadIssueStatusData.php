<?php

namespace Oro\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Oro\IssueBundle\Entity\IssueStatus;

class LoadIssueStatusData extends AbstractFixture implements FixtureInterface
{
    /**
     * @var array
     */
    protected $data = array(
        IssueStatus::STATUS_OPEN => 'Open',
        IssueStatus::STATUS_INPROGRESS => 'In progress',
        IssueStatus::STATUS_RESOLVED => 'Resolved',
        IssueStatus::STATUS_CLOSED => 'Closed',
        IssueStatus::STATUS_REOPENED => 'Reopened'
    );

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $i = 10;
        foreach ($this->data as $key => $value) {
            $issueStatus = new IssueStatus($key);
            $issueStatus
                ->setName($value)
                ->setPriority($i);
            $manager->persist($issueStatus);
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
        return 15;
    }
}
