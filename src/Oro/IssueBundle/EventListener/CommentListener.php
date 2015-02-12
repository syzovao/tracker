<?php
namespace Oro\IssueBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Oro\IssueBundle\Entity\Issue;
use Oro\IssueBundle\Entity\IssueComment;
use Oro\IssueBundle\Entity\IssueActivity;


class CommentListener
{
    /**
     * Populates identities for stored references
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        /** @var IssueComment $entity */
        $entity = $args->getEntity();
        if ($entity instanceof IssueComment) {
            /** @var Issue $issue */
            $issue = $entity->getIssue();
            /** @var \Doctrine\ORM\EntityManager $em */
            $em = $args->getEntityManager();

            //add commentator as collaborator
            $issue->addCollaborator($entity->getUser());
            $em->persist($issue);
            $em->flush();

            //create activity
            $activity = new IssueActivity();
            $activity
                ->setCode(IssueActivity::ACTIVITY_ISSUE_COMMENTED)
                ->setIssue($issue)
                ->setUser($entity->getUser())
                ->setDescription('Comment: ' . $entity->getContent());
            $em->persist($activity);
            $em->flush($activity);
        }
    }
}
