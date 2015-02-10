<?php
namespace Oro\IssueBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Oro\IssueBundle\Entity\Issue;
use Oro\UserBundle\Entity\User;

class IssueListener
{
    /**
     * Populates identities for stored references
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        //$entityManager = $args->getEntityManager();

        if ($entity instanceof Issue) {
            //add assignee as collaborator
            $entity->addCollaborator($entity->getAssignee());
        }
    }
}
