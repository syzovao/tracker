<?php

namespace Oro\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Oro\IssueBundle\Entity\IssueType;

class LoadIssueTypeData extends AbstractFixture implements FixtureInterface
{
    /**
     * @var array
     */
    protected $data = array(
        IssueType::TYPE_TASK => 'Task',
        IssueType::TYPE_SUBTASK => 'Sub-task',
        IssueType::TYPE_STORY => 'Story',
        IssueType::TYPE_BUG => 'Bug'
    );

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $i = 10;
        foreach ($this->data as $key => $value) {
            $issueType = new IssueType($key);
            $issueType
                ->setName($value)
                ->setPriority($i);
            $manager->persist($issueType);
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
        return 5;
    }
}
