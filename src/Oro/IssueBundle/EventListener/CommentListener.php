<?php
namespace Oro\IssueBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Oro\IssueBundle\Entity\Issue;
use Oro\IssueBundle\Entity\IssueComment;


class CommentListener
{
    /**
     * Populates identities for stored references
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $issue = $entity->getIssue();
        $em = $args->getEntityManager();

        if ($entity instanceof IssueComment && $issue instanceof Issue) {
            //add commentator as collaborator
            $issue->addCollaborator($entity->getUser());
            $em->persist($entity);
            $em->flush();
        }
    }
}
