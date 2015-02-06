<?php

namespace Oro\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Oro\IssueBundle\Entity\IssuePriority;

class LoadIssuePriorityData extends AbstractFixture implements FixtureInterface
{
    /**
     * @var array
     */
    protected $data = array(
        IssuePriority::PRIORITY_TRIVIAL => 'Trivial',
        IssuePriority::PRIORITY_MINOR => 'Minor',
        IssuePriority::PRIORITY_MAJOR => 'Major',
        IssuePriority::PRIORITY_CRITICAL => 'Critical',
        IssuePriority::PRIORITY_BLOCKER => 'Blocker'
    );

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $i = 10;
        foreach ($this->data as $key => $value) {
            $issuePriority = new IssuePriority($key);
            $issuePriority
                ->setName($value)
                ->setPriority($i);
            $manager->persist($issuePriority);
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
        return 10;
    }
}
